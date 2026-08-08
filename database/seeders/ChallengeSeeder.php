<?php

namespace Database\Seeders;

use App\Models\Challenge;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $challenges = [
            // ═══════════════════════════════════════════════════
            // KUBERNETES — BEGINNER
            // ═══════════════════════════════════════════════════
            [
                'title' => 'Create a Basic Pod',
                'slug' => 'create-a-basic-pod',
                'category' => 'kubernetes',
                'difficulty' => 'beginner',
                'estimated_minutes' => 10,
                'order_index' => 1,
                'is_published' => true,
                'description' => "Create a Kubernetes Pod that runs an Nginx web server.\n\nYour pod should:\n- Be named \"web-server\"\n- Use the nginx:1.25 image\n- Expose port 80\n\nAfter writing the manifest, apply it and verify the pod is running.",
                'initial_files_json' => [
                    'pod.yaml' => "# Write your Pod manifest here\napiVersion: v1\nkind: Pod\nmetadata:\n  name: \nspec:\n  containers:\n  - name: \n    image: \n",
                ],
                'file_language_map' => ['pod.yaml' => 'yaml'],
                'command_flows_json' => [
                    'required_commands' => [
                        ['command' => 'kubectl apply -f pod.yaml', 'label' => 'Apply the pod manifest'],
                        ['command' => 'kubectl get pods', 'label' => 'Verify pod is running'],
                    ],
                    'validations' => [
                        'kubectl apply -f pod.yaml' => [
                            'must_contain' => ['nginx', 'web-server'],
                            'must_have_fields' => ['spec.containers'],
                        ],
                    ],
                ],
                'solution_files_json' => [
                    'pod.yaml' => "apiVersion: v1\nkind: Pod\nmetadata:\n  name: web-server\nspec:\n  containers:\n  - name: nginx\n    image: nginx:1.25\n    ports:\n    - containerPort: 80",
                ],
                'solution_explanation' => "A Pod is the smallest deployable unit in Kubernetes. The spec.containers array defines one or more containers. Each container needs a name and an image. The ports field is optional but documents which ports the container listens on.",
                'hints_json' => [
                    'Set metadata.name to "web-server"',
                    'The container image should be "nginx:1.25"',
                    'Add a ports section with containerPort: 80',
                ],
                'tags' => ['pods', 'nginx', 'yaml', 'basics'],
            ],

            // ═══════════════════════════════════════════════════
            // KUBERNETES — MEDIUM
            // ═══════════════════════════════════════════════════
            [
                'title' => 'Mount Secrets as Environment Variables',
                'slug' => 'mount-secrets-as-env-vars',
                'category' => 'kubernetes',
                'difficulty' => 'medium',
                'estimated_minutes' => 20,
                'order_index' => 2,
                'is_published' => true,
                'description' => "Create a Kubernetes Secret and mount it as environment variables in a Pod.\n\nStep 1: Create a secret named \"app-secret\" with a key \"API_KEY\" set to \"my-secret-key-123\"\nStep 2: Write a Pod manifest that mounts this secret as an environment variable\nStep 3: Verify the pod is running and the secret is mounted\n\nUse kubectl create secret for step 1 and a YAML manifest for step 2.",
                'initial_files_json' => [
                    'pod.yaml' => "# Create a pod that references the secret\napiVersion: v1\nkind: Pod\nmetadata:\n  name: app-pod\nspec:\n  containers:\n  - name: app\n    image: nginx:1.25\n    env:\n    - name: API_KEY\n      valueFrom:\n        # Add secretKeyRef here\n",
                ],
                'file_language_map' => ['pod.yaml' => 'yaml'],
                'command_flows_json' => [
                    'required_commands' => [
                        ['command' => 'kubectl create secret generic app-secret --from-literal=API_KEY=my-secret-key-123', 'label' => 'Create the secret "app-secret"'],
                        ['command' => 'kubectl apply -f pod.yaml', 'label' => 'Apply the pod with secret reference'],
                        ['command' => 'kubectl get pods', 'label' => 'Verify pod is running'],
                        ['command' => 'kubectl describe pod app-pod', 'label' => 'Confirm secret is mounted'],
                    ],
                    'validations' => [
                        'kubectl apply -f pod.yaml' => [
                            'must_contain' => ['secretKeyRef', 'app-secret'],
                        ],
                    ],
                ],
                'solution_files_json' => [
                    'pod.yaml' => "apiVersion: v1\nkind: Pod\nmetadata:\n  name: app-pod\nspec:\n  containers:\n  - name: app\n    image: nginx:1.25\n    env:\n    - name: API_KEY\n      valueFrom:\n        secretKeyRef:\n          name: app-secret\n          key: API_KEY",
                ],
                'solution_explanation' => "Secrets are mounted into pods via secretKeyRef under the env.valueFrom field. The name references the Secret object name, and the key references the specific data key within that Secret. This keeps sensitive values out of your pod manifests.",
                'hints_json' => [
                    'First create the secret using: kubectl create secret generic app-secret --from-literal=API_KEY=my-secret-key-123',
                    'Use secretKeyRef under valueFrom in the env section',
                    'The secretKeyRef needs both "name" (the secret name) and "key" (the data key)',
                ],
                'tags' => ['secrets', 'env', 'pods', 'security'],
            ],

            // ═══════════════════════════════════════════════════
            // KUBERNETES — HARD
            // ═══════════════════════════════════════════════════
            [
                'title' => 'Configure Liveness and Readiness Probes',
                'slug' => 'configure-liveness-readiness-probes',
                'category' => 'kubernetes',
                'difficulty' => 'hard',
                'estimated_minutes' => 25,
                'order_index' => 3,
                'is_published' => true,
                'description' => "Harden a Pod by adding both liveness and readiness probes.\n\nYour pod should:\n- Be named \"hardened-app\"\n- Use the nginx:1.25 image on port 80\n- Have a liveness probe (httpGet on port 80, path /, initialDelaySeconds: 15, periodSeconds: 10)\n- Have a readiness probe (httpGet on port 80, path /, initialDelaySeconds: 5, periodSeconds: 5)\n- Run as non-root (securityContext.runAsNonRoot: true is optional but good practice)\n\nApply the manifest and describe the pod to verify probes are configured.",
                'initial_files_json' => [
                    'pod.yaml' => "apiVersion: v1\nkind: Pod\nmetadata:\n  name: hardened-app\nspec:\n  containers:\n  - name: app\n    image: nginx:1.25\n    ports:\n    - containerPort: 80\n    # Add liveness probe here\n    # Add readiness probe here\n",
                ],
                'file_language_map' => ['pod.yaml' => 'yaml'],
                'command_flows_json' => [
                    'required_commands' => [
                        ['command' => 'kubectl apply -f pod.yaml', 'label' => 'Apply the hardened pod manifest'],
                        ['command' => 'kubectl describe pod hardened-app', 'label' => 'Verify probes are configured'],
                    ],
                    'validations' => [
                        'kubectl apply -f pod.yaml' => [
                            'must_contain' => ['livenessProbe', 'readinessProbe', 'httpGet'],
                        ],
                    ],
                ],
                'solution_files_json' => [
                    'pod.yaml' => "apiVersion: v1\nkind: Pod\nmetadata:\n  name: hardened-app\nspec:\n  containers:\n  - name: app\n    image: nginx:1.25\n    ports:\n    - containerPort: 80\n    livenessProbe:\n      httpGet:\n        path: /\n        port: 80\n      initialDelaySeconds: 15\n      periodSeconds: 10\n    readinessProbe:\n      httpGet:\n        path: /\n        port: 80\n      initialDelaySeconds: 5\n      periodSeconds: 5",
                ],
                'solution_explanation' => "Liveness probes tell Kubernetes when to restart a container (if the probe fails, the container is restarted). Readiness probes tell Kubernetes when a container is ready to accept traffic. httpGet probes make an HTTP request to the specified path and port. initialDelaySeconds gives the container time to start up before probing begins.",
                'hints_json' => [
                    'livenessProbe and readinessProbe go at the container level (same indent as image, ports)',
                    'Use httpGet with path: / and port: 80 for both probes',
                    'initialDelaySeconds controls the delay before the first probe; periodSeconds controls how often to probe',
                ],
                'tags' => ['probes', 'liveness', 'readiness', 'hardening', 'health-checks'],
            ],

            // ═══════════════════════════════════════════════════
            // DOCKER — BEGINNER
            // ═══════════════════════════════════════════════════
            [
                'title' => 'Write a Dockerfile for a Python App',
                'slug' => 'write-dockerfile-python-app',
                'category' => 'docker',
                'difficulty' => 'beginner',
                'estimated_minutes' => 10,
                'order_index' => 1,
                'is_published' => true,
                'description' => "Write a Dockerfile for a simple Python Flask application.\n\nYour Dockerfile should:\n- Use python:3.11-slim as the base image\n- Set the working directory to /app\n- Copy requirements.txt and install dependencies\n- Copy the application code\n- Expose port 5000\n- Set the CMD to run \"python app.py\"",
                'initial_files_json' => [
                    'Dockerfile' => "# Write your Dockerfile here\nFROM \n\n",
                    'app.py' => "from flask import Flask\napp = Flask(__name__)\n\n@app.route('/')\ndef hello():\n    return 'Hello, World!'\n\nif __name__ == '__main__':\n    app.run(host='0.0.0.0', port=5000)",
                    'requirements.txt' => "flask==3.0.0",
                ],
                'file_language_map' => [
                    'Dockerfile' => 'dockerfile',
                    'app.py' => 'python',
                    'requirements.txt' => 'text',
                ],
                'command_flows_json' => [
                    'required_commands' => [
                        ['command' => 'docker build -t flask-app .', 'label' => 'Build the Docker image'],
                    ],
                ],
                'solution_files_json' => [
                    'Dockerfile' => "FROM python:3.11-slim\n\nWORKDIR /app\n\nCOPY requirements.txt .\nRUN pip install --no-cache-dir -r requirements.txt\n\nCOPY . .\n\nEXPOSE 5000\n\nCMD [\"python\", \"app.py\"]",
                ],
                'solution_explanation' => "A Dockerfile defines how to build a container image. FROM sets the base image. WORKDIR sets the working directory. COPY copies files from the build context. RUN executes commands during the build. EXPOSE documents the port. CMD defines the default command to run.",
                'hints_json' => [
                    'Start with FROM python:3.11-slim',
                    'Use WORKDIR /app to set the working directory',
                    'Copy requirements.txt first and run pip install before copying the rest of the code (this optimizes Docker layer caching)',
                ],
                'tags' => ['dockerfile', 'python', 'flask', 'basics'],
            ],

            // ═══════════════════════════════════════════════════
            // DOCKER — MEDIUM
            // ═══════════════════════════════════════════════════
            [
                'title' => 'Multi-Stage Build Optimization',
                'slug' => 'multi-stage-build-optimization',
                'category' => 'docker',
                'difficulty' => 'medium',
                'estimated_minutes' => 20,
                'order_index' => 2,
                'is_published' => true,
                'description' => "Optimize a Dockerfile using multi-stage builds to reduce the final image size.\n\nCreate a multi-stage Dockerfile for a Go application:\n- Stage 1 (builder): Use golang:1.21 to build the binary\n- Stage 2 (production): Use alpine:3.18 as the minimal runtime\n- Copy only the compiled binary from the builder stage\n- The final image should NOT contain the Go toolchain",
                'initial_files_json' => [
                    'Dockerfile' => "# Stage 1: Build\nFROM golang:1.21 AS builder\n\n# Stage 2: Production\n# Use a minimal base image\n",
                    'main.go' => "package main\n\nimport (\n\t\"fmt\"\n\t\"net/http\"\n)\n\nfunc main() {\n\thttp.HandleFunc(\"/\", func(w http.ResponseWriter, r *http.Request) {\n\t\tfmt.Fprintf(w, \"Hello from Go!\")\n\t})\n\thttp.ListenAndServe(\":8080\", nil)\n}",
                ],
                'file_language_map' => ['Dockerfile' => 'dockerfile', 'main.go' => 'go'],
                'command_flows_json' => [
                    'required_commands' => [
                        ['command' => 'docker build -t go-app .', 'label' => 'Build the multi-stage image'],
                    ],
                ],
                'solution_files_json' => [
                    'Dockerfile' => "# Stage 1: Build\nFROM golang:1.21 AS builder\nWORKDIR /app\nCOPY main.go .\nRUN CGO_ENABLED=0 GOOS=linux go build -o server main.go\n\n# Stage 2: Production\nFROM alpine:3.18\nWORKDIR /app\nCOPY --from=builder /app/server .\nEXPOSE 8080\nCMD [\"./server\"]",
                ],
                'solution_explanation' => "Multi-stage builds use multiple FROM statements. The first stage compiles the code, the second stage copies only the binary. This reduces the final image from ~800MB (golang image) to ~10MB (alpine + binary). CGO_ENABLED=0 ensures a statically linked binary that works on Alpine.",
                'hints_json' => [
                    'Name the first stage with AS builder',
                    'Use COPY --from=builder to copy the binary from the build stage',
                    'Set CGO_ENABLED=0 to create a statically linked binary for Alpine',
                ],
                'tags' => ['multi-stage', 'optimization', 'golang', 'alpine'],
            ],

            // ═══════════════════════════════════════════════════
            // DOCKER — HARD
            // ═══════════════════════════════════════════════════
            [
                'title' => 'Build a Docker Compose Stack',
                'slug' => 'build-docker-compose-stack',
                'category' => 'docker',
                'difficulty' => 'hard',
                'estimated_minutes' => 30,
                'order_index' => 3,
                'is_published' => true,
                'description' => "Create a docker-compose.yml that defines a three-service stack:\n\n1. **web** - Nginx reverse proxy on port 80\n2. **app** - Python Flask app on port 5000 (internal only)\n3. **db** - PostgreSQL 15 with a persistent volume\n\nRequirements:\n- All services should be on a custom network called \"app-network\"\n- The db service should have a named volume \"pgdata\"\n- The app service should depend on the db service\n- Use environment variables for database credentials",
                'initial_files_json' => [
                    'docker-compose.yml' => "version: '3.8'\n\nservices:\n  # Define your services here\n\nvolumes:\n  # Define named volumes\n\nnetworks:\n  # Define custom network\n",
                ],
                'file_language_map' => ['docker-compose.yml' => 'yaml'],
                'command_flows_json' => [
                    'required_commands' => [
                        ['command' => 'cat docker-compose.yml', 'label' => 'Review your compose file'],
                    ],
                ],
                'solution_files_json' => [
                    'docker-compose.yml' => "version: '3.8'\n\nservices:\n  web:\n    image: nginx:alpine\n    ports:\n      - \"80:80\"\n    depends_on:\n      - app\n    networks:\n      - app-network\n\n  app:\n    image: python:3.11-slim\n    command: python app.py\n    environment:\n      - DB_HOST=db\n      - DB_PORT=5432\n      - DB_NAME=myapp\n      - DB_USER=postgres\n      - DB_PASSWORD=secret\n    depends_on:\n      - db\n    networks:\n      - app-network\n\n  db:\n    image: postgres:15\n    environment:\n      - POSTGRES_DB=myapp\n      - POSTGRES_USER=postgres\n      - POSTGRES_PASSWORD=secret\n    volumes:\n      - pgdata:/var/lib/postgresql/data\n    networks:\n      - app-network\n\nvolumes:\n  pgdata:\n\nnetworks:\n  app-network:\n    driver: bridge",
                ],
                'solution_explanation' => "Docker Compose defines multi-container applications. Services communicate via the custom network using service names as hostnames. Named volumes persist data across container restarts. depends_on controls startup order. Environment variables configure the database connection.",
                'hints_json' => [
                    'Define three services: web, app, db under the services key',
                    'Use depends_on to ensure db starts before app, and app before web',
                    'Create a named volume "pgdata" and mount it to /var/lib/postgresql/data in the db service',
                ],
                'tags' => ['docker-compose', 'networking', 'volumes', 'postgresql'],
            ],

            // ═══════════════════════════════════════════════════
            // TERRAFORM — BEGINNER
            // ═══════════════════════════════════════════════════
            [
                'title' => 'Define a Local File Resource',
                'slug' => 'define-local-file-resource',
                'category' => 'terraform',
                'difficulty' => 'beginner',
                'estimated_minutes' => 10,
                'order_index' => 1,
                'is_published' => true,
                'description' => "Create a Terraform configuration that uses the local provider to create a file.\n\nYour configuration should:\n- Define a resource of type \"local_file\" named \"hello\"\n- Set the filename to \"hello.txt\"\n- Set the content to \"Hello, Terraform!\"\n\nRun terraform init, then terraform plan to see what will be created.",
                'initial_files_json' => [
                    'main.tf' => "# Define a local_file resource\nresource \"local_file\" \"hello\" {\n  # Add filename and content here\n}\n",
                ],
                'file_language_map' => ['main.tf' => 'hcl'],
                'command_flows_json' => [
                    'required_commands' => [
                        ['command' => 'terraform init', 'label' => 'Initialize Terraform'],
                        ['command' => 'terraform plan', 'label' => 'Preview the execution plan'],
                    ],
                ],
                'solution_files_json' => [
                    'main.tf' => "resource \"local_file\" \"hello\" {\n  filename = \"hello.txt\"\n  content  = \"Hello, Terraform!\"\n}",
                ],
                'solution_explanation' => "Terraform resources follow the pattern: resource \"TYPE\" \"NAME\" { ... }. The local_file resource creates a file on disk. The filename attribute sets the path and content sets what goes in the file. terraform init downloads the required provider, and terraform plan shows what will happen without making changes.",
                'hints_json' => [
                    'The resource type is "local_file" and the name is "hello"',
                    'Use filename = "hello.txt" inside the resource block',
                    'Use content = "Hello, Terraform!" to set the file content',
                ],
                'tags' => ['terraform', 'local-provider', 'resources', 'basics'],
            ],

            // ═══════════════════════════════════════════════════
            // TERRAFORM — MEDIUM
            // ═══════════════════════════════════════════════════
            [
                'title' => 'Create Variables and Outputs',
                'slug' => 'terraform-variables-and-outputs',
                'category' => 'terraform',
                'difficulty' => 'medium',
                'estimated_minutes' => 15,
                'order_index' => 2,
                'is_published' => true,
                'description' => "Refactor a Terraform configuration to use variables and outputs.\n\nCreate three files:\n1. **variables.tf** - Define a variable \"environment\" (string, default \"dev\") and \"app_name\" (string, no default)\n2. **main.tf** - Use the variables in a local_file resource to create a config file\n3. **outputs.tf** - Output the filename and a computed \"full_name\" combining app_name and environment",
                'initial_files_json' => [
                    'variables.tf' => "# Define your variables here\nvariable \"environment\" {\n  # Add type, description, and default\n}\n\nvariable \"app_name\" {\n  # Add type and description (no default)\n}\n",
                    'main.tf' => "resource \"local_file\" \"config\" {\n  filename = \"config.txt\"\n  content  = \"App: ??? Environment: ???\"\n}\n",
                    'outputs.tf' => "# Define your outputs here\n",
                ],
                'file_language_map' => [
                    'variables.tf' => 'hcl',
                    'main.tf' => 'hcl',
                    'outputs.tf' => 'hcl',
                ],
                'command_flows_json' => [
                    'required_commands' => [
                        ['command' => 'terraform init', 'label' => 'Initialize Terraform'],
                        ['command' => 'terraform validate', 'label' => 'Validate configuration'],
                        ['command' => 'terraform plan', 'label' => 'Preview the plan'],
                    ],
                ],
                'solution_files_json' => [
                    'variables.tf' => "variable \"environment\" {\n  type        = string\n  description = \"The deployment environment\"\n  default     = \"dev\"\n}\n\nvariable \"app_name\" {\n  type        = string\n  description = \"The application name\"\n}",
                    'main.tf' => "resource \"local_file\" \"config\" {\n  filename = \"\${var.app_name}-\${var.environment}.txt\"\n  content  = \"App: \${var.app_name} Environment: \${var.environment}\"\n}",
                    'outputs.tf' => "output \"filename\" {\n  value = local_file.config.filename\n}\n\noutput \"full_name\" {\n  value = \"\${var.app_name}-\${var.environment}\"\n}",
                ],
                'solution_explanation' => "Variables make Terraform configs reusable. Use var.name to reference them. Variables can have types, descriptions, defaults, and validation rules. Outputs expose values after apply — useful for passing data between modules or displaying results.",
                'hints_json' => [
                    'Variables use: variable "name" { type = string, default = "value" }',
                    'Reference variables with ${var.variable_name} in strings',
                    'Outputs use: output "name" { value = expression }',
                ],
                'tags' => ['variables', 'outputs', 'interpolation', 'hcl'],
            ],

            // ═══════════════════════════════════════════════════
            // TERRAFORM — HARD
            // ═══════════════════════════════════════════════════
            [
                'title' => 'Build a Terraform Module',
                'slug' => 'build-terraform-module',
                'category' => 'terraform',
                'difficulty' => 'hard',
                'estimated_minutes' => 30,
                'order_index' => 3,
                'is_published' => true,
                'description' => "Create a reusable Terraform module structure.\n\nBuild a module that creates a \"server config\" with the following structure:\n\n1. **main.tf** (root) - Calls the module twice: once for \"web\" and once for \"api\"\n2. **modules/server-config/main.tf** - The module that creates a local_file with server info\n3. **modules/server-config/variables.tf** - Module input variables (server_name, port, environment)\n4. **modules/server-config/outputs.tf** - Module outputs (config_path)\n\nThe module should create a file named \"{server_name}-{environment}.conf\" with the server configuration.",
                'initial_files_json' => [
                    'main.tf' => "# Root module - call the server-config module\nmodule \"web_server\" {\n  source = \"./modules/server-config\"\n  # Pass variables\n}\n\nmodule \"api_server\" {\n  source = \"./modules/server-config\"\n  # Pass variables\n}\n",
                    'modules/server-config/main.tf' => "# Module implementation\nresource \"local_file\" \"config\" {\n  # Create the config file\n}\n",
                    'modules/server-config/variables.tf' => "# Module input variables\n",
                    'modules/server-config/outputs.tf' => "# Module outputs\n",
                ],
                'file_language_map' => [
                    'main.tf' => 'hcl',
                    'modules/server-config/main.tf' => 'hcl',
                    'modules/server-config/variables.tf' => 'hcl',
                    'modules/server-config/outputs.tf' => 'hcl',
                ],
                'command_flows_json' => [
                    'required_commands' => [
                        ['command' => 'terraform init', 'label' => 'Initialize with modules'],
                        ['command' => 'terraform plan', 'label' => 'Preview module resources'],
                    ],
                ],
                'solution_files_json' => [
                    'main.tf' => "module \"web_server\" {\n  source      = \"./modules/server-config\"\n  server_name = \"web\"\n  port        = 80\n  environment = \"production\"\n}\n\nmodule \"api_server\" {\n  source      = \"./modules/server-config\"\n  server_name = \"api\"\n  port        = 3000\n  environment = \"production\"\n}",
                    'modules/server-config/main.tf' => "resource \"local_file\" \"config\" {\n  filename = \"\${var.server_name}-\${var.environment}.conf\"\n  content  = <<-EOT\n    server_name = \${var.server_name}\n    port        = \${var.port}\n    environment = \${var.environment}\n  EOT\n}",
                    'modules/server-config/variables.tf' => "variable \"server_name\" {\n  type        = string\n  description = \"Name of the server\"\n}\n\nvariable \"port\" {\n  type        = number\n  description = \"Port number\"\n}\n\nvariable \"environment\" {\n  type        = string\n  description = \"Deployment environment\"\n  default     = \"dev\"\n}",
                    'modules/server-config/outputs.tf' => "output \"config_path\" {\n  value       = local_file.config.filename\n  description = \"Path to the generated config file\"\n}",
                ],
                'solution_explanation' => "Modules are Terraform's way of creating reusable infrastructure components. A module has its own variables (inputs), resources, and outputs. The root module calls child modules with the module block, passing values via attributes that match the module's variable names. This enables DRY infrastructure code.",
                'hints_json' => [
                    'The module block needs source, plus any required variables as attributes',
                    'Module variables are defined in variables.tf inside the module directory',
                    'Use var.variable_name inside the module to reference its inputs',
                ],
                'tags' => ['modules', 'reusability', 'structure', 'advanced'],
            ],

            // ═══════════════════════════════════════════════════
            // REAL-CLUSTER: TROUBLESHOOT (Beginner)
            // ═══════════════════════════════════════════════════
            [
                'title' => 'Fix the CrashLooping Pod',
                'slug' => 'fix-the-crashlooping-pod',
                'category' => 'kubernetes',
                'difficulty' => 'beginner',
                'problem_type' => 'troubleshoot',
                'requires_cluster' => true,
                'estimated_minutes' => 10,
                'time_limit_minutes' => 30,
                'order_index' => 10,
                'points' => 15,
                'is_published' => true,
                'description' => "A pod named **web-app** has been deployed but is stuck in CrashLoopBackOff.\n\nYour task:\n1. Investigate why the pod is failing\n2. Fix the issue so the pod reaches **Running** status\n3. Submit to verify your fix\n\nHint: Start by describing the pod to see the error events.",
                'scenario_manifests_json' => [
                    "apiVersion: v1\nkind: Pod\nmetadata:\n  name: web-app\n  labels:\n    app: web-app\nspec:\n  containers:\n  - name: nginx\n    image: nginx:99.99\n    ports:\n    - containerPort: 80",
                ],
                'initial_files_json' => [
                    'fix.yaml' => "# Investigate the pod, then write a corrected manifest here\n# Apply it with: kubectl apply -f fix.yaml\napiVersion: v1\nkind: Pod\nmetadata:\n  name: web-app\n  labels:\n    app: web-app\nspec:\n  containers:\n  - name: nginx\n    image:       # ← Fix the image tag\n    ports:\n    - containerPort: 80\n",
                ],
                'file_language_map' => ['fix.yaml' => 'yaml'],
                'validation_rules_json' => [
                    [
                        'type' => 'pod_status',
                        'name' => 'web-app',
                        'namespace' => 'default',
                        'expected_status' => 'Running',
                        'description' => 'Pod "web-app" should be in Running status',
                    ],
                    [
                        'type' => 'container_image',
                        'pod_name' => 'web-app',
                        'namespace' => 'default',
                        'expected_image' => 'nginx:1.25',
                        'description' => 'Pod should use a valid nginx image (nginx:1.25)',
                    ],
                ],
                'solution_files_json' => [
                    'fix.yaml' => "apiVersion: v1\nkind: Pod\nmetadata:\n  name: web-app\n  labels:\n    app: web-app\nspec:\n  containers:\n  - name: nginx\n    image: nginx:1.25\n    ports:\n    - containerPort: 80",
                ],
                'solution_explanation' => "The pod was using image \"nginx:99.99\" which doesn't exist, causing ImagePullBackOff/CrashLoopBackOff. The fix is to delete the broken pod and re-create it with a valid image tag like nginx:1.25.\n\nDiagnostic commands:\n- kubectl describe pod web-app → shows Events with \"ImagePullBackOff\"\n- kubectl get pods → shows CrashLoopBackOff status\n\nFix:\n- kubectl delete pod web-app\n- kubectl apply -f fix.yaml (with corrected image)",
                'hints_json' => [
                    'Run "kubectl describe pod web-app" and look at the Events section',
                    'The image tag "nginx:99.99" does not exist. Use a real tag like "nginx:1.25"',
                    'You need to delete the broken pod first: "kubectl delete pod web-app", then apply the fixed YAML',
                ],
                'tags' => ['troubleshoot', 'pods', 'crashloop', 'image-pull', 'real-cluster'],
            ],

            // ═══════════════════════════════════════════════════
            // REAL-CLUSTER: BUILD (Medium)
            // ═══════════════════════════════════════════════════
            [
                'title' => 'Deploy a Scalable Web App',
                'slug' => 'deploy-scalable-web-app',
                'category' => 'kubernetes',
                'difficulty' => 'medium',
                'problem_type' => 'build',
                'requires_cluster' => true,
                'estimated_minutes' => 15,
                'time_limit_minutes' => 45,
                'order_index' => 11,
                'points' => 25,
                'is_published' => true,
                'description' => "Create a production-ready deployment with a Service.\n\nYour task:\n1. Create a **Deployment** named \"api-server\" with:\n   - 3 replicas\n   - Image: nginx:1.25\n   - Container port: 80\n   - Labels: app=api-server\n2. Create a **Service** named \"api-service\" that:\n   - Selects pods with label app=api-server\n   - Exposes port 80\n   - Type: ClusterIP\n3. Verify all 3 replicas are running\n4. Submit to validate",
                'scenario_manifests_json' => [],
                'initial_files_json' => [
                    'deployment.yaml' => "# Create your Deployment here\napiVersion: apps/v1\nkind: Deployment\nmetadata:\n  name: api-server\nspec:\n  replicas:     # How many?\n  selector:\n    matchLabels:\n      app: api-server\n  template:\n    metadata:\n      labels:\n        app: api-server\n    spec:\n      containers:\n      - name: nginx\n        image:          # Which image?\n        ports:\n        - containerPort: 80\n",
                    'service.yaml' => "# Create your Service here\napiVersion: v1\nkind: Service\nmetadata:\n  name: api-service\nspec:\n  selector:\n    # Which pods?\n  ports:\n  - port: 80\n    targetPort: 80\n",
                ],
                'file_language_map' => ['deployment.yaml' => 'yaml', 'service.yaml' => 'yaml'],
                'validation_rules_json' => [
                    [
                        'type' => 'resource_exists',
                        'kind' => 'Deployment',
                        'name' => 'api-server',
                        'namespace' => 'default',
                        'description' => 'Deployment "api-server" should exist',
                    ],
                    [
                        'type' => 'replica_count',
                        'kind' => 'deployment',
                        'name' => 'api-server',
                        'namespace' => 'default',
                        'expected' => 3,
                        'description' => 'Deployment should have 3 ready replicas',
                    ],
                    [
                        'type' => 'resource_exists',
                        'kind' => 'Service',
                        'name' => 'api-service',
                        'namespace' => 'default',
                        'description' => 'Service "api-service" should exist',
                    ],
                    [
                        'type' => 'endpoints_populated',
                        'service_name' => 'api-service',
                        'namespace' => 'default',
                        'description' => 'Service "api-service" should have active endpoints',
                    ],
                ],
                'solution_files_json' => [
                    'deployment.yaml' => "apiVersion: apps/v1\nkind: Deployment\nmetadata:\n  name: api-server\nspec:\n  replicas: 3\n  selector:\n    matchLabels:\n      app: api-server\n  template:\n    metadata:\n      labels:\n        app: api-server\n    spec:\n      containers:\n      - name: nginx\n        image: nginx:1.25\n        ports:\n        - containerPort: 80",
                    'service.yaml' => "apiVersion: v1\nkind: Service\nmetadata:\n  name: api-service\nspec:\n  selector:\n    app: api-server\n  ports:\n  - port: 80\n    targetPort: 80",
                ],
                'solution_explanation' => "A Deployment manages a set of identical pods. The selector.matchLabels must match the template.metadata.labels so the Deployment knows which pods it owns. The Service uses the same label selector to route traffic to all matching pods. With 3 replicas, Kubernetes ensures exactly 3 pods are always running.",
                'hints_json' => [
                    'Set spec.replicas to 3 in the Deployment',
                    'Make sure the Service selector matches the pod labels: app: api-server',
                    'Apply both files: kubectl apply -f deployment.yaml -f service.yaml',
                ],
                'tags' => ['deployment', 'service', 'replicas', 'scaling', 'real-cluster'],
            ],

            // ═══════════════════════════════════════════════════
            // REAL-CLUSTER: SCENARIO (Hard)
            // ═══════════════════════════════════════════════════
            [
                'title' => 'Debug the Broken Service Connection',
                'slug' => 'debug-broken-service-connection',
                'category' => 'kubernetes',
                'difficulty' => 'hard',
                'problem_type' => 'scenario',
                'requires_cluster' => true,
                'estimated_minutes' => 20,
                'time_limit_minutes' => 60,
                'order_index' => 12,
                'points' => 40,
                'is_published' => true,
                'description' => "A team reports their frontend app can't connect to the backend API.\n\nBoth pods are deployed and running, but the frontend gets connection errors when trying to reach the backend via the Service.\n\n**Your investigation should:**\n1. Check the status of all pods and services\n2. Identify why the Service isn't routing to the backend pod\n3. Fix the issue so the Service correctly routes to the backend\n4. Submit to validate\n\nThere are multiple resources deployed — look carefully at the labels and selectors.",
                'scenario_manifests_json' => [
                    // Backend pod with correct labels
                    "apiVersion: v1\nkind: Pod\nmetadata:\n  name: backend-api\n  labels:\n    app: backend\n    tier: api\nspec:\n  containers:\n  - name: api\n    image: nginx:1.25\n    ports:\n    - containerPort: 80",
                    // Service with WRONG selector (mismatched label)
                    "apiVersion: v1\nkind: Service\nmetadata:\n  name: backend-service\nspec:\n  selector:\n    app: backend-api\n    tier: api\n  ports:\n  - port: 80\n    targetPort: 80",
                    // Frontend pod that tries to connect
                    "apiVersion: v1\nkind: Pod\nmetadata:\n  name: frontend-app\n  labels:\n    app: frontend\nspec:\n  containers:\n  - name: frontend\n    image: nginx:1.25\n    ports:\n    - containerPort: 80",
                ],
                'initial_files_json' => [
                    'notes.md' => "# Investigation Notes\n# Use this space to track what you find\n#\n# Useful commands:\n# kubectl get pods -o wide\n# kubectl get svc\n# kubectl get endpoints\n# kubectl describe svc backend-service\n# kubectl describe pod backend-api\n",
                ],
                'file_language_map' => ['notes.md' => 'markdown'],
                'validation_rules_json' => [
                    [
                        'type' => 'pod_status',
                        'name' => 'backend-api',
                        'namespace' => 'default',
                        'expected_status' => 'Running',
                        'description' => 'Backend pod should be Running',
                    ],
                    [
                        'type' => 'pod_status',
                        'name' => 'frontend-app',
                        'namespace' => 'default',
                        'expected_status' => 'Running',
                        'description' => 'Frontend pod should be Running',
                    ],
                    [
                        'type' => 'endpoints_populated',
                        'service_name' => 'backend-service',
                        'namespace' => 'default',
                        'description' => 'Service "backend-service" should have active endpoints (routing to backend pod)',
                    ],
                ],
                'solution_files_json' => [
                    'fix-service.yaml' => "apiVersion: v1\nkind: Service\nmetadata:\n  name: backend-service\nspec:\n  selector:\n    app: backend        # was: backend-api (wrong!)\n    tier: api\n  ports:\n  - port: 80\n    targetPort: 80",
                ],
                'solution_explanation' => "The Service selector had app: backend-api but the pod label is app: backend. This mismatch meant the Service had no endpoints (kubectl get endpoints backend-service shows <none>).\n\nThe fix is to update the Service selector to match the pod labels:\n- selector.app: backend (not backend-api)\n- selector.tier: api (this was already correct)\n\nDiagnostic flow:\n1. kubectl get endpoints backend-service → shows <none>\n2. kubectl describe svc backend-service → shows selector\n3. kubectl get pod backend-api --show-labels → shows actual labels\n4. Compare selectors vs labels → mismatch found\n5. kubectl apply -f fix-service.yaml → endpoints populate",
                'hints_json' => [
                    'Run "kubectl get endpoints backend-service" — what do you see?',
                    'Compare the Service selector (kubectl describe svc backend-service) with the pod labels (kubectl get pod backend-api --show-labels)',
                    'The Service selector says "app: backend-api" but the pod label is "app: backend" — fix the selector',
                ],
                'tags' => ['scenario', 'services', 'selectors', 'labels', 'debugging', 'networking', 'real-cluster'],
            ],
        ];

        foreach ($challenges as $data) {
            Challenge::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        $this->command->info('Seeded ' . count($challenges) . ' challenges.');
    }
}
