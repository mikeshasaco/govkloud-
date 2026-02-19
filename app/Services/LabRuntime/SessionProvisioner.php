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

    try {
      // Step 1: Create host namespace
      Log::info("Creating namespace: {$namespace}");
      if (!$this->k8sClient->createNamespace($namespace)) {
        throw new Exception("Failed to create namespace: {$namespace}");
      }

      // Step 2: Apply ResourceQuota and LimitRange
      Log::info("Applying resource quotas to: {$namespace}");
      $this->applyResourceGuardrails($session);

      // Step 3: Install vcluster
      Log::info("Installing vcluster: {$session->vcluster_release_name}");
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

      // Step 6: Install workbench
      Log::info("Installing workbench: {$session->workbench_release_name}");
      if (!$this->installWorkbench($session)) {
        throw new Exception("Failed to install workbench");
      }

      // Step 7: Create ingress for workbench
      Log::info("Creating ingress for session: {$session->id}");
      if (!$this->createWorkbenchIngress($session)) {
        throw new Exception("Failed to create workbench ingress");
      }

      // Step 8: Update session with code_url and status
      $codeUrl = $this->ingressUrlBuilder->buildWorkbenchUrl($session->id);
      $session->update([
        'code_url' => $codeUrl,
        'status' => LabSession::STATUS_RUNNING,
        'last_activity_at' => now(),
      ]);

      Log::info("Session provisioned successfully", [
        'session_id' => $session->id,
        'code_url' => $codeUrl,
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

    // vcluster v0.21+ uses new values format
    // Use upgradeInstallNoWait to avoid blocking, we poll for readiness separately
    $values = [
      'controlPlane.statefulSet.resources.limits.memory' => '512Mi',
      'controlPlane.statefulSet.resources.limits.cpu' => '500m',
    ];

    return $this->helmClient->upgradeInstallNoWait(
      $session->vcluster_release_name,
      config('govkloud.helm.vcluster_chart'),
      $session->host_namespace,
      $values
    );
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
    $namespace = $session->host_namespace;
    $releaseName = $session->vcluster_release_name;

    // The vcluster creates a secret named "vc-vc-{release}" containing
    // the admin kubeconfig (key: "config", base64-encoded)
    $secretName = "vc-{$releaseName}";

    // Wait for the secret to be created (vcluster needs a few seconds after starting)
    $maxWait = 120; // seconds
    $waited = 0;
    $kubeconfigData = null;

    while ($waited < $maxWait) {
      $command = sprintf(
        '%s get secret %s -n %s --kubeconfig %s -o jsonpath={.data.config} 2>&1',
        escapeshellarg(config('govkloud.kubectl.binary_path')),
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

    // Store as a new secret for the workbench pod to mount
    return $this->k8sClient->createSecretFromFile(
      $namespace,
      'vcluster-kubeconfig',
      'config',
      $kubeconfigData
    );
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
# Session configuration - password is set via session.token
session:
  id: "{$session->id}"
  token: "{$session->session_token}"

image:
  repository: {$repository}
  tag: "{$tag}"

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
        ports:
        - containerPort: 8080
        env:
        - name: PASSWORD
          value: "{$session->session_token}"
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
    // The Helm chart creates a service named {releaseName}-govkloud-workbench
    $serviceName = $session->workbench_release_name . '-govkloud-workbench';

    $ingressYaml = $this->ingressUrlBuilder->generateIngressYaml(
      'workbench-ingress',
      $session->id,
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
    try {
      $this->k8sClient->deleteNamespace($session->host_namespace);
    } catch (Exception $e) {
      Log::warning("Cleanup failed", ['error' => $e->getMessage()]);
    }
  }
}
