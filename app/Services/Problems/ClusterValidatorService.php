<?php

namespace App\Services\Problems;

use App\Services\K8s\K8sClient;
use Illuminate\Support\Facades\Log;

/**
 * Auto-grading engine that validates real Kubernetes cluster state
 * against a set of rules defined per challenge.
 *
 * Each validation rule is checked against the user's vcluster
 * and returns a pass/fail result with a human-readable message.
 */
class ClusterValidatorService
{
    public function __construct(
        protected K8sClient $k8sClient,
    ) {}

    /**
     * Run all validation rules against a cluster by exec'ing into the vcluster pod.
     *
     * @param  array  $rules        Validation rules from challenge.validation_rules_json
     * @param  string $namespace    Host namespace (e.g. gk-sess-prob-username)
     * @param  string $runnerPod  Vcluster pod name (e.g. vc-prob-8f14e45f-0)
     * @return array  ['passed' => bool, 'results' => [...], 'score' => int, 'total' => int]
     */
    public function validate(array $rules, string $hostNamespace, string $runnerPod): array
    {
        $results = [];
        $passed = 0;
        $total = count($rules);

        foreach ($rules as $rule) {
            $result = $this->runValidation($rule, $hostNamespace, $runnerPod);
            $results[] = $result;

            if ($result['passed']) {
                $passed++;
            }
        }

        return [
            'passed' => $passed === $total,
            'results' => $results,
            'score' => $passed,
            'total' => $total,
        ];
    }

