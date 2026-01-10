<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $session->lab->title }} - Lab Runtime</title>
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #10b981;
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--bg-dark);
            color: var(--text);
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .runtime-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
        }

        .lab-title {
            font-size: 1rem;
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .timer {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .btn-stop {
            padding: 0.5rem 1rem;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }

        /* Split layout */
        .runtime-container {
            flex: 1;
            display: flex;
            overflow: hidden;
        }

        /* Left panel - Instructions */
        .instructions-panel {
            width: 400px;
            background: var(--bg-card);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .instructions-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
        }

        .steps-list {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .step-card {
            background: var(--bg-dark);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }

        .step-card.active {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .step-header {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .step-number {
            width: 24px;
            height: 24px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .step-type {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.1);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }

        .step-content {
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* Right panel - Workbench iframe */
        .workbench-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .workbench-status {
            padding: 2rem;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .workbench-iframe {
            flex: 1;
            width: 100%;
            border: none;
        }

        .error-message {
            color: #ef4444;
            margin-top: 1rem;
        }

        /* Resize handle */
        .resize-handle {
            width: 4px;
            background: var(--border);
            cursor: col-resize;
        }

        .resize-handle:hover {
            background: var(--primary);
        }
    </style>
</head>

<body>
    <header class="runtime-header">
        <div class="lab-title">{{ $session->lab->title }}</div>
        <div class="header-actions">
            <span class="timer" id="timer">Loading...</span>
            <button class="btn-stop" id="stop-btn">Stop Lab</button>
        </div>
    </header>

    <div class="runtime-container">
        <div class="instructions-panel" id="instructions-panel">
            <div class="instructions-header">Instructions</div>
            <div class="steps-list">
                @foreach($session->lab->steps as $step)
                    <div class="step-card {{ $loop->first ? 'active' : '' }}" data-step="{{ $loop->index }}">
                        <div class="step-header">
                            <div class="step-number">{{ $loop->iteration }}</div>
                            <span class="step-type">{{ $step->type }}</span>
                        </div>
                        <div class="step-content">
                            @if($step->type === 'instruction')
                                {!! nl2br(e($step->payload_json['content'] ?? '')) !!}
                            @elseif($step->type === 'task')
                                <strong>{{ $step->payload_json['title'] ?? 'Task' }}</strong>
                                <p>{{ $step->payload_json['description'] ?? '' }}</p>
                            @elseif($step->type === 'quiz')
                                <strong>{{ $step->payload_json['question'] ?? 'Quiz' }}</strong>
                            @else
                                {{ $step->payload_json['title'] ?? 'Step ' . $loop->iteration }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="resize-handle"></div>

        <div class="workbench-panel" id="workbench-panel">
            <div class="workbench-status" id="workbench-status">
                <div class="spinner"></div>
                <div>Provisioning your environment...</div>
                <div class="text-muted" id="status-text">Status: {{ $session->status }}</div>
                <div class="error-message" id="error-message" style="display: none;"></div>
            </div>
        </div>
    </div>

    <script>
        const SESSION_ID = '{{ $session->id }}';
        const EXPIRES_AT = new Date('{{ $session->expires_at->toIso8601String() }}');
        let pollInterval;
        let heartbeatInterval;

        // Timer update
        function updateTimer() {
            const now = new Date();
            const diff = EXPIRES_AT - now;

            if (diff <= 0) {
                document.getElementById('timer').textContent = 'Expired';
                return;
            }

            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            document.getElementById('timer').textContent = `${minutes}:${seconds.toString().padStart(2, '0')} remaining`;
        }

        // Poll for session status
        async function pollStatus() {
            try {
                const response = await fetch(`/api/lab-sessions/${SESSION_ID}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const data = await response.json();

                document.getElementById('status-text').textContent = `Status: ${data.status}`;

                if (data.status === 'running' && data.code_url) {
                    clearInterval(pollInterval);
                    showWorkbench(data.code_url);
                    startHeartbeat();
                } else if (data.status === 'error') {
                    clearInterval(pollInterval);
                    document.getElementById('error-message').textContent = data.error_message || 'An error occurred';
                    document.getElementById('error-message').style.display = 'block';
                    document.querySelector('.spinner').style.display = 'none';
                }
            } catch (error) {
                console.error('Poll error:', error);
            }
        }

        // Show workbench iframe
        function showWorkbench(url) {
            const panel = document.getElementById('workbench-panel');
            panel.innerHTML = `<iframe class="workbench-iframe" src="${url}"></iframe>`;
        }

        // Send heartbeat
        async function sendHeartbeat() {
            try {
                await fetch(`/api/lab-sessions/${SESSION_ID}/heartbeat`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
            } catch (error) {
                console.error('Heartbeat error:', error);
            }
        }

        function startHeartbeat() {
            heartbeatInterval = setInterval(sendHeartbeat, 60000);
        }

        // Stop lab
        document.getElementById('stop-btn').addEventListener('click', async function () {
            if (!confirm('Are you sure you want to stop this lab? All progress will be lost.')) {
                return;
            }

            try {
                await fetch(`/api/lab-sessions/${SESSION_ID}/stop`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                window.location.href = '/modules';
            } catch (error) {
                alert('Failed to stop lab');
            }
        });

        // Initialize
        updateTimer();
        setInterval(updateTimer, 1000);

        @if($session->status === 'running' && $session->code_url)
            showWorkbench('{{ $session->code_url }}');
            startHeartbeat();
        @else
            pollInterval = setInterval(pollStatus, 3000);
            pollStatus();
        @endif
    </script>
</body>

</html>