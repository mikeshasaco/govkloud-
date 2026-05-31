<?php

namespace App\Services\LabRuntime;

use App\Models\LabSession;
use App\Services\K8s\K8sClient;
use App\Services\K8s\HelmClient;
use App\Services\K8s\IngressUrlBuilder;
use Illuminate\Support\Facades\Log;
use Exception;

class SessionProvisioner
{
  public function __construct(
    protected K8sClient $k8sClient,
    protected HelmClient $helmClient,
    protected IngressUrlBuilder $ingressUrlBuilder,
  ) {
  }

  /**
   * Provision a complete lab session environment
   */
  public function provision(LabSession $session): bool
  {
    $lab = $session->lab;
    $namespace = $session->host_namespace;
    $namespaceAlreadyExists = $this->k8sClient->namespaceExists($namespace);

    try {
      // Step 1: Create host namespace (idempotent)
      if ($namespaceAlreadyExists) {
        Log::info("Namespace already exists, reusing: {$namespace}");
      } else {
        Log::info("Creating namespace: {$namespace}");
        if (!$this->k8sClient->createNamespace($namespace)) {
          throw new Exception("Failed to create namespace: {$namespace}");
        }

        // Step 1b: Copy TLS certificate into new namespace
        Log::info("Copying TLS secret to: {$namespace}");
        if (!$this->k8sClient->copySecret('govkloud-tls', 'default', $namespace)) {
          Log::warning("Failed to copy TLS secret - ingress will use default cert");
        }

        // Step 2: Apply ResourceQuota and LimitRange to new namespace
        Log::info("Applying resource quotas to: {$namespace}");
        $this->applyResourceGuardrails($session);
      }

      // Step 3: Install vcluster (helm upgrade --install is idempotent)
      Log::info("Installing/upgrading vcluster: {$session->vcluster_release_name}");
      if (!$this->installVcluster($session)) {
        throw new Exception("Failed to install vcluster");
      }

      // Step 4: Wait for vcluster to be ready
      Log::info("Waiting for vcluster to be ready");
      if (!$this->waitForVcluster($session)) {
        throw new Exception("vcluster did not become ready in time");
      }

      // Step 5: Get vcluster kubeconfig and store as secret
      Log::info("Extracting vcluster kubeconfig");
      if (!$this->storeVclusterKubeconfig($session)) {
        throw new Exception("Failed to store vcluster kubeconfig");
      }

      // Step 6: Install workbench (helm upgrade --install is idempotent)
      Log::info("Installing/upgrading workbench: {$session->workbench_release_name}");
      if (!$this->installWorkbench($session)) {
        throw new Exception("Failed to install workbench");
      }

      // Step 7: Create ingress for workbench (kubectl apply is idempotent)
      Log::info("Creating/updating ingress for session: {$session->id}");
      if (!$this->createWorkbenchIngress($session)) {
        throw new Exception("Failed to create workbench ingress");
      }

      // Step 7.5: Wait for ingress to be ready (skip if namespace already existed — ingress is likely active)
      $user = $session->user;
      $codeUrl = $this->ingressUrlBuilder->buildWorkbenchUrl($user->username);
      if (!$namespaceAlreadyExists) {
        $this->waitForIngressReady($codeUrl, $session->id);
      } else {
        Log::info("Skipping ingress wait — namespace pre-existed, ingress should be active");
      }

      // Step 8: Update session with code_url and status
      $session->update([
        'code_url' => $codeUrl,
        'status' => LabSession::STATUS_RUNNING,
        'last_activity_at' => now(),
      ]);

      Log::info("Session provisioned successfully", [
        'session_id' => $session->id,
        'code_url' => $codeUrl,
        'reused_namespace' => $namespaceAlreadyExists,
      ]);

      return true;

    } catch (Exception $e) {
      Log::error("Session provisioning failed", [
        'session_id' => $session->id,
        'error' => $e->getMessage(),
      ]);

      $session->markError($e->getMessage());

      // Best effort cleanup
      $this->cleanupOnError($session);

      return false;
    }
  }

