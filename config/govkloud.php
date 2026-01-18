<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Host Kubernetes Configuration
    |--------------------------------------------------------------------------
    */
    'host_k8s' => [
        'kubeconfig_path' => env('GOVKLOUD_KUBECONFIG_PATH', null), // null = in-cluster
        'namespace_prefix' => env('GOVKLOUD_NAMESPACE_PREFIX', 'gk-sess-'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ingress Configuration
    |--------------------------------------------------------------------------
    */
    'ingress' => [
        'base_domain' => env('GOVKLOUD_BASE_DOMAIN', 'labs.govkloud.io'),
        'path_prefix' => env('GOVKLOUD_PATH_PREFIX', '/labs'),
        'tls_enabled' => env('GOVKLOUD_TLS_ENABLED', true),
        'ingress_class' => env('GOVKLOUD_INGRESS_CLASS', 'nginx'),
        'port' => env('GOVKLOUD_INGRESS_PORT', null), // For local dev port-forward
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Configuration
    |--------------------------------------------------------------------------
    */
    'session' => [
        'ttl_default_minutes' => env('GOVKLOUD_TTL_MINUTES', 180),
        'idle_timeout_minutes' => env('GOVKLOUD_IDLE_TIMEOUT_MINUTES', 2),
        'max_concurrent_sessions' => env('GOVKLOUD_MAX_CONCURRENT', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Helm Configuration
    |--------------------------------------------------------------------------
    */
    'helm' => [
        'binary_path' => env('GOVKLOUD_HELM_PATH', '/usr/local/bin/helm'),
        'vcluster_chart' => env('GOVKLOUD_VCLUSTER_CHART', 'vcluster/vcluster'),
        'vcluster_repo' => env('GOVKLOUD_VCLUSTER_REPO', 'https://charts.loft.sh'),
        'workbench_chart_path' => env('GOVKLOUD_WORKBENCH_CHART', '/charts/govkloud-workbench'),
    ],

    /*
    |--------------------------------------------------------------------------
    | kubectl Configuration
    |--------------------------------------------------------------------------
    */
    'kubectl' => [
        'binary_path' => env('GOVKLOUD_KUBECTL_PATH', '/usr/local/bin/kubectl'),
    ],

    /*
    |--------------------------------------------------------------------------
    | vcluster CLI Configuration
    |--------------------------------------------------------------------------
    */
    'vcluster' => [
        'binary_path' => env('GOVKLOUD_VCLUSTER_PATH', '/usr/local/bin/vcluster'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource Defaults
    |--------------------------------------------------------------------------
    */
    'resources' => [
        // Workbench pod defaults (must fit within namespace quota after vcluster)
        'default_cpu_limit' => env('GOVKLOUD_DEFAULT_CPU', '500m'),
        'default_memory_limit' => env('GOVKLOUD_DEFAULT_MEMORY', '1Gi'),
        'default_storage_limit' => env('GOVKLOUD_DEFAULT_STORAGE', '10Gi'),

        // Namespace quota limits (must accommodate vcluster + workbench + overhead)
        'namespace_cpu_quota' => env('GOVKLOUD_NAMESPACE_CPU_QUOTA', '2'),
        'namespace_memory_quota' => env('GOVKLOUD_NAMESPACE_MEMORY_QUOTA', '4Gi'),
    ],
];
