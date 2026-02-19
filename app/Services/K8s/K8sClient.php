<?php

namespace App\Services\K8s;

use Illuminate\Support\Facades\Log;
use RuntimeException;

class K8sClient
{
    protected string $kubectlPath;
    protected ?string $kubeconfigPath;

    public function __construct()
    {
        $this->kubectlPath = config('govkloud.kubectl.binary_path');
        $this->kubeconfigPath = config('govkloud.host_k8s.kubeconfig_path');
    }

    /**
     * Create a namespace (idempotent - returns true if already exists)
     */
    public function createNamespace(string $name): bool
    {
        // First check if namespace already exists
        if ($this->namespaceExists($name)) {
            return true;
        }

        $result = $this->runCommand(['create', 'namespace', $name]);

        // Also return true if it already exists (race condition)
        if (!$result['success'] && str_contains($result['output'], 'AlreadyExists')) {
            return true;
        }

        return $result['success'];
    }

    /**
     * Delete a namespace
     */
    public function deleteNamespace(string $name): bool
    {
        $result = $this->runCommand(['delete', 'namespace', $name, '--ignore-not-found']);
        return $result['success'];
    }

    /**
     * Apply YAML content to a namespace
     */
    public function applyYaml(string $namespace, string $yamlContent): bool
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'k8s_yaml_');
        file_put_contents($tempFile, $yamlContent);

        try {
            $result = $this->runCommand(['apply', '-n', $namespace, '-f', $tempFile]);
            return $result['success'];
        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Wait for a deployment to be ready
     */
    public function waitForDeploymentReady(string $namespace, string $deploymentName, int $timeoutSeconds = 300): bool
    {
        $result = $this->runCommand([
            'rollout',
            'status',
            "deployment/{$deploymentName}",
            '-n',
            $namespace,
            '--timeout',
            "{$timeoutSeconds}s"
        ]);

        return $result['success'];
    }

    /**
     * Wait for pods matching a label selector to be ready
     */
    public function waitForPodLabelReady(string $namespace, string $labelSelector, int $timeoutSeconds = 300): bool
    {
        $result = $this->runCommand([
            'wait',
            '--for=condition=ready',
            'pod',
            '-l',
            $labelSelector,
            '-n',
            $namespace,
            '--timeout',
            "{$timeoutSeconds}s"
        ]);

        return $result['success'];
    }

    /**
     * Wait for a StatefulSet to have ready replicas (polling approach)
     */
    public function waitForStatefulSetReady(string $namespace, string $name, int $timeoutSeconds = 300): bool
    {
        $start = time();
        $sleepInterval = 5;

        while ((time() - $start) < $timeoutSeconds) {
            $result = $this->runCommand([
                'get',
                'statefulset',
                $name,
                '-n',
                $namespace,
                '-o',
                'jsonpath={.status.readyReplicas}'
            ]);

            if ($result['success']) {
                $readyReplicas = (int) trim($result['output']);
                if ($readyReplicas >= 1) {
                    Log::info("StatefulSet {$name} is ready with {$readyReplicas} replicas");
                    return true;
                }
            }

            Log::debug("Waiting for StatefulSet {$name}... ({$result['output']})");
            sleep($sleepInterval);
        }

        Log::warning("StatefulSet {$name} did not become ready within {$timeoutSeconds}s");
        return false;
    }

    /**
     * Create a secret from file content
     */
    public function createSecretFromFile(string $namespace, string $name, string $key, string $fileContent): bool
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'k8s_secret_');
        file_put_contents($tempFile, $fileContent);

        try {
            // Delete existing secret if it exists
            $this->runCommand(['delete', 'secret', $name, '-n', $namespace, '--ignore-not-found']);

            $result = $this->runCommand([
                'create',
                'secret',
                'generic',
                $name,
                "--from-file={$key}={$tempFile}",
                '-n',
                $namespace
            ]);

            return $result['success'];
        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Copy a secret from one namespace to another
     */
    public function copySecret(string $name, string $fromNamespace, string $toNamespace): bool
    {
        // Get the secret as JSON
        $result = $this->runCommand([
            'get',
            'secret',
            $name,
            '-n',
            $fromNamespace,
            '-o',
            'json'
        ]);

        if (!$result['success']) {
            Log::error("Failed to read secret {$name} from {$fromNamespace}");
            return false;
        }

        $secret = json_decode($result['output'], true);
        if (!$secret) {
            return false;
        }

        // Strip metadata that would prevent applying to a new namespace
        $secret['metadata'] = [
            'name' => $secret['metadata']['name'],
            'namespace' => $toNamespace,
        ];
        unset($secret['metadata']['resourceVersion'], $secret['metadata']['uid'], $secret['metadata']['creationTimestamp']);

        $tempFile = tempnam(sys_get_temp_dir(), 'k8s_copy_');
        file_put_contents($tempFile, json_encode($secret));

        try {
            $result = $this->runCommand(['apply', '-f', $tempFile]);
            return $result['success'];
        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Get secret data
     */
    public function getSecretData(string $namespace, string $name, string $key): ?string
    {
        $result = $this->runCommand([
            'get',
            'secret',
            $name,
            '-n',
            $namespace,
            '-o',
            "jsonpath={.data.{$key}}"
        ]);

        if (!$result['success'] || empty($result['output'])) {
            return null;
        }

        return base64_decode($result['output']);
    }

    /**
     * Check if namespace exists
     */
    public function namespaceExists(string $name): bool
    {
        $result = $this->runCommand(['get', 'namespace', $name, '--ignore-not-found']);
        return $result['success'] && !empty(trim($result['output']));
    }

    /**
     * Run a kubectl command
     */
    protected function runCommand(array $args): array
    {
        $command = [$this->kubectlPath];

        if ($this->kubeconfigPath) {
            $command[] = '--kubeconfig';
            $command[] = $this->kubeconfigPath;
        }

        $command = array_merge($command, $args);

        $commandString = implode(' ', array_map('escapeshellarg', $command));

        Log::debug('K8sClient running command', ['command' => $commandString]);

        $output = [];
        $returnCode = 0;
        exec($commandString . ' 2>&1', $output, $returnCode);

        $outputString = implode("\n", $output);

        if ($returnCode !== 0) {
            Log::warning('K8sClient command failed', [
                'command' => $commandString,
                'output' => $outputString,
                'returnCode' => $returnCode,
            ]);
        }

        return [
            'success' => $returnCode === 0,
            'output' => $outputString,
            'returnCode' => $returnCode,
        ];
    }

    /**
     * Generate ResourceQuota YAML
     */
    public function generateResourceQuotaYaml(string $name, array $limits): string
    {
        return <<<YAML
apiVersion: v1
kind: ResourceQuota
metadata:
  name: {$name}
spec:
  hard:
    requests.cpu: "{$limits['cpu']}"
    requests.memory: "{$limits['memory']}"
    limits.cpu: "{$limits['cpu']}"
    limits.memory: "{$limits['memory']}"
    requests.storage: "{$limits['storage']}"
YAML;
    }

    /**
     * Generate LimitRange YAML
     */
    public function generateLimitRangeYaml(string $name): string
    {
        return <<<YAML
apiVersion: v1
kind: LimitRange
metadata:
  name: {$name}
spec:
  limits:
  - default:
      cpu: "500m"
      memory: "512Mi"
    defaultRequest:
      cpu: "100m"
      memory: "128Mi"
    type: Container
YAML;
    }
}
