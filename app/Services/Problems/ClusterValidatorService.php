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
     * Run all validation rules against a cluster using the given kubeconfig.
     *
     * @param  array  $rules      Validation rules from challenge.validation_rules_json
     * @param  string $kubeconfig Path to the vcluster kubeconfig file
     * @return array  ['passed' => bool, 'results' => [...], 'score' => int, 'total' => int]
     */
    public function validate(array $rules, string $kubeconfig): array
    {
        $results = [];
        $passed = 0;
        $total = count($rules);

        foreach ($rules as $rule) {
            $result = $this->runValidation($rule, $kubeconfig);
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
    protected function runValidation(array $rule, string $kubeconfig): array
    {
        $type = $rule['type'] ?? '';
        $description = $rule['description'] ?? "Check: {$type}";

        try {
            $passed = match ($type) {
                'pod_status' => $this->checkPodStatus($rule, $kubeconfig),
                'resource_exists' => $this->checkResourceExists($rule, $kubeconfig),
                'resource_not_exists' => $this->checkResourceNotExists($rule, $kubeconfig),
                'field_equals' => $this->checkFieldEquals($rule, $kubeconfig),
                'field_contains' => $this->checkFieldContains($rule, $kubeconfig),
                'container_image' => $this->checkContainerImage($rule, $kubeconfig),
                'replica_count' => $this->checkReplicaCount($rule, $kubeconfig),
                'endpoints_populated' => $this->checkEndpointsPopulated($rule, $kubeconfig),
                'label_exists' => $this->checkLabelExists($rule, $kubeconfig),
                'config_data' => $this->checkConfigData($rule, $kubeconfig),
                'resource_count' => $this->checkResourceCount($rule, $kubeconfig),
                'pod_log_contains' => $this->checkPodLogContains($rule, $kubeconfig),
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
    protected function checkPodStatus(array $rule, string $kubeconfig): bool
    {
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $expected = $rule['expected_status'];

        $result = $this->kubectl(
            ['get', 'pod', $name, '-n', $namespace, '-o', 'jsonpath={.status.phase}'],
            $kubeconfig
        );

        return $result['success'] && trim($result['output']) === $expected;
    }

    /**
     * Check if a Kubernetes resource exists.
     */
    protected function checkResourceExists(array $rule, string $kubeconfig): bool
    {
        $kind = strtolower($rule['kind']);
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';

        $result = $this->kubectl(
            ['get', $kind, $name, '-n', $namespace, '--ignore-not-found', '-o', 'name'],
            $kubeconfig
        );

        return $result['success'] && !empty(trim($result['output']));
    }

    /**
     * Check if a Kubernetes resource does NOT exist.
     */
    protected function checkResourceNotExists(array $rule, string $kubeconfig): bool
    {
        return !$this->checkResourceExists($rule, $kubeconfig);
    }

    /**
     * Check if a specific field equals an expected value.
     * Uses jsonpath to extract the field.
     */
    protected function checkFieldEquals(array $rule, string $kubeconfig): bool
    {
        $kind = strtolower($rule['kind']);
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $field = $rule['field']; // e.g. "spec.replicas"
        $expected = (string) $rule['expected'];

        $jsonpath = "{.{$field}}";

        $result = $this->kubectl(
            ['get', $kind, $name, '-n', $namespace, '-o', "jsonpath={$jsonpath}"],
            $kubeconfig
        );

        return $result['success'] && trim($result['output']) === $expected;
    }

    /**
     * Check if a specific field contains a substring.
     */
    protected function checkFieldContains(array $rule, string $kubeconfig): bool
    {
        $kind = strtolower($rule['kind']);
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $field = $rule['field'];
        $expected = $rule['contains'];

        $jsonpath = "{.{$field}}";

        $result = $this->kubectl(
            ['get', $kind, $name, '-n', $namespace, '-o', "jsonpath={$jsonpath}"],
            $kubeconfig
        );

        return $result['success'] && str_contains($result['output'], $expected);
    }

    /**
     * Check if a pod's container uses the expected image.
     */
    protected function checkContainerImage(array $rule, string $kubeconfig): bool
    {
        $podName = $rule['pod_name'] ?? $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $expectedImage = $rule['expected_image'];
        $containerIndex = $rule['container_index'] ?? 0;

        $result = $this->kubectl(
            ['get', 'pod', $podName, '-n', $namespace, '-o',
             "jsonpath={.spec.containers[{$containerIndex}].image}"],
            $kubeconfig
        );

        return $result['success'] && trim($result['output']) === $expectedImage;
    }

    /**
     * Check if a Deployment/StatefulSet has the expected number of ready replicas.
     */
    protected function checkReplicaCount(array $rule, string $kubeconfig): bool
    {
        $kind = strtolower($rule['kind'] ?? 'deployment');
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $expected = (int) $rule['expected'];

        $result = $this->kubectl(
            ['get', $kind, $name, '-n', $namespace, '-o', 'jsonpath={.status.readyReplicas}'],
            $kubeconfig
        );

        return $result['success'] && (int) trim($result['output']) === $expected;
    }

    /**
     * Check if a Service has active endpoints (at least one).
     */
    protected function checkEndpointsPopulated(array $rule, string $kubeconfig): bool
    {
        $serviceName = $rule['service_name'] ?? $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';

        $result = $this->kubectl(
            ['get', 'endpoints', $serviceName, '-n', $namespace, '-o',
             'jsonpath={.subsets[0].addresses}'],
            $kubeconfig
        );

        return $result['success'] && !empty(trim($result['output']));
    }

    /**
     * Check if a resource has specific labels.
     */
    protected function checkLabelExists(array $rule, string $kubeconfig): bool
    {
        $kind = strtolower($rule['kind']);
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $labelKey = $rule['label_key'];
        $labelValue = $rule['label_value'] ?? null;

        $result = $this->kubectl(
            ['get', $kind, $name, '-n', $namespace, '-o',
             "jsonpath={.metadata.labels.{$labelKey}}"],
            $kubeconfig
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
    protected function checkConfigData(array $rule, string $kubeconfig): bool
    {
        $kind = strtolower($rule['kind'] ?? 'configmap');
        $name = $rule['name'];
        $namespace = $rule['namespace'] ?? 'default';
        $key = $rule['key'];
        $expectedValue = $rule['expected_value'] ?? null;

        $result = $this->kubectl(
            ['get', $kind, $name, '-n', $namespace, '-o', "jsonpath={.data.{$key}}"],
            $kubeconfig
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
    protected function checkResourceCount(array $rule, string $kubeconfig): bool
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

        $result = $this->kubectl($args, $kubeconfig);

        if (!$result['success']) {
            return false;
        }

        $names = array_filter(explode(' ', trim($result['output'])));
        return count($names) === $expected;
    }

    /**
     * Check if a pod's logs contain a specific string.
     */
    protected function checkPodLogContains(array $rule, string $kubeconfig): bool
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

        $result = $this->kubectl($args, $kubeconfig);

        return $result['success'] && str_contains($result['output'], $contains);
    }

    /**
     * Run a kubectl command using a specific kubeconfig (the user's vcluster).
     */
    protected function kubectl(array $args, string $kubeconfig): array
    {
        $kubectlPath = config('govkloud.kubectl.binary_path');
        $command = [$kubectlPath, '--kubeconfig', $kubeconfig];
        $command = array_merge($command, $args);

        $commandString = implode(' ', array_map('escapeshellarg', $command));

        $output = [];
        $returnCode = 0;
        exec($commandString . ' 2>&1', $output, $returnCode);

        return [
            'success' => $returnCode === 0,
            'output' => implode("\n", $output),
            'returnCode' => $returnCode,
        ];
    }
}