    /**
     * Run a single validation rule.
     */
    protected function runValidation(array $rule, string $hostNamespace, string $runnerPod): array
    {
        $type = $rule['type'] ?? '';
        $description = $rule['description'] ?? "Check: {$type}";

        try {
            $passed = match ($type) {
                'pod_status' => $this->checkPodStatus($rule, $hostNamespace, $runnerPod),
                'resource_exists' => $this->checkResourceExists($rule, $hostNamespace, $runnerPod),
                'resource_not_exists' => $this->checkResourceNotExists($rule, $hostNamespace, $runnerPod),
                'field_equals' => $this->checkFieldEquals($rule, $hostNamespace, $runnerPod),
                'field_contains' => $this->checkFieldContains($rule, $hostNamespace, $runnerPod),
                'container_image' => $this->checkContainerImage($rule, $hostNamespace, $runnerPod),
                'replica_count' => $this->checkReplicaCount($rule, $hostNamespace, $runnerPod),
                'endpoints_populated' => $this->checkEndpointsPopulated($rule, $hostNamespace, $runnerPod),
                'label_exists' => $this->checkLabelExists($rule, $hostNamespace, $runnerPod),
                'config_data' => $this->checkConfigData($rule, $hostNamespace, $runnerPod),
                'resource_count' => $this->checkResourceCount($rule, $hostNamespace, $runnerPod),
                'pod_log_contains' => $this->checkPodLogContains($rule, $hostNamespace, $runnerPod),
                default => throw new \InvalidArgumentException("Unknown validation type: {$type}"),
            };

            return [
                'type' => $type,
                'description' => $description,
                'passed' => $passed,
                'message' => $passed ? '✅ Passed' : '❌ Failed',
            ];
        } catch (\Throwable $e) {
            Log::warning("Validation rule failed with exception", [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return [
                'type' => $type,
                'description' => $description,
                'passed' => false,
                'message' => "❌ Error: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Check if a pod has the expected status (Running, Succeeded, etc.)
     */
    protected function checkPodStatus(array $rule, string $hostNamespace, string $runnerPod): bool
    {
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $expected = $rule['expected_status'];

        $result = $this->kubectl(
            ['get', 'pod', $name, '-n', $namespace, '-o', 'jsonpath={.status.phase}'],
            $hostNamespace, $runnerPod
        );

        return $result['success'] && trim($result['output']) === $expected;
    }

    /**
     * Check if a Kubernetes resource exists.
     */
    protected function checkResourceExists(array $rule, string $hostNamespace, string $runnerPod): bool
    {
        $kind = strtolower($rule['kind']);
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';

        $result = $this->kubectl(
            ['get', $kind, $name, '-n', $namespace, '--ignore-not-found', '-o', 'name'],
            $hostNamespace, $runnerPod
        );

        return $result['success'] && !empty(trim($result['output']));
    }

    /**
     * Check if a Kubernetes resource does NOT exist.
     */
    protected function checkResourceNotExists(array $rule, string $hostNamespace, string $runnerPod): bool
    {
        return !$this->checkResourceExists($rule, $hostNamespace, $runnerPod);
    }

    /**
     * Check if a specific field equals an expected value.
     * Uses jsonpath to extract the field.
     */
    protected function checkFieldEquals(array $rule, string $hostNamespace, string $runnerPod): bool
    {
        $kind = strtolower($rule['kind']);
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $field = $rule['field']; // e.g. "spec.replicas"
        $expected = (string) $rule['expected'];

        $jsonpath = "{.{$field}}";

        $result = $this->kubectl(
            ['get', $kind, $name, '-n', $namespace, '-o', "jsonpath={$jsonpath}"],
            $hostNamespace, $runnerPod
        );

        return $result['success'] && trim($result['output']) === $expected;
    }

    /**
     * Check if a specific field contains a substring.
     */
    protected function checkFieldContains(array $rule, string $hostNamespace, string $runnerPod): bool
    {
        $kind = strtolower($rule['kind']);
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $field = $rule['field'];
        $expected = $rule['contains'];

        $jsonpath = "{.{$field}}";

        $result = $this->kubectl(
            ['get', $kind, $name, '-n', $namespace, '-o', "jsonpath={$jsonpath}"],
            $hostNamespace, $runnerPod
        );

        return $result['success'] && str_contains($result['output'], $expected);
    }

    /**
     * Check if a pod's container uses the expected image.
     */
    protected function checkContainerImage(array $rule, string $hostNamespace, string $runnerPod): bool
    {
        $podName = $rule['pod_name'] ?? $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $expectedImage = $rule['expected_image'];
        $containerIndex = $rule['container_index'] ?? 0;

        $result = $this->kubectl(
            ['get', 'pod', $podName, '-n', $namespace, '-o',
             "jsonpath={.spec.containers[{$containerIndex}].image}"],
            $hostNamespace, $runnerPod
        );

        $actualImage = trim($result['output']);
        // Allow partial match (e.g. expected "nginx" matches "nginx:1.25")
        return $result['success'] && (
            $actualImage === $expectedImage ||
            str_starts_with($actualImage, $expectedImage . ':') ||
            str_starts_with($actualImage, $expectedImage . '@')
        );
    }

    /**
     * Check if a Deployment/StatefulSet has the expected number of ready replicas.
     */
    protected function checkReplicaCount(array $rule, string $hostNamespace, string $runnerPod): bool
    {
        $kind = strtolower($rule['kind'] ?? 'deployment');
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $expected = (int) ($rule['expected'] ?? $rule['expected_replicas'] ?? 0);

        $result = $this->kubectl(
            ['get', $kind, $name, '-n', $namespace, '-o', 'jsonpath={.status.readyReplicas}'],
            $hostNamespace, $runnerPod
        );

        return $result['success'] && (int) trim($result['output']) === $expected;
    }

    /**
     * Check if a Service has active endpoints (at least one).
     */
    protected function checkEndpointsPopulated(array $rule, string $hostNamespace, string $runnerPod): bool
    {
        $serviceName = $rule['service_name'] ?? $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';

        $result = $this->kubectl(
            ['get', 'endpoints', $serviceName, '-n', $namespace, '-o',
             'jsonpath={.subsets[0].addresses}'],
            $hostNamespace, $runnerPod
        );

        return $result['success'] && !empty(trim($result['output']));
    }

    /**
     * Check if a resource has specific labels.
     */
    protected function checkLabelExists(array $rule, string $hostNamespace, string $runnerPod): bool
    {
        $kind = strtolower($rule['kind']);
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';

        // Support both formats:
        //   { "label_key": "app", "label_value": "hello" }
        //   { "expected_labels": { "app": "hello" } }
        $labelKey = $rule['label_key'] ?? null;
        $labelValue = $rule['label_value'] ?? null;

        if (!$labelKey && isset($rule['expected_labels'])) {
            // Extract first key-value pair from expected_labels map
            $labelKey = array_key_first($rule['expected_labels']);
            $labelValue = $rule['expected_labels'][$labelKey] ?? null;
        }

        if (!$labelKey) {
            Log::warning('label_exists rule missing label_key or expected_labels', $rule);
            return false;
        }

        $result = $this->kubectl(
            ['get', $kind, $name, '-n', $namespace, '-o',
             "jsonpath={.metadata.labels.{$labelKey}}"],
            $hostNamespace, $runnerPod
        );

        if (!$result['success']) {
            return false;
        }

        $actual = trim($result['output']);

        // If no specific value required, just check key exists
        if ($labelValue === null) {
            return !empty($actual);
        }

        return $actual === $labelValue;
    }

    /**
     * Check if a ConfigMap or Secret contains specific keys.
     */
    protected function checkConfigData(array $rule, string $hostNamespace, string $runnerPod): bool
    {
        $kind = strtolower($rule['kind'] ?? 'configmap');
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';

        // Support both formats:
        //   { "key": "APP_ENV", "expected_value": "production" }
        //   { "expected_keys": ["APP_ENV"] }
        $key = $rule['key'] ?? null;
        $expectedValue = $rule['expected_value'] ?? null;

        // If using expected_keys array, check each key exists
        if (!$key && isset($rule['expected_keys'])) {
            foreach ($rule['expected_keys'] as $checkKey) {
                $result = $this->kubectl(
                    ['get', $kind, $name, '-n', $namespace, '-o', "jsonpath={.data.{$checkKey}}"],
                    $hostNamespace, $runnerPod
                );
                if (!$result['success'] || empty(trim($result['output']))) {
                    return false;
                }
            }
            return true;
        }

        if (!$key) {
            Log::warning('config_data rule missing key or expected_keys', $rule);
            return false;
        }

        $result = $this->kubectl(
            ['get', $kind, $name, '-n', $namespace, '-o', "jsonpath={.data.{$key}}"],
            $hostNamespace, $runnerPod
        );

        if (!$result['success']) {
            return false;
        }

        $actual = trim($result['output']);

        if ($expectedValue !== null) {
            return $actual === $expectedValue;
        }

        return !empty($actual);
    }

    /**
     * Check that a specific number of resources of a kind exist.
     */
    protected function checkResourceCount(array $rule, string $hostNamespace, string $runnerPod): bool
    {
        $kind = strtolower($rule['kind']);
        $namespace = $rule['namespace'] ?? 'default';
        $expected = (int) $rule['expected'];
        $labelSelector = $rule['label_selector'] ?? null;

        $args = ['get', $kind, '-n', $namespace, '-o', 'jsonpath={.items[*].metadata.name}'];
        if ($labelSelector) {
            $args[] = '-l';
            $args[] = $labelSelector;
        }

        $result = $this->kubectl($args, $hostNamespace, $runnerPod);

        if (!$result['success']) {
            return false;
        }

        $names = array_filter(explode(' ', trim($result['output'])));
        return count($names) === $expected;
    }

    /**
     * Check if a pod's logs contain a specific string.
     */
    protected function checkPodLogContains(array $rule, string $hostNamespace, string $runnerPod): bool
    {
        $podName = $rule['pod_name'] ?? $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $contains = $rule['contains'];
        $container = $rule['container'] ?? null;

        $args = ['logs', $podName, '-n', $namespace, '--tail=100'];
        if ($container) {
            $args[] = '-c';
            $args[] = $container;
        }

        $result = $this->kubectl($args, $hostNamespace, $runnerPod);

        return $result['success'] && str_contains($result['output'], $contains);
    }

    /**
     * Run a kubectl command using a specific kubeconfig (the user's vcluster).
     */
    protected function kubectl(array $args, string $hostNamespace, string $runnerPod): array
    {
        $kubectlPath = config('govkloud.kubectl.binary_path');
        $hostKubeconfig = config('govkloud.host_k8s.kubeconfig_path');

        // Build: kubectl --kubeconfig <host> exec -n <namespace> <pod> -- kubectl <args>
        $innerArgs = implode(' ', array_map('escapeshellarg', $args));
        $command = sprintf(
            '%s --kubeconfig %s exec -n %s %s -- kubectl %s 2>&1',
            escapeshellarg($kubectlPath),
            escapeshellarg($hostKubeconfig),
            escapeshellarg($hostNamespace),
            escapeshellarg($runnerPod),
            $innerArgs
        );

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        return [
            'success' => $returnCode === 0,
            'output' => implode("\n", $output),
            'returnCode' => $returnCode,
        ];
    }
}
