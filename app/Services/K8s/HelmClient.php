<?php

namespace App\Services\K8s;

use Illuminate\Support\Facades\Log;

class HelmClient
{
    protected string $helmPath;
    protected ?string $kubeconfigPath;

    public function __construct()
    {
        $this->helmPath = config('govkloud.helm.binary_path');
        $this->kubeconfigPath = config('govkloud.host_k8s.kubeconfig_path');
    }

    /**
     * Add a Helm repo if it doesn't exist
     */
    public function repoAddIfMissing(string $name, string $url): bool
    {
        // Check if repo exists
        $result = $this->runCommand(['repo', 'list', '-o', 'json']);

        if ($result['success']) {
            $repos = json_decode($result['output'], true) ?? [];
            foreach ($repos as $repo) {
                if (($repo['name'] ?? '') === $name) {
                    return true; // Already exists
                }
            }
        }

        // Add the repo
        $result = $this->runCommand(['repo', 'add', $name, $url]);

        if ($result['success']) {
            $this->runCommand(['repo', 'update']);
        }

        return $result['success'];
    }

    /**
     * Install a Helm release (with --wait)
     */
    public function install(string $release, string $chart, string $namespace, array $values = []): bool
    {
        $args = [
            'install',
            $release,
            $chart,
            '-n',
            $namespace,
            '--create-namespace',
            '--wait',
            '--timeout',
            '10m',
        ];

        // Add values as --set arguments
        foreach ($values as $key => $value) {
            $args[] = '--set';
            $args[] = "{$key}={$value}";
        }

        $result = $this->runCommand($args);
        return $result['success'];
    }

    /**
     * Install a Helm release without waiting (returns immediately)
     */
    public function installNoWait(string $release, string $chart, string $namespace, array $values = []): bool
    {
        $args = [
            'install',
            $release,
            $chart,
            '-n',
            $namespace,
            '--create-namespace',
        ];

        // Add values as --set arguments
        foreach ($values as $key => $value) {
            $args[] = '--set';
            $args[] = "{$key}={$value}";
        }

        $result = $this->runCommand($args);
        return $result['success'];
    }

    /**
     * Install with a values file
     */
    public function installWithValuesFile(string $release, string $chart, string $namespace, string $valuesYaml): bool
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'helm_values_');
        file_put_contents($tempFile, $valuesYaml);

        try {
            $args = [
                'install',
                $release,
                $chart,
                '-n',
                $namespace,
                '--create-namespace',
                '--wait',
                '--timeout',
                '10m',
                '-f',
                $tempFile,
            ];

            $result = $this->runCommand($args);
            return $result['success'];
        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Upgrade or install a Helm release (with --wait)
     */
    public function upgradeInstall(string $release, string $chart, string $namespace, array $values = []): bool
    {
        $args = [
            'upgrade',
            '--install',
            $release,
            $chart,
            '-n',
            $namespace,
            '--create-namespace',
            '--wait',
            '--timeout',
            '10m',
        ];

        foreach ($values as $key => $value) {
            $args[] = '--set';
            $args[] = "{$key}={$value}";
        }

        $result = $this->runCommand($args);
        return $result['success'];
    }

    /**
     * Upgrade or install a Helm release without waiting (returns immediately)
     */
    public function upgradeInstallNoWait(string $release, string $chart, string $namespace, array $values = []): bool
    {
        $args = [
            'upgrade',
            '--install',
            $release,
            $chart,
            '-n',
            $namespace,
            '--create-namespace',
        ];

        foreach ($values as $key => $value) {
            $args[] = '--set';
            $args[] = "{$key}={$value}";
        }

        $result = $this->runCommand($args);
        return $result['success'];
    }

    /**
     * Uninstall a Helm release
     */
    public function uninstall(string $release, string $namespace): bool
    {
        $result = $this->runCommand([
            'uninstall',
            $release,
            '-n',
            $namespace,
            '--wait',
        ]);

        return $result['success'];
    }

    /**
     * Check if a release exists
     */
    public function releaseExists(string $release, string $namespace): bool
    {
        $result = $this->runCommand([
            'status',
            $release,
            '-n',
            $namespace,
        ]);

        return $result['success'];
    }

    /**
     * Get release status
     */
    public function getReleaseStatus(string $release, string $namespace): ?string
    {
        $result = $this->runCommand([
            'status',
            $release,
            '-n',
            $namespace,
            '-o',
            'json',
        ]);

        if (!$result['success']) {
            return null;
        }

        $data = json_decode($result['output'], true);
        return $data['info']['status'] ?? null;
    }

    /**
     * Run a helm command
     */
    protected function runCommand(array $args): array
    {
        $command = [$this->helmPath];

        if ($this->kubeconfigPath) {
            $command[] = '--kubeconfig';
            $command[] = $this->kubeconfigPath;
        }

        $command = array_merge($command, $args);

        $commandString = implode(' ', array_map('escapeshellarg', $command));

        Log::debug('HelmClient running command', ['command' => $commandString]);

        $output = [];
        $returnCode = 0;
        exec($commandString . ' 2>&1', $output, $returnCode);

        $outputString = implode("\n", $output);

        if ($returnCode !== 0) {
            Log::warning('HelmClient command failed', [
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
}
