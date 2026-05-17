FROM codercom/code-server:4.96.1

USER root

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    ca-certificates \
    bash-completion \
    jq \
    git \
    unzip \
    gnupg \
    lsb-release \
    software-properties-common \
    python3 \
    python3-pip \
    python3-venv \
    sshpass \
    && rm -rf /var/lib/apt/lists/*

# Install kubectl
RUN curl -LO "https://dl.k8s.io/release/$(curl -sL https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl" \
    && install -o root -g root -m 0755 kubectl /usr/local/bin/kubectl \
    && rm kubectl \
    && kubectl completion bash > /etc/bash_completion.d/kubectl

# Install helm
RUN curl https://raw.githubusercontent.com/helm/helm/main/scripts/get-helm-3 | bash \
    && helm completion bash > /etc/bash_completion.d/helm

# Install k9s (terminal K8s UI)
RUN curl -sL $(curl -s https://api.github.com/repos/derailed/k9s/releases/latest | jq -r '.assets[] | select(.name | contains("Linux_amd64.tar.gz")) | .browser_download_url') \
    | tar xz -C /usr/local/bin k9s

# Install kubectx + kubens
RUN curl -sL $(curl -s https://api.github.com/repos/ahmetb/kubectx/releases/latest | jq -r '.assets[] | select(.name == "kubectx") | .browser_download_url') \
    -o /usr/local/bin/kubectx && chmod +x /usr/local/bin/kubectx \
    && curl -sL $(curl -s https://api.github.com/repos/ahmetb/kubectx/releases/latest | jq -r '.assets[] | select(.name == "kubens") | .browser_download_url') \
    -o /usr/local/bin/kubens && chmod +x /usr/local/bin/kubens

# Install Terraform
RUN curl -fsSL https://apt.releases.hashicorp.com/gpg | gpg --dearmor -o /usr/share/keyrings/hashicorp-archive-keyring.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/hashicorp-archive-keyring.gpg] https://apt.releases.hashicorp.com $(lsb_release -cs) main" \
    > /etc/apt/sources.list.d/hashicorp.list \
    && apt-get update && apt-get install -y terraform \
    && rm -rf /var/lib/apt/lists/*

# Install Azure CLI
RUN curl -sL https://aka.ms/InstallAzureCLIDeb | bash \
    && rm -rf /var/lib/apt/lists/*

# Install Ansible
RUN python3 -m pip install --no-cache-dir --break-system-packages \
    ansible \
    ansible-lint

# Remove sudo access — users should NOT have root
RUN rm -f /etc/sudoers.d/* \
    && sed -i '/coder/d' /etc/sudoers 2>/dev/null || true \
    && apt-get purge -y sudo 2>/dev/null || true

# Set up bash profile with useful aliases
RUN echo '\n\
# Kubernetes aliases\n\
alias k="kubectl"\n\
alias kgp="kubectl get pods"\n\
alias kgs="kubectl get svc"\n\
alias kgd="kubectl get deployments"\n\
alias kgn="kubectl get nodes"\n\
alias kga="kubectl get all"\n\
alias kaf="kubectl apply -f"\n\
alias kdel="kubectl delete"\n\
alias klog="kubectl logs"\n\
alias kex="kubectl exec -it"\n\
alias kns="kubens"\n\
alias kctx="kubectx"\n\
\n\
# Terraform aliases\n\
alias tf="terraform"\n\
alias tfi="terraform init"\n\
alias tfp="terraform plan"\n\
alias tfa="terraform apply"\n\
alias tfd="terraform destroy"\n\
\n\
# General aliases\n\
alias ll="ls -la"\n\
alias python="python3"\n\
alias pip="pip3"\n\
\n\
# Enable bash completion\n\
source /etc/bash_completion 2>/dev/null\n\
' >> /etc/bash.bashrc

# Switch to non-root user
USER 1000
WORKDIR /home/coder
