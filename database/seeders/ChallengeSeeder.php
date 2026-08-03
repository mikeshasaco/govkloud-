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