  /**
   * Wait until the ingress route is actually serving traffic.
   * There's a delay between creating the K8s Ingress resource and
   * the nginx-ingress controller configuring the route.
   */
  protected function waitForIngressReady(string $url, string $sessionId): void
  {
    $maxAttempts = 30;  // 30 × 2s = 60s max
    $delaySeconds = 2;

    Log::info("Waiting for ingress readiness", [
      'session_id' => $sessionId,
      'url' => $url,
    ]);

    $streamContext = stream_context_create([
      'http' => [
        'method' => 'HEAD',
        'timeout' => 5,
        'ignore_errors' => true,
      ],
      'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
      ],
    ]);

    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
      try {
        $headers = @get_headers($url, false, $streamContext);

        if ($headers && isset($headers[0])) {
          $statusLine = $headers[0];  // e.g. "HTTP/1.1 200 OK"
          $statusCode = (int) substr($statusLine, 9, 3);

          if ($statusCode !== 404) {
            Log::info("Ingress ready after {$attempt} attempts", [
              'session_id' => $sessionId,
              'status_code' => $statusCode,
            ]);
            return;
          }
        }
      } catch (\Throwable $e) {
        // Ignore errors, keep trying
      }

      if ($attempt < $maxAttempts) {
        sleep($delaySeconds);
      }
    }

    Log::warning("Ingress readiness timeout — proceeding anyway", [
      'session_id' => $sessionId,
      'url' => $url,
      'attempts' => $maxAttempts,
    ]);
  }

  /**
   * Apply ResourceQuota and LimitRange to namespace
   * Note: Quota must accommodate both vcluster (~500m/512Mi) AND workbench resources
   */
  protected function applyResourceGuardrails(LabSession $session): void
  {
    $namespace = $session->host_namespace;

    // Quota limits are set higher to accommodate vcluster + workbench + overhead
    // Do NOT use workbench limits here - those are just for the workbench pod
    $quotaLimits = [
      'cpu' => config('govkloud.resources.namespace_cpu_quota', '2'),
      'memory' => config('govkloud.resources.namespace_memory_quota', '4Gi'),
      'storage' => config('govkloud.resources.default_storage_limit', '10Gi'),
    ];

    $quotaYaml = $this->k8sClient->generateResourceQuotaYaml('session-quota', $quotaLimits);
    $this->k8sClient->applyYaml($namespace, $quotaYaml);

    $limitRangeYaml = $this->k8sClient->generateLimitRangeYaml('session-limits');
    $this->k8sClient->applyYaml($namespace, $limitRangeYaml);
  }

  /**
   * Install vcluster into the host namespace
   */
  protected function installVcluster(LabSession $session): bool
  {
    // Ensure vcluster repo is added
    $this->helmClient->repoAddIfMissing(
      'vcluster',
      config('govkloud.helm.vcluster_repo')
    );

    // Build values YAML with init.manifests for RBAC bootstrapping
    // vcluster applies init.manifests inside the virtual cluster at startup,
    // so RBAC is enforced before any user can interact with it.
    $valuesYaml = $this->generateVclusterValues();

    return $this->helmClient->upgradeInstallWithValuesFileNoWait(
      $session->vcluster_release_name,
      config('govkloud.helm.vcluster_chart'),
      $session->host_namespace,
      $valuesYaml
    );
  }

  /**
   * Generate vcluster Helm values YAML including security hardening
   * and init.manifests for RBAC bootstrapping inside the vcluster
   */
  protected function generateVclusterValues(): string
  {
    return <<<'YAML'
controlPlane:
  statefulSet:
    resources:
      limits:
        memory: "512Mi"
        cpu: "500m"

sync:
  fromHost:
    nodes:
      enabled: false
    storageClasses:
      enabled: false
    ingressClasses:
      enabled: false

# Bootstrap RBAC inside the vcluster at startup.
# This creates a restricted service account that has access to
# everything EXCEPT nodes — hiding real AKS infrastructure names.
experimental:
  deploy:
    vcluster:
      manifests: |-
        apiVersion: v1
        kind: ServiceAccount
        metadata:
          name: lab-user
          namespace: default
        ---
        apiVersion: v1
        kind: Secret
        metadata:
          name: lab-user-token
          namespace: default
          annotations:
            kubernetes.io/service-account.name: lab-user
        type: kubernetes.io/service-account-token
        ---
        apiVersion: rbac.authorization.k8s.io/v1
        kind: ClusterRole
        metadata:
          name: lab-user-role
        rules:
        - apiGroups: [""]
          resources:
          - pods
          - pods/log
          - pods/exec
          - pods/portforward
          - services
          - endpoints
          - configmaps
          - secrets
          - persistentvolumeclaims
          - serviceaccounts
          - events
          - replicationcontrollers
          - resourcequotas
          - limitranges
          verbs: ["get", "list", "watch", "create", "update", "patch", "delete"]
        - apiGroups: ["apps"]
          resources: ["deployments", "replicasets", "statefulsets", "daemonsets"]
          verbs: ["get", "list", "watch", "create", "update", "patch", "delete"]
        - apiGroups: ["batch"]
          resources: ["jobs", "cronjobs"]
          verbs: ["get", "list", "watch", "create", "update", "patch", "delete"]
        - apiGroups: ["networking.k8s.io"]
          resources: ["ingresses", "networkpolicies"]
          verbs: ["get", "list", "watch", "create", "update", "patch", "delete"]
        - apiGroups: ["autoscaling"]
          resources: ["horizontalpodautoscalers"]
          verbs: ["get", "list", "watch", "create", "update", "patch", "delete"]
        - apiGroups: ["policy"]
          resources: ["poddisruptionbudgets"]
          verbs: ["get", "list", "watch", "create", "update", "patch", "delete"]
        - apiGroups: ["rbac.authorization.k8s.io"]
          resources: ["roles", "rolebindings"]
          verbs: ["get", "list", "watch", "create", "update", "patch", "delete"]
        - apiGroups: [""]
          resources: ["namespaces"]
          verbs: ["get", "list", "watch", "create", "update", "patch", "delete"]
        - apiGroups: [""]
          resources: ["persistentvolumes"]
          verbs: ["get", "list", "watch"]
        - apiGroups: ["storage.k8s.io"]
          resources: ["storageclasses"]
          verbs: ["get", "list", "watch"]
        - apiGroups: ["rbac.authorization.k8s.io"]
          resources: ["clusterroles", "clusterrolebindings"]
          verbs: ["get", "list", "watch"]
        - apiGroups: ["apiextensions.k8s.io"]
          resources: ["customresourcedefinitions"]
          verbs: ["get", "list", "watch"]
        ---
        apiVersion: rbac.authorization.k8s.io/v1
        kind: ClusterRoleBinding
        metadata:
          name: lab-user-binding
        roleRef:
          apiGroup: rbac.authorization.k8s.io
          kind: ClusterRole
          name: lab-user-role
        subjects:
        - kind: ServiceAccount
          name: lab-user
          namespace: default
YAML;
  }

  /**
   * Wait for vcluster StatefulSet to be ready
   */
  protected function waitForVcluster(LabSession $session): bool
  {
    // vcluster v0.21+ creates a statefulset named after the release
    // 10 minute timeout for init container to complete
    return $this->k8sClient->waitForStatefulSetReady(
      $session->host_namespace,
      $session->vcluster_release_name,
      600
    );
  }

  /**
   * Get vcluster kubeconfig and store as K8s secret
   * Uses kubectl to extract from vcluster's auto-created secret instead of
   * the vcluster CLI (which hangs in non-interactive exec contexts).
   * See: docs/vcluster-kubeconfig-extraction.yaml
   */
  protected function storeVclusterKubeconfig(LabSession $session): bool
  {
    $kubeconfigPath = config('govkloud.host_k8s.kubeconfig_path');
    $kubectlPath = config('govkloud.kubectl.binary_path');
    $namespace = $session->host_namespace;
    $releaseName = $session->vcluster_release_name;

    // The vcluster creates a secret named "vc-{release}" containing
    // the admin kubeconfig (key: "config", base64-encoded)
    $secretName = "vc-{$releaseName}";

    // Wait for the secret to be created (vcluster needs a few seconds after starting)
    $maxWait = 120; // seconds
    $waited = 0;
    $kubeconfigData = null;

    while ($waited < $maxWait) {
      $command = sprintf(
        '%s get secret %s -n %s --kubeconfig %s -o jsonpath={.data.config} 2>&1',
        escapeshellarg($kubectlPath),
        escapeshellarg($secretName),
        escapeshellarg($namespace),
        escapeshellarg($kubeconfigPath)
      );

      $output = [];
      $returnCode = 0;
      exec($command, $output, $returnCode);

      $result = implode("\n", $output);

      if ($returnCode === 0 && !empty($result) && !str_contains($result, 'NotFound')) {
        $kubeconfigData = base64_decode($result);
        break;
      }

      Log::info("Waiting for vcluster kubeconfig secret...", [
        'secret' => $secretName,
        'namespace' => $namespace,
        'waited' => $waited,
      ]);

      sleep(5);
      $waited += 5;
    }

    if (empty($kubeconfigData)) {
      Log::error("Failed to extract vcluster kubeconfig from secret", [
        'secret' => $secretName,
        'namespace' => $namespace,
      ]);
      return false;
    }

    // Rewrite the server URL from localhost:8443 to the vcluster's ClusterIP service
    // The vcluster service is accessible at: https://{release}.{namespace}:443
    $vclusterServiceUrl = "https://{$releaseName}.{$namespace}:443";
    $kubeconfigData = str_replace(
      'https://localhost:8443',
      $vclusterServiceUrl,
      $kubeconfigData
    );

    Log::info("Extracted and rewrote vcluster kubeconfig", [
      'server_url' => $vclusterServiceUrl,
    ]);

    // Try to extract lab-user token from inside the vcluster via kubectl exec.
    // init.manifests creates the lab-user SA + token at vcluster startup.
    // We exec into the vcluster pod because the App Service can't reach
    // the vcluster API via K8s internal DNS.
    $restrictedKubeconfig = $this->buildLabUserKubeconfig(
      $session, $kubeconfigData, $vclusterServiceUrl
    );

    if ($restrictedKubeconfig) {
      Log::info("Using restricted lab-user kubeconfig (nodes hidden)");
      $kubeconfigData = $restrictedKubeconfig;
    } else {
      Log::warning("Falling back to admin kubeconfig — lab-user token not yet available");
    }

    // Store as a new secret for the workbench pod to mount
    return $this->k8sClient->createSecretFromFile(
      $namespace,
      'vcluster-kubeconfig',
      'config',
      $kubeconfigData
    );
  }

  /**
   * Extract the lab-user service account token from inside the vcluster
   * and build a restricted kubeconfig that hides nodes from users.
   *
   * Uses a short-lived K8s Job that runs inside the cluster — it can
   * reach the vcluster API via internal DNS, unlike the App Service.
   * The Job mounts the admin kubeconfig, extracts the lab-user token,
   * and writes the restricted kubeconfig to a new secret.
   *
   * Returns null if the token extraction fails.
   */
  protected function buildLabUserKubeconfig(
    LabSession $session,
    string $adminKubeconfig,
    string $vclusterServiceUrl
  ): ?string {
    $namespace = $session->host_namespace;
    $jobName = 'extract-lab-token';

    // The Job script:
    // 1. Wait for the lab-user-token secret inside the vcluster
    // 2. Extract the token and CA cert
    // 3. Build a restricted kubeconfig
    // 4. Create a K8s secret with the restricted kubeconfig
    $script = <<<'SCRIPT'
#!/bin/sh
set -e

export KUBECONFIG=/admin-kc/config

# Wait for lab-user-token to be populated (init.manifests creates it)
MAX_WAIT=60
WAITED=0
while [ $WAITED -lt $MAX_WAIT ]; do
  TOKEN_B64=$(kubectl get secret lab-user-token -n default -o jsonpath='{.data.token}' 2>/dev/null || echo "")
  if [ -n "$TOKEN_B64" ]; then
    break
  fi
  echo "Waiting for lab-user-token... ($WAITED s)"
  sleep 3
  WAITED=$((WAITED + 3))
done

if [ -z "$TOKEN_B64" ]; then
  echo "ERROR: Timed out waiting for lab-user-token"
  exit 1
fi

# Decode the token
TOKEN=$(echo "$TOKEN_B64" | base64 -d)

# Get the CA cert (base64-encoded)
CA_B64=$(kubectl get secret lab-user-token -n default -o jsonpath='{.data.ca\.crt}')

# Get the server URL from current kubeconfig
SERVER=$(kubectl config view --minify -o jsonpath='{.clusters[0].cluster.server}')

# Build the restricted kubeconfig
cat > /tmp/restricted-kubeconfig << EOF
apiVersion: v1
kind: Config
clusters:
- cluster:
    certificate-authority-data: ${CA_B64}
    server: ${SERVER}
  name: vcluster
contexts:
- context:
    cluster: vcluster
    user: lab-user
    namespace: default
  name: lab-user@vcluster
current-context: lab-user@vcluster
users:
- name: lab-user
  user:
    token: ${TOKEN}
EOF

echo "Restricted kubeconfig built successfully"
cat /tmp/restricted-kubeconfig
SCRIPT;

    // Create a ConfigMap with the script
    $scriptConfigMapYaml = sprintf(
      "apiVersion: v1\nkind: ConfigMap\nmetadata:\n  name: %s-script\ndata:\n  extract.sh: |\n%s",
      $jobName,
      implode("\n", array_map(fn($line) => "    {$line}", explode("\n", $script)))
    );

    $this->k8sClient->applyYaml($namespace, $scriptConfigMapYaml);

    // Create the Job that runs inside the cluster
    $jobYaml = <<<YAML
apiVersion: batch/v1
kind: Job
metadata:
  name: {$jobName}
spec:
  ttlSecondsAfterFinished: 30
  backoffLimit: 0
  template:
    spec:
      restartPolicy: Never
      containers:
      - name: extract
        image: bitnami/kubectl:latest
        command: ["/bin/sh", "/scripts/extract.sh"]
        volumeMounts:
        - name: admin-kubeconfig
          mountPath: /admin-kc
          readOnly: true
        - name: script
          mountPath: /scripts
          readOnly: true
      volumes:
      - name: admin-kubeconfig
        secret:
          secretName: vcluster-kubeconfig
      - name: script
        configMap:
          name: {$jobName}-script
          defaultMode: 0755
YAML;

    // Delete any existing job first
    $this->k8sClient->runCommand(['delete', 'job', $jobName, '-n', $namespace, '--ignore-not-found']);
    $this->k8sClient->applyYaml($namespace, $jobYaml);

    // Wait for the Job to complete
    $maxWait = 90;
    $waited = 0;
    $jobOutput = null;

    while ($waited < $maxWait) {
      $result = $this->k8sClient->runCommand([
        'get', 'job', $jobName, '-n', $namespace,
        '-o', 'jsonpath={.status.succeeded}'
      ]);

      if ($result['success'] && trim($result['output']) === '1') {
        // Job completed — get the output (the restricted kubeconfig)
        $logsResult = $this->k8sClient->runCommand([
          'logs', "job/{$jobName}", '-n', $namespace
        ]);

        if ($logsResult['success']) {
          $jobOutput = $logsResult['output'];
        }
        break;
      }

      // Check if the Job failed
      $failResult = $this->k8sClient->runCommand([
        'get', 'job', $jobName, '-n', $namespace,
        '-o', 'jsonpath={.status.failed}'
      ]);
      if ($failResult['success'] && trim($failResult['output']) === '1') {
        $logsResult = $this->k8sClient->runCommand([
          'logs', "job/{$jobName}", '-n', $namespace
        ]);
        Log::error("Token extraction job failed", [
          'output' => $logsResult['output'] ?? 'no logs',
        ]);
        break;
      }

      sleep(3);
      $waited += 3;
    }

    // Cleanup
    $this->k8sClient->runCommand(['delete', 'job', $jobName, '-n', $namespace, '--ignore-not-found']);
    $this->k8sClient->runCommand(['delete', 'configmap', "{$jobName}-script", '-n', $namespace, '--ignore-not-found']);

    if (empty($jobOutput)) {
      return null;
    }

    // Extract just the kubeconfig YAML from the Job output
    // (skip the log lines, find the YAML block)
    if (preg_match('/^(apiVersion: v1\nkind: Config\n.+)$/ms', $jobOutput, $matches)) {
      return $matches[1];
    }

    return null;
  }


  /**
   * Install workbench (code-server) deployment
   */
  protected function installWorkbench(LabSession $session): bool
  {
    $lab = $session->lab;
    $workbenchRelease = 'workbench-' . substr($session->id, 0, 8);

    $session->update(['workbench_release_name' => $workbenchRelease]);

    $valuesYaml = $this->generateWorkbenchValues($session);

    // If using a local chart
    $chartPath = config('govkloud.helm.workbench_chart_path');

    if (file_exists($chartPath)) {
      return $this->helmClient->installWithValuesFile(
        $workbenchRelease,
        $chartPath,
        $session->host_namespace,
        $valuesYaml
      );
    }

    // Fallback: create deployment directly via kubectl
    return $this->createWorkbenchDirectly($session);
  }

  /**
   * Generate workbench Helm values
   */
  protected function generateWorkbenchValues(LabSession $session): string
  {
    $lab = $session->lab;
    $limits = $lab->getResourceLimits();

    // Parse image into repository and tag (handle "repo:tag" or just "repo")
    $imageParts = explode(':', $lab->workbench_image, 2);
    $repository = $imageParts[0];
    $tag = $imageParts[1] ?? 'latest';

    return <<<YAML
# Session configuration
session:
  id: "{$session->id}"
  token: "{$session->session_token}"

image:
  repository: {$repository}
  tag: "{$tag}"

# Disable password auth - users are already authenticated via GovKloud
args:
  - "--auth"
  - "none"

service:
  port: 8080

resources:
  limits:
    cpu: "{$limits['cpu']}"
    memory: "{$limits['memory']}"
  requests:
    cpu: "100m"
    memory: "256Mi"

kubeconfig:
  secretName: vcluster-kubeconfig
  secretKey: config
  mountPath: /home/coder/.kube

workspace:
  mountPath: /workspace
  sizeLimit: "10Gi"
YAML;
  }

  /**
   * Create workbench deployment directly (fallback if no Helm chart)
   */
  protected function createWorkbenchDirectly(LabSession $session): bool
  {
    $lab = $session->lab;
    $limits = $lab->getResourceLimits();
    $namespace = $session->host_namespace;
    $name = 'workbench';

    $yaml = <<<YAML
apiVersion: apps/v1
kind: Deployment
metadata:
  name: {$name}
spec:
  replicas: 1
  selector:
    matchLabels:
      app: workbench
      session: {$session->id}
  template:
    metadata:
      labels:
        app: workbench
        session: {$session->id}
    spec:
      containers:
      - name: code-server
        image: {$lab->workbench_image}
        args: ["--auth", "none"]
        ports:
        - containerPort: 8080
        resources:
          limits:
            cpu: "{$limits['cpu']}"
            memory: "{$limits['memory']}"
          requests:
            cpu: "100m"
            memory: "256Mi"
        volumeMounts:
        - name: kubeconfig
          mountPath: /home/coder/.kube
          readOnly: true
        - name: workspace
          mountPath: /workspace
      volumes:
      - name: kubeconfig
        secret:
          secretName: vcluster-kubeconfig
      - name: workspace
        emptyDir: {}
---
apiVersion: v1
kind: Service
metadata:
  name: workbench
spec:
  selector:
    app: workbench
    session: {$session->id}
  ports:
  - port: 8080
    targetPort: 8080
YAML;

    if (!$this->k8sClient->applyYaml($namespace, $yaml)) {
      return false;
    }

    return $this->k8sClient->waitForDeploymentReady($namespace, $name, 180);
  }

  /**
   * Create ingress for workbench access
   */
  protected function createWorkbenchIngress(LabSession $session): bool
  {
    // Determine the correct service name based on deployment method
    $chartPath = config('govkloud.helm.workbench_chart_path');
    if (file_exists($chartPath)) {
      // Helm chart creates a service named {releaseName}-govkloud-workbench
      $serviceName = $session->workbench_release_name . '-govkloud-workbench';
    } else {
      // Direct kubectl deployment creates a service named 'workbench'
      $serviceName = 'workbench';
    }

    // Use the user's username for stable ingress paths (persists across sessions)
    $user = $session->user;
    $pathIdentifier = $user->username;

    $ingressYaml = $this->ingressUrlBuilder->generateIngressYaml(
      'workbench-ingress',
      $pathIdentifier,
      $serviceName,
      8080
    );

    return $this->k8sClient->applyYaml($session->host_namespace, $ingressYaml);
  }

  /**
   * Best effort cleanup on provisioning failure
   */
  protected function cleanupOnError(LabSession $session): void
  {
    // With persistent user namespaces, do NOT delete on error.
    // The namespace may have resources from previous sessions.
    Log::info("Preserving persistent namespace after provisioning error", [
      'namespace' => $session->host_namespace,
      'session_id' => $session->id,
    ]);
  }
}
