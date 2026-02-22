<?php

namespace Database\Seeders;

use App\Models\Lab;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Seeder;

class SampleCourseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding sample courses...');

        // ============================================================
        // COURSE 1: Kubernetes Fundamentals
        // ============================================================
        $k8sFundamentals = Module::updateOrCreate(
            ['slug' => 'k8s-fundamentals'],
            [
                'title' => 'Kubernetes Fundamentals',
                'description' => 'Master the core concepts of Kubernetes. Learn about Pods, Deployments, Services, and how to manage containerized applications at scale. This hands-on course takes you from zero to deploying production-ready workloads.',
                'category' => 'Kubernetes',
                'order_index' => 1,
                'is_published' => true,
            ]
        );

        $k8sLab = Lab::updateOrCreate(
            ['title' => 'K8s Fundamentals Lab', 'module_id' => $k8sFundamentals->id],
            [
                'description' => 'Hands-on Kubernetes cluster with pre-configured environment',
                'estimated_minutes' => 60,
                'ttl_minutes' => 120,
                'workbench_image' => 'codercom/code-server:latest',
                'is_published' => true,
            ]
        );

        $this->createLessons($k8sFundamentals, $k8sLab, [
            [
                'title' => 'What is Kubernetes?',
                'video_url' => 'https://www.youtube.com/watch?v=PziYflu8cB8',
                'reading_md' => "# What is Kubernetes?\n\nKubernetes (K8s) is an open-source container orchestration platform that automates the deployment, scaling, and management of containerized applications.\n\n## Why Kubernetes?\n\n- **Automated rollouts & rollbacks** — Progressively roll out changes to your application with zero downtime\n- **Service discovery & load balancing** — Kubernetes gives pods their own IPs and a single DNS name for a set of pods\n- **Self-healing** — Restarts failed containers, replaces and reschedules containers when nodes die\n- **Horizontal scaling** — Scale your application up or down with a simple command or automatically\n\n## History\n\nKubernetes was originally developed by Google, drawing on over 15 years of experience running production workloads. It was open-sourced in 2014 and is now maintained by the Cloud Native Computing Foundation (CNCF).\n\n## Architecture Overview\n\n```\nControl Plane:\n├── API Server (kube-apiserver)\n├── Scheduler (kube-scheduler)\n├── Controller Manager (kube-controller-manager)\n└── etcd (distributed key-value store)\n\nWorker Nodes:\n├── kubelet\n├── kube-proxy\n└── Container Runtime (containerd)\n```",
                'quiz_json' => [
                    [
                        'question' => 'What does Kubernetes automate?',
                        'type' => 'multiple_choice',
                        'options' => [
                            'Database queries',
                            'Deployment, scaling, and management of containerized applications',
                            'Code compilation',
                            'Network configuration only',
                        ],
                        'correct_answer' => 'Deployment, scaling, and management of containerized applications',
                        'explanation' => 'Kubernetes is a container orchestration platform that automates deployment, scaling, and management.',
                    ],
                    [
                        'question' => 'Which organization originally developed Kubernetes?',
                        'type' => 'multiple_choice',
                        'options' => ['Microsoft', 'Amazon', 'Google', 'Red Hat'],
                        'correct_answer' => 'Google',
                        'explanation' => 'Kubernetes was originally developed by Google and open-sourced in 2014.',
                    ],
                ],
            ],
            [
                'title' => 'Pods: The Smallest Deployable Unit',
                'video_url' => 'https://www.youtube.com/watch?v=5cNrTU6o3Fw',
                'reading_md' => "# Understanding Pods\n\nA **Pod** is the smallest deployable unit in Kubernetes. It represents a single instance of a running process in your cluster.\n\n## Key Concepts\n\n- A Pod can contain **one or more containers** that share storage and network\n- Containers in a Pod share the same IP address and port space\n- Pods are **ephemeral** — they can be created, destroyed, and replaced\n\n## Pod YAML Example\n\n```yaml\napiVersion: v1\nkind: Pod\nmetadata:\n  name: nginx-pod\n  labels:\n    app: nginx\nspec:\n  containers:\n  - name: nginx\n    image: nginx:1.25\n    ports:\n    - containerPort: 80\n    resources:\n      requests:\n        memory: \"64Mi\"\n        cpu: \"250m\"\n      limits:\n        memory: \"128Mi\"\n        cpu: \"500m\"\n```\n\n## Pod Lifecycle\n\n1. **Pending** — Pod accepted but containers not yet running\n2. **Running** — At least one container is running\n3. **Succeeded** — All containers terminated successfully\n4. **Failed** — At least one container terminated with failure\n5. **Unknown** — State cannot be determined",
                'quiz_json' => [
                    [
                        'question' => 'What is the smallest deployable unit in Kubernetes?',
                        'type' => 'multiple_choice',
                        'options' => ['Container', 'Pod', 'Deployment', 'Node'],
                        'correct_answer' => 'Pod',
                    ],
                    [
                        'question' => 'Can a Pod contain multiple containers?',
                        'type' => 'multiple_choice',
                        'options' => ['Yes', 'No'],
                        'correct_answer' => 'Yes',
                        'explanation' => 'Pods can contain one or more containers that share storage and network resources.',
                    ],
                ],
            ],
            [
                'title' => 'Deployments & ReplicaSets',
                'video_url' => 'https://www.youtube.com/watch?v=EQNO_kM96Mo',
                'reading_md' => "# Deployments\n\nA **Deployment** provides declarative updates for Pods and ReplicaSets. You describe a desired state and the Deployment controller changes the actual state to match.\n\n## Creating a Deployment\n\n```yaml\napiVersion: apps/v1\nkind: Deployment\nmetadata:\n  name: nginx-deployment\nspec:\n  replicas: 3\n  selector:\n    matchLabels:\n      app: nginx\n  template:\n    metadata:\n      labels:\n        app: nginx\n    spec:\n      containers:\n      - name: nginx\n        image: nginx:1.25\n        ports:\n        - containerPort: 80\n```\n\n## Scaling\n\n```bash\n# Scale to 5 replicas\nkubectl scale deployment nginx-deployment --replicas=5\n\n# Autoscale based on CPU\nkubectl autoscale deployment nginx-deployment --min=3 --max=10 --cpu-percent=80\n```\n\n## Rolling Updates\n\nDeployments support rolling updates by default — pods are updated gradually to ensure zero downtime:\n\n```bash\nkubectl set image deployment/nginx-deployment nginx=nginx:1.26\nkubectl rollout status deployment/nginx-deployment\nkubectl rollout undo deployment/nginx-deployment  # rollback\n```",
                'quiz_json' => [
                    [
                        'question' => 'What does a Deployment manage?',
                        'type' => 'multiple_choice',
                        'options' => ['Pods and ReplicaSets', 'Nodes', 'Namespaces', 'Volumes'],
                        'correct_answer' => 'Pods and ReplicaSets',
                    ],
                ],
            ],
            [
                'title' => 'Services & Networking',
                'video_url' => 'https://www.youtube.com/watch?v=T4Z7visMM4E',
                'reading_md' => "# Kubernetes Services\n\nA **Service** is an abstraction that defines a logical set of Pods and a policy for accessing them. Services enable loose coupling between dependent Pods.\n\n## Service Types\n\n| Type | Description |\n|------|-------------|\n| **ClusterIP** | Internal-only (default) |\n| **NodePort** | Exposes on each node's IP at a static port |\n| **LoadBalancer** | Creates an external load balancer |\n| **ExternalName** | Maps to an external DNS name |\n\n## ClusterIP Example\n\n```yaml\napiVersion: v1\nkind: Service\nmetadata:\n  name: nginx-service\nspec:\n  selector:\n    app: nginx\n  ports:\n  - port: 80\n    targetPort: 80\n  type: ClusterIP\n```\n\n## DNS Resolution\n\nKubernetes provides built-in DNS. Services are accessible via:\n```\n<service-name>.<namespace>.svc.cluster.local\n```",
            ],
            [
                'title' => 'ConfigMaps & Secrets',
                'video_url' => 'https://www.youtube.com/watch?v=FAnQTgr04mU',
                'reading_md' => "# ConfigMaps & Secrets\n\n## ConfigMaps\nConfigMaps allow you to decouple configuration from container images. They store non-sensitive configuration data as key-value pairs.\n\n```yaml\napiVersion: v1\nkind: ConfigMap\nmetadata:\n  name: app-config\ndata:\n  DATABASE_HOST: \"mysql.default.svc\"\n  LOG_LEVEL: \"info\"\n```\n\n## Secrets\nSecrets store sensitive data like passwords, tokens, and keys. They are base64-encoded.\n\n```yaml\napiVersion: v1\nkind: Secret\nmetadata:\n  name: db-credentials\ntype: Opaque\ndata:\n  username: YWRtaW4=\n  password: cGFzc3dvcmQ=\n```\n\n> **Important:** In production, use a secrets management solution like HashiCorp Vault or Azure Key Vault.",
                'quiz_json' => [
                    [
                        'question' => 'What is the difference between ConfigMaps and Secrets?',
                        'type' => 'multiple_choice',
                        'options' => [
                            'ConfigMaps store non-sensitive data, Secrets store sensitive data',
                            'They are the same thing',
                            'ConfigMaps are faster',
                            'Secrets can only store passwords',
                        ],
                        'correct_answer' => 'ConfigMaps store non-sensitive data, Secrets store sensitive data',
                    ],
                ],
            ],
            [
                'title' => 'Namespaces & Resource Management',
                'video_url' => 'https://www.youtube.com/watch?v=K3jNo4z5Jx8',
                'reading_md' => "# Namespaces\n\nNamespaces provide a mechanism for isolating groups of resources within a single cluster.\n\n## Default Namespaces\n\n- `default` — For objects with no namespace\n- `kube-system` — For objects created by the Kubernetes system\n- `kube-public` — Readable by all users\n- `kube-node-lease` — For lease objects associated with nodes\n\n## Resource Quotas\n\n```yaml\napiVersion: v1\nkind: ResourceQuota\nmetadata:\n  name: team-quota\n  namespace: team-a\nspec:\n  hard:\n    pods: \"20\"\n    requests.cpu: \"4\"\n    requests.memory: \"8Gi\"\n    limits.cpu: \"8\"\n    limits.memory: \"16Gi\"\n```",
            ],
        ]);

        // ============================================================
        // COURSE 2: Docker Containers
        // ============================================================
        $docker = Module::updateOrCreate(
            ['slug' => 'docker-essentials'],
            [
                'title' => 'Docker Container Essentials',
                'description' => 'Learn Docker from the ground up. Build, ship, and run applications using containers. Understand images, Dockerfiles, multi-stage builds, and container networking for modern application development.',
                'category' => 'Containers',
                'order_index' => 2,
                'is_published' => true,
            ]
        );

        $this->createLessons($docker, null, [
            [
                'title' => 'Introduction to Containers',
                'video_url' => 'https://www.youtube.com/watch?v=0qotVMX-J5s',
                'reading_md' => "# What are Containers?\n\nContainers are lightweight, standalone packages that include everything needed to run an application: code, runtime, system tools, libraries, and settings.\n\n## Containers vs Virtual Machines\n\n| Feature | Containers | VMs |\n|---------|-----------|-----|\n| **Boot time** | Seconds | Minutes |\n| **Size** | MBs | GBs |\n| **OS** | Shares host kernel | Full OS |\n| **Isolation** | Process-level | Hardware-level |\n| **Density** | 100s per host | 10s per host |\n\n## Why Containers?\n\n- **Consistency** — Works the same everywhere\n- **Speed** — Start in milliseconds\n- **Efficiency** — Share the host OS kernel\n- **Portability** — Run anywhere Docker is installed",
            ],
            [
                'title' => 'Building Docker Images',
                'video_url' => 'https://www.youtube.com/watch?v=SnSH8Ht3MIc',
                'reading_md' => "# Docker Images & Dockerfiles\n\nA **Docker image** is a read-only template used to create containers. Images are built using a **Dockerfile**.\n\n## Dockerfile Example\n\n```dockerfile\n# Multi-stage build for a Node.js app\nFROM node:20-alpine AS builder\nWORKDIR /app\nCOPY package*.json ./\nRUN npm ci --production\nCOPY . .\nRUN npm run build\n\nFROM node:20-alpine\nWORKDIR /app\nCOPY --from=builder /app/dist ./dist\nCOPY --from=builder /app/node_modules ./node_modules\nEXPOSE 3000\nCMD [\"node\", \"dist/server.js\"]\n```\n\n## Best Practices\n\n1. Use **multi-stage builds** to reduce image size\n2. Order instructions from least to most frequently changed\n3. Use `.dockerignore` to exclude unnecessary files\n4. Pin specific versions for base images\n5. Run as non-root user",
                'quiz_json' => [
                    [
                        'question' => 'What is the purpose of multi-stage builds?',
                        'type' => 'multiple_choice',
                        'options' => [
                            'To run multiple containers',
                            'To reduce final image size',
                            'To enable parallel builds',
                            'To support multiple architectures',
                        ],
                        'correct_answer' => 'To reduce final image size',
                    ],
                ],
            ],
            [
                'title' => 'Docker Compose',
                'video_url' => 'https://www.youtube.com/watch?v=HG6yIjZapSA',
                'reading_md' => "# Docker Compose\n\nDocker Compose is a tool for defining and running multi-container applications.\n\n## docker-compose.yml\n\n```yaml\nversion: '3.8'\nservices:\n  web:\n    build: .\n    ports:\n      - \"3000:3000\"\n    depends_on:\n      - db\n      - redis\n    environment:\n      DATABASE_URL: postgres://user:pass@db:5432/app\n\n  db:\n    image: postgres:16-alpine\n    volumes:\n      - db_data:/var/lib/postgresql/data\n    environment:\n      POSTGRES_PASSWORD: pass\n\n  redis:\n    image: redis:7-alpine\n\nvolumes:\n  db_data:\n```\n\n## Commands\n\n```bash\ndocker compose up -d     # Start all services\ndocker compose logs -f   # Follow logs\ndocker compose down      # Stop and remove\n```",
            ],
            [
                'title' => 'Container Networking',
                'video_url' => 'https://www.youtube.com/watch?v=bKFMS5C4CG0',
                'reading_md' => "# Docker Networking\n\nDocker networking allows containers to communicate with each other and the outside world.\n\n## Network Types\n\n- **bridge** — Default network (isolated container network)\n- **host** — Container uses host network directly\n- **none** — No networking\n- **overlay** — Multi-host networking for Swarm\n\n## Custom Networks\n\n```bash\n# Create a network\ndocker network create my-app-net\n\n# Run containers on the network\ndocker run -d --name api --network my-app-net my-api\ndocker run -d --name db --network my-app-net postgres\n\n# api can reach db at hostname 'db'\n```",
            ],
            [
                'title' => 'Docker Security Best Practices',
                'video_url' => 'https://www.youtube.com/watch?v=JE2PJbbpjsM',
                'reading_md' => "# Container Security\n\n## Key Principles\n\n1. **Run as non-root** — Never run containers as root in production\n2. **Use minimal base images** — Alpine or distroless\n3. **Scan for vulnerabilities** — Use Trivy, Snyk, or Docker Scout\n4. **Read-only filesystems** — Mount filesystem as read-only where possible\n5. **Limit resources** — Set CPU and memory limits\n\n## Secure Dockerfile\n\n```dockerfile\nFROM node:20-alpine\nRUN addgroup -S appgroup && adduser -S appuser -G appgroup\nWORKDIR /app\nCOPY --chown=appuser:appgroup . .\nUSER appuser\nEXPOSE 3000\nCMD [\"node\", \"server.js\"]\n```",
                'quiz_json' => [
                    [
                        'question' => 'Why should containers not run as root?',
                        'type' => 'multiple_choice',
                        'options' => [
                            'Containers run faster as non-root',
                            'Root access is not available in containers',
                            'Root containers pose a security risk if compromised',
                            'Kubernetes requires non-root containers',
                        ],
                        'correct_answer' => 'Root containers pose a security risk if compromised',
                    ],
                ],
            ],
        ]);

        // ============================================================
        // COURSE 3: Helm Charts
        // ============================================================
        $helm = Module::updateOrCreate(
            ['slug' => 'helm-charts'],
            [
                'title' => 'Helm: The Kubernetes Package Manager',
                'description' => 'Learn to package, deploy, and manage Kubernetes applications using Helm charts. Master templates, values, dependencies, and chart repositories for repeatable deployments.',
                'category' => 'Kubernetes',
                'order_index' => 3,
                'is_published' => true,
            ]
        );

        $helmLab = Lab::updateOrCreate(
            ['title' => 'Helm Charts Lab', 'module_id' => $helm->id],
            [
                'description' => 'Practice creating and deploying Helm charts',
                'estimated_minutes' => 45,
                'ttl_minutes' => 90,
                'workbench_image' => 'codercom/code-server:latest',
                'is_published' => true,
            ]
        );

        $this->createLessons($helm, $helmLab, [
            [
                'title' => 'What is Helm?',
                'video_url' => 'https://www.youtube.com/watch?v=fy8SHvNZGeE',
                'reading_md' => "# Helm — The Kubernetes Package Manager\n\nHelm helps you manage Kubernetes applications. Helm Charts define, install, and upgrade even the most complex Kubernetes applications.\n\n## Key Concepts\n\n- **Chart** — A package of Kubernetes resources\n- **Release** — A running instance of a chart\n- **Repository** — A collection of charts\n- **Values** — Configuration for a chart\n\n## Why Helm?\n\n1. **Templating** — Reuse YAML with variables\n2. **Packaging** — Bundle related resources\n3. **Versioning** — Track deployments and rollback\n4. **Dependencies** — Manage chart dependencies",
            ],
            [
                'title' => 'Creating Your First Chart',
                'video_url' => 'https://www.youtube.com/watch?v=jUYNS90nq8U',
                'reading_md' => "# Creating a Helm Chart\n\n## Chart Structure\n\n```\nmy-app/\n├── Chart.yaml        # Chart metadata\n├── values.yaml       # Default configuration\n├── templates/\n│   ├── deployment.yaml\n│   ├── service.yaml\n│   ├── ingress.yaml\n│   ├── _helpers.tpl  # Template helpers\n│   └── NOTES.txt     # Post-install notes\n└── charts/           # Dependencies\n```\n\n## Commands\n\n```bash\nhelm create my-app           # Scaffold a chart\nhelm install my-release ./my-app  # Install\nhelm upgrade my-release ./my-app  # Upgrade\nhelm rollback my-release 1   # Rollback\nhelm uninstall my-release    # Uninstall\n```",
                'quiz_json' => [
                    [
                        'question' => 'What file contains default configuration values for a Helm chart?',
                        'type' => 'multiple_choice',
                        'options' => ['Chart.yaml', 'values.yaml', 'config.yaml', 'defaults.yaml'],
                        'correct_answer' => 'values.yaml',
                    ],
                ],
            ],
            [
                'title' => 'Helm Templates & Values',
                'video_url' => 'https://www.youtube.com/watch?v=DQk8HOVlumI',
                'reading_md' => "# Helm Templating\n\nHelm uses Go templates to generate Kubernetes manifests from charts.\n\n## Template Syntax\n\n```yaml\napiVersion: apps/v1\nkind: Deployment\nmetadata:\n  name: {{ .Release.Name }}-{{ .Chart.Name }}\nspec:\n  replicas: {{ .Values.replicaCount }}\n  template:\n    spec:\n      containers:\n      - name: {{ .Chart.Name }}\n        image: \"{{ .Values.image.repository }}:{{ .Values.image.tag }}\"\n        {{- if .Values.resources }}\n        resources:\n          {{- toYaml .Values.resources | nindent 10 }}\n        {{- end }}\n```\n\n## values.yaml\n\n```yaml\nreplicaCount: 3\nimage:\n  repository: nginx\n  tag: \"1.25\"\nresources:\n  limits:\n    cpu: 500m\n    memory: 128Mi\n```",
            ],
            [
                'title' => 'Chart Dependencies',
                'video_url' => 'https://www.youtube.com/watch?v=hWzFGGd_AiA',
                'reading_md' => "# Helm Dependencies\n\nCharts can depend on other charts. Dependencies are declared in `Chart.yaml`.\n\n```yaml\n# Chart.yaml\ndependencies:\n  - name: postgresql\n    version: \"12.x.x\"\n    repository: \"https://charts.bitnami.com/bitnami\"\n    condition: postgresql.enabled\n  - name: redis\n    version: \"17.x.x\"\n    repository: \"https://charts.bitnami.com/bitnami\"\n```\n\n## Dependency Commands\n\n```bash\nhelm dependency update    # Download dependencies\nhelm dependency build     # Rebuild charts/ directory\nhelm dependency list      # List dependencies\n```",
            ],
        ]);

        // ============================================================
        // COURSE 4: CI/CD with GitHub Actions
        // ============================================================
        $cicd = Module::updateOrCreate(
            ['slug' => 'github-actions-cicd'],
            [
                'title' => 'CI/CD with GitHub Actions',
                'description' => 'Build automated deployment pipelines with GitHub Actions. Learn to build, test, and deploy applications to Kubernetes, Azure, and AWS with industry-standard CI/CD practices.',
                'category' => 'DevOps',
                'order_index' => 4,
                'is_published' => true,
            ]
        );

        $this->createLessons($cicd, null, [
            [
                'title' => 'GitHub Actions Fundamentals',
                'video_url' => 'https://www.youtube.com/watch?v=R8_veQiYBjI',
                'reading_md' => <<<'MD'
# GitHub Actions

GitHub Actions is a CI/CD platform that allows you to automate your build, test, and deployment pipelines.

## Key Concepts

- **Workflow** — Automated process defined in YAML
- **Event** — A trigger (push, PR, schedule)
- **Job** — A set of steps that run on the same runner
- **Step** — An individual task (action or script)
- **Action** — A reusable unit of code

## Basic Workflow

```yaml
name: CI Pipeline
on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v4
    - uses: actions/setup-node@v4
      with:
        node-version: 20
    - run: npm ci
    - run: npm test
    - run: npm run build
```
MD,
            ],
            [
                'title' => 'Building Docker Images in CI',
                'video_url' => 'https://www.youtube.com/watch?v=yfBtjLxn_6k',
                'reading_md' => <<<'MD'
# Building & Pushing Docker Images

## Workflow for Docker Build

```yaml
jobs:
  build:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v4

    - uses: docker/login-action@v3
      with:
        registry: ghcr.io
        username: ${{ github.actor }}
        password: ${{ secrets.GITHUB_TOKEN }}

    - uses: docker/build-push-action@v5
      with:
        context: .
        push: true
        tags: ghcr.io/${{ github.repository }}:latest
```

## Multi-Architecture Builds

Use QEMU and buildx for ARM + AMD64:

```yaml
    - uses: docker/setup-qemu-action@v3
    - uses: docker/setup-buildx-action@v3
    - uses: docker/build-push-action@v5
      with:
        platforms: linux/amd64,linux/arm64
```
MD,
                'quiz_json' => [
                    [
                        'question' => 'Which GitHub Action is used to build and push Docker images?',
                        'type' => 'multiple_choice',
                        'options' => ['docker/build-push-action', 'actions/docker@v4', 'docker/compose-action', 'actions/build@v3'],
                        'correct_answer' => 'docker/build-push-action',
                    ],
                ],
            ],
            [
                'title' => 'Deploying to Kubernetes from CI',
                'video_url' => 'https://www.youtube.com/watch?v=X3F3El_yvFg',
                'reading_md' => <<<'MD'
# Kubernetes Deployment from CI/CD

## Deploy with kubectl

```yaml
jobs:
  deploy:
    runs-on: ubuntu-latest
    needs: build
    steps:
    - uses: azure/k8s-set-context@v3
      with:
        kubeconfig: ${{ secrets.KUBECONFIG }}

    - uses: azure/k8s-deploy@v4
      with:
        namespace: production
        manifests: k8s/
        images: ghcr.io/my-org/my-app:latest
```

## Deploy with Helm

```yaml
    - name: Deploy with Helm
      run: |
        helm upgrade --install my-app ./charts/my-app \
          --namespace production \
          --set image.tag=${{ github.sha }} \
          --wait --timeout 300s
```
MD,
            ],
            [
                'title' => 'Secrets Management in CI/CD',
                'video_url' => 'https://www.youtube.com/watch?v=dG_WfaMFHWo',
                'reading_md' => <<<'MD'
# Managing Secrets

## GitHub Secrets

Store sensitive values in GitHub repository or organization secrets:

- Repository Settings → Secrets and variables → Actions
- Reference with `${{ secrets.SECRET_NAME }}`

## Environment-Specific Secrets

```yaml
jobs:
  deploy-staging:
    environment: staging
    runs-on: ubuntu-latest
    steps:
    - run: echo "Deploying to ${{ vars.DEPLOY_URL }}"
      env:
        DB_PASSWORD: ${{ secrets.DB_PASSWORD }}

  deploy-production:
    environment: production
    needs: deploy-staging
    runs-on: ubuntu-latest
```

## Best Practices

1. Never hardcode secrets in workflows
2. Use environment-level secrets for isolation
3. Rotate secrets regularly
4. Use OIDC for cloud provider authentication
MD,
            ],
        ]);

        // ============================================================
        // COURSE 5: Cloud Security for Government
        // ============================================================
        $security = Module::updateOrCreate(
            ['slug' => 'gov-cloud-security'],
            [
                'title' => 'Cloud Security for Government',
                'description' => 'Understand security frameworks and compliance requirements for government cloud deployments. Cover FedRAMP, NIST 800-53, zero trust architecture, and container security for federal workloads.',
                'category' => 'Security',
                'order_index' => 5,
                'is_published' => true,
            ]
        );

        $this->createLessons($security, null, [
            [
                'title' => 'Introduction to FedRAMP',
                'video_url' => 'https://www.youtube.com/watch?v=BdrvYAr-hy0',
                'reading_md' => "# FedRAMP Overview\n\nThe **Federal Risk and Authorization Management Program** (FedRAMP) provides a standardized approach to security authorization for cloud products and services.\n\n## Impact Levels\n\n| Level | Classification | Controls |\n|-------|---------------|----------|\n| **Low** | Public data | 125 controls |\n| **Moderate** | CUI, PII | 325 controls |\n| **High** | Law enforcement, emergency services | 421 controls |\n\n## Authorization Process\n\n1. **Preparation** — Document system architecture and security\n2. **Assessment** — Third-party assessment organization (3PAO) audit\n3. **Authorization** — Joint Authorization Board (JAB) review\n4. **Continuous Monitoring** — Ongoing compliance",
                'quiz_json' => [
                    [
                        'question' => 'How many security controls does FedRAMP Moderate require?',
                        'type' => 'multiple_choice',
                        'options' => ['125', '225', '325', '421'],
                        'correct_answer' => '325',
                    ],
                ],
            ],
            [
                'title' => 'NIST 800-53 Security Controls',
                'video_url' => 'https://www.youtube.com/watch?v=MypMbhf2eUE',
                'reading_md' => "# NIST 800-53\n\n**NIST Special Publication 800-53** provides a catalog of security and privacy controls for federal information systems.\n\n## Control Families\n\n- **AC** — Access Control\n- **AU** — Audit and Accountability\n- **CA** — Assessment, Authorization, and Monitoring\n- **CM** — Configuration Management\n- **IA** — Identification and Authentication\n- **SC** — System and Communications Protection\n- **SI** — System and Information Integrity\n\n## Key Controls\n\n```\nAC-2: Account Management\n  → Manage system accounts, groups, roles\n  → Automated monitoring and removal\n\nAC-6: Least Privilege\n  → Users get minimum access needed\n  → Privileged access is audited\n\nSC-8: Transmission Confidentiality\n  → Encrypt data in transit (TLS 1.2+)\n  → Certificate management\n```",
            ],
            [
                'title' => 'Zero Trust Architecture',
                'video_url' => 'https://www.youtube.com/watch?v=bv-cSaFnu24',
                'reading_md' => "# Zero Trust Architecture\n\nZero Trust is based on the principle: **\"Never trust, always verify.\"**\n\n## Core Principles\n\n1. **Verify explicitly** — Always authenticate and authorize\n2. **Use least privilege** — Limit user access with JIT and JEA\n3. **Assume breach** — Minimize blast radius and segment access\n\n## Implementation Pillars\n\n```\n┌─────────────┐\n│  Identities │ ← MFA, RBAC, conditional access\n├─────────────┤\n│  Devices     │ ← Compliance checking, health validation\n├─────────────┤\n│  Networks    │ ← Microsegmentation, encrypted transport\n├─────────────┤\n│  Applications│ ← API security, runtime protection\n├─────────────┤\n│  Data        │ ← Classification, encryption, DLP\n├─────────────┤\n│  Infrastructure│ ← Container security, IaC scanning\n└─────────────┘\n```\n\n## EO 14028 Requirements\n\nExecutive Order 14028 directs federal agencies to adopt zero trust architecture by 2025.",
                'quiz_json' => [
                    [
                        'question' => 'What is the core principle of Zero Trust?',
                        'type' => 'multiple_choice',
                        'options' => [
                            'Trust internal networks',
                            'Never trust, always verify',
                            'Trust but verify',
                            'Trust after initial authentication',
                        ],
                        'correct_answer' => 'Never trust, always verify',
                    ],
                ],
            ],
            [
                'title' => 'Container Security in Federal Environments',
                'video_url' => 'https://www.youtube.com/watch?v=nrhxNNH5lt0',
                'reading_md' => "# Container Security\n\n## DISA STIG for Containers\n\nThe Defense Information Systems Agency (DISA) publishes Security Technical Implementation Guides (STIGs) for container platforms.\n\n## Key Requirements\n\n1. **Image provenance** — Only use images from trusted registries\n2. **Vulnerability scanning** — Scan all images before deployment\n3. **Runtime protection** — Monitor container behavior\n4. **Network policies** — Restrict pod-to-pod communication\n5. **Secrets management** — External secret stores, not environment variables\n\n## Pod Security Standards\n\n```yaml\napiVersion: v1\nkind: Namespace\nmetadata:\n  name: production\n  labels:\n    pod-security.kubernetes.io/enforce: restricted\n    pod-security.kubernetes.io/audit: restricted\n    pod-security.kubernetes.io/warn: restricted\n```",
            ],
        ]);

        // ============================================================
        // COURSE 6: Terraform Infrastructure as Code
        // ============================================================
        $terraform = Module::updateOrCreate(
            ['slug' => 'terraform-iac'],
            [
                'title' => 'Terraform: Infrastructure as Code',
                'description' => 'Learn to provision and manage cloud infrastructure using HashiCorp Terraform. Cover HCL syntax, state management, modules, providers, and CI/CD integration for repeatable infrastructure.',
                'category' => 'DevOps',
                'order_index' => 6,
                'is_published' => true,
            ]
        );

        $this->createLessons($terraform, null, [
            [
                'title' => 'Introduction to Infrastructure as Code',
                'video_url' => 'https://www.youtube.com/watch?v=tomUWcQ0P3k',
                'reading_md' => "# Infrastructure as Code (IaC)\n\nIaC is the practice of managing infrastructure through code instead of manual processes.\n\n## Benefits\n\n- **Version controlled** — Track changes like application code\n- **Repeatable** — Same infrastructure every time\n- **Auditable** — Know who changed what and when\n- **Testable** — Validate before applying\n\n## IaC Tools Comparison\n\n| Tool | Language | Approach | Cloud Support |\n|------|----------|----------|---------------|\n| **Terraform** | HCL | Declarative | Multi-cloud |\n| **CloudFormation** | JSON/YAML | Declarative | AWS only |\n| **Pulumi** | Python/Go/TS | Imperative | Multi-cloud |\n| **Bicep** | Bicep | Declarative | Azure only |",
            ],
            [
                'title' => 'Terraform Basics & HCL',
                'video_url' => 'https://www.youtube.com/watch?v=SLB_c_ayRMo',
                'reading_md' => "# Terraform & HCL\n\n## Basic Configuration\n\n```hcl\n# provider.tf\nterraform {\n  required_providers {\n    azurerm = {\n      source  = \"hashicorp/azurerm\"\n      version = \"~> 3.0\"\n    }\n  }\n}\n\nprovider \"azurerm\" {\n  features {}\n}\n\n# main.tf\nresource \"azurerm_resource_group\" \"example\" {\n  name     = \"rg-example\"\n  location = \"East US\"\n}\n\nresource \"azurerm_kubernetes_cluster\" \"aks\" {\n  name                = \"aks-example\"\n  location            = azurerm_resource_group.example.location\n  resource_group_name = azurerm_resource_group.example.name\n  dns_prefix          = \"aks-example\"\n\n  default_node_pool {\n    name       = \"default\"\n    node_count = 3\n    vm_size    = \"Standard_D2s_v3\"\n  }\n}\n```\n\n## Workflow\n\n```bash\nterraform init      # Initialize\nterraform plan      # Preview changes\nterraform apply     # Apply changes\nterraform destroy   # Tear down\n```",
                'quiz_json' => [
                    [
                        'question' => 'What command previews Terraform changes without applying them?',
                        'type' => 'multiple_choice',
                        'options' => ['terraform init', 'terraform plan', 'terraform apply', 'terraform preview'],
                        'correct_answer' => 'terraform plan',
                    ],
                ],
            ],
            [
                'title' => 'Terraform State Management',
                'video_url' => 'https://www.youtube.com/watch?v=fYGwMKStKr0',
                'reading_md' => "# State Management\n\nTerraform stores the state of your infrastructure in a **state file** (`terraform.tfstate`).\n\n## Remote State\n\nFor team collaboration, use remote state:\n\n```hcl\nterraform {\n  backend \"azurerm\" {\n    resource_group_name  = \"rg-terraform\"\n    storage_account_name = \"tfstateaccount\"\n    container_name       = \"tfstate\"\n    key                  = \"prod.terraform.tfstate\"\n  }\n}\n```\n\n## State Locking\n\nRemote backends support state locking to prevent concurrent modifications.\n\n## Best Practices\n\n1. **Never** store state locally in production\n2. Enable state encryption at rest\n3. Use workspaces for environment separation\n4. Backup state files regularly",
            ],
            [
                'title' => 'Terraform Modules',
                'video_url' => 'https://www.youtube.com/watch?v=E2e_0hbiSxE',
                'reading_md' => "# Terraform Modules\n\nModules are reusable packages of Terraform configuration.\n\n## Module Structure\n\n```\nmodules/\n└── aks-cluster/\n    ├── main.tf\n    ├── variables.tf\n    ├── outputs.tf\n    └── README.md\n```\n\n## Using a Module\n\n```hcl\nmodule \"aks\" {\n  source = \"./modules/aks-cluster\"\n\n  cluster_name    = \"prod-aks\"\n  resource_group  = \"rg-prod\"\n  node_count      = 5\n  vm_size         = \"Standard_D4s_v3\"\n  k8s_version     = \"1.28\"\n}\n\noutput \"kubeconfig\" {\n  value     = module.aks.kubeconfig\n  sensitive = true\n}\n```",
            ],
        ]);

        $this->command->info("✅ Created 6 courses with " . Lesson::count() . " total lessons!");
    }

    /**
     * Helper to create lessons for a module
     */
    private function createLessons(Module $module, ?Lab $lab, array $lessons): void
    {
        foreach ($lessons as $index => $data) {
            Lesson::updateOrCreate(
                ['module_id' => $module->id, 'title' => $data['title']],
                [
                    'lab_id' => $lab?->id,
                    'video_url' => $data['video_url'] ?? null,
                    'reading_md' => $data['reading_md'] ?? null,
                    'quiz_json' => $data['quiz_json'] ?? null,
                    'order_index' => $index + 1,
                    'is_published' => true,
                ]
            );
        }
    }
}
