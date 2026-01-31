<?php

namespace App\Services\K8s;

class IngressUrlBuilder
{
  protected string $baseDomain;
  protected string $pathPrefix;
  protected bool $tlsEnabled;
  protected ?int $port;

  public function __construct()
  {
    $this->baseDomain = config('govkloud.ingress.base_domain');
    $this->pathPrefix = config('govkloud.ingress.path_prefix');
    $this->tlsEnabled = config('govkloud.ingress.tls_enabled');
    $this->port = config('govkloud.ingress.port');
  }

  /**
   * Build the workbench URL for a session
   */
  public function buildWorkbenchUrl(string $sessionId): string
  {
    $scheme = $this->tlsEnabled ? 'https' : 'http';
    $path = rtrim($this->pathPrefix, '/') . '/' . $sessionId . '/';

    // Include port if configured (for local dev with port-forward)
    $host = $this->baseDomain;
    if ($this->port) {
      $host .= ':' . $this->port;
    }

    return "{$scheme}://{$host}{$path}";
  }

  /**
   * Build the ingress path for a session
   */
  public function buildIngressPath(string $sessionId): string
  {
    // Match all paths under /labs/{session}/ - not just /code
    // This allows VS Code's /login, /static/, /vscode/ etc. to work
    return rtrim($this->pathPrefix, '/') . '/' . $sessionId . '(/|$)(.*)';
  }

  /**
   * Generate Ingress YAML for workbench
   */
  public function generateIngressYaml(string $name, string $sessionId, string $serviceName, int $servicePort = 8080): string
  {
    $ingressClass = config('govkloud.ingress.ingress_class');
    $path = $this->buildIngressPath($sessionId);
    $host = $this->baseDomain;

    $tlsBlock = '';
    if ($this->tlsEnabled) {
      $tlsBlock = <<<YAML
  tls:
  - hosts:
    - {$host}
    secretName: govkloud-tls
YAML;
    }

    return <<<YAML
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: {$name}
  annotations:
    kubernetes.io/ingress.class: {$ingressClass}
    nginx.ingress.kubernetes.io/rewrite-target: /\$2
    nginx.ingress.kubernetes.io/proxy-read-timeout: "3600"
    nginx.ingress.kubernetes.io/proxy-send-timeout: "3600"
spec:
  ingressClassName: {$ingressClass}
{$tlsBlock}
  rules:
  - host: {$host}
    http:
      paths:
      - path: {$path}
        pathType: ImplementationSpecific
        backend:
          service:
            name: {$serviceName}
            port:
              number: {$servicePort}
YAML;
  }
}
