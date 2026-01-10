# GovKloud Workbench Helm Chart

This chart deploys a code-server (VS Code in browser) instance for GovKloud lab sessions.

## Usage

This chart is installed automatically by Laravel's `SessionProvisioner` service when a user starts a lab session. It is not typically installed manually.

### Automatic Installation (via Laravel)

The `SessionProvisioner` calls:
```bash
helm install workbench-<session-id> ./charts/govkloud-workbench \
  --namespace gk-sess-<id> \
  --set session.id=<uuid> \
  --set session.token=<random-token> \
  --set image.repository=<lab.workbench_image> \
  --set resources.limits.cpu=<lab-config> \
  --set resources.limits.memory=<lab-config>
```

### Manual Installation (for testing)

```bash
helm install test-workbench ./charts/govkloud-workbench \
  --namespace test-lab \
  --create-namespace \
  --set session.id=test-123 \
  --set session.token=mysecretpassword
```

## Configuration

| Parameter | Description | Default |
|-----------|-------------|---------|
| `session.id` | Lab session UUID | `""` |
| `session.token` | Password for code-server | `""` |
| `image.repository` | Container image | `codercom/code-server` |
| `image.tag` | Image tag | `4.96.1` |
| `resources.limits.cpu` | CPU limit | `2` |
| `resources.limits.memory` | Memory limit | `4Gi` |
| `kubeconfig.secretName` | Secret containing vcluster kubeconfig | `vcluster-kubeconfig` |

## Architecture

```
┌─────────────────────────────────────────────────────┐
│ Host Namespace (gk-sess-xxxxx)                      │
│                                                     │
│  ┌─────────────┐    ┌─────────────────────────────┐ │
│  │  vcluster   │    │  workbench (code-server)    │ │
│  │             │    │                             │ │
│  │  K8s API    │◄───│  /home/coder/.kube/config   │ │
│  │             │    │  (mounted from secret)      │ │
│  └─────────────┘    └─────────────────────────────┘ │
│                                                     │
│  ┌─────────────────────────────────────────────────┐ │
│  │  Ingress (/labs/{session}/code)                │ │
│  └─────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────┘
```

The workbench pod mounts the vcluster kubeconfig, allowing users to run `kubectl` commands against their isolated virtual cluster.
