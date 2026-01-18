@extends('layouts.govkloud')

@section('title', $lesson->title . ' - GovKloud Labs')

@push('styles')
    <style>
        /* Split-screen layout */
        .split-container {
            display: flex;
            height: calc(100vh - 60px);
            overflow: hidden;
        }

        .lesson-panel {
            width: 40%;
            min-width: 320px;
            max-width: 600px;
            background: var(--bg-main);
            border-right: 1px solid var(--border);
            overflow-y: auto;
            padding: 1.5rem;
        }

        .lab-panel {
            flex: 1;
            background: #1e1e1e;
            position: relative;
        }

        .lab-panel iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Lab Header Bar */
        .lab-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 44px;
            background: rgba(15, 23, 42, 0.95);
            border-bottom: 1px solid rgba(210, 180, 140, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            z-index: 20;
            backdrop-filter: blur(10px);
        }

        .lab-header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .lab-status-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.75rem;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 50px;
            font-size: 0.75rem;
            color: #10b981;
        }

        .lab-status-badge.provisioning {
            background: rgba(251, 191, 36, 0.15);
            border-color: rgba(251, 191, 36, 0.3);
            color: #fbbf24;
        }

        .lab-status-badge .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .lab-timer {
            font-size: 0.8rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-stop {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
            padding: 0.4rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-stop:hover {
            background: rgba(239, 68, 68, 0.25);
            border-color: #ef4444;
        }

        .btn-stop:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Adjust iframe for header */
        .lab-content {
            position: absolute;
            top: 44px;
            left: 0;
            right: 0;
            bottom: 0;
        }

        /* Session status overlay */
        .session-status {
            position: absolute;
            top: 44px;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.95);
            color: white;
            z-index: 10;
        }

        .session-status.ready {
            display: none;
        }

        .session-status.stopped {
            display: flex;
        }

        .session-status .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(210, 180, 140, 0.3);
            border-top: 4px solid #D2B48C;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1.5rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .session-status h3 {
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
        }

        .session-status p {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .session-status .btn-restart {
            background: linear-gradient(135deg, #D2B48C, #C4A77D);
            color: #0f172a;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        /* Lesson content styles */
        .lesson-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .lesson-header .breadcrumb {
            margin-bottom: 1rem;
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: var(--radius);
            background: var(--bg-card);
            margin-bottom: 1.5rem;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .reading-content {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 1.5rem;
            line-height: 1.8;
        }

        .reading-content h1,
        .reading-content h2,
        .reading-content h3 {
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .reading-content p {
            margin-bottom: 1rem;
        }

        .reading-content code {
            background: rgba(210, 180, 140, 0.15);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Fira Code', monospace;
        }

        .reading-content pre {
            background: #0d1117;
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 1rem 0;
        }

        .lesson-nav {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        /* Idle warning toast */
        .idle-warning {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: rgba(251, 191, 36, 0.95);
            color: #0f172a;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            display: none;
            animation: slideIn 0.3s ease;
        }

        .idle-warning.show {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        @keyframes slideIn {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .split-container {
                flex-direction: column;
                height: auto;
            }

            .lesson-panel {
                width: 100%;
                max-width: none;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .lab-panel {
                height: 60vh;
                min-height: 400px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="split-container">
        <!-- Left Panel: Lesson Content -->
        <div class="lesson-panel">
            <div class="lesson-header">
                <div class="breadcrumb">
                    <a href="{{ route('courses.index') }}">Modules</a>
                    <span>/</span>
                    <a href="{{ route('courses.show', $module->slug) }}">{{ $module->title }}</a>
                    <span>/</span>
                    <span>{{ $lesson->title }}</span>
                </div>
                <h1>{{ $lesson->title }}</h1>
            </div>

            @if($lesson->video_url)
                <div class="video-container">
                    <iframe src="{{ $lesson->video_url }}" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            @endif

            @if($lesson->reading_md)
                <div class="reading-content">
                    {!! Str::markdown($lesson->reading_md) !!}
                </div>
            @endif

            <div class="lesson-nav">
                @if($prevLesson)
                    <a href="{{ route('lessons.show', [$module->slug, $prevLesson->slug]) }}" class="btn btn-secondary">
                        ← {{ Str::limit($prevLesson->title, 20) }}
                    </a>
                @else
                    <div></div>
                @endif

                @if($nextLesson)
                    <a href="{{ route('lessons.show', [$module->slug, $nextLesson->slug]) }}" class="btn btn-primary">
                        {{ Str::limit($nextLesson->title, 20) }} →
                    </a>
                @else
                    <a href="{{ route('courses.show', $module->slug) }}" class="btn btn-success">
                        ✓ Complete Module
                    </a>
                @endif
            </div>
        </div>

        <!-- Right Panel: Lab Workbench -->
        <div class="lab-panel">
            <!-- Lab Header Bar -->
            <div class="lab-header">
                <div class="lab-header-left">
                    <div class="lab-status-badge provisioning" id="labStatusBadge">
                        <span class="dot"></span>
                        <span id="labStatusText">Provisioning</span>
                    </div>
                    <div class="lab-timer">
                        <span>⏱️</span>
                        <span id="idleTimer">Active</span>
                    </div>
                </div>
                <button class="btn-stop" id="stopLabBtn" onclick="stopLab()">
                    ⏹️ Stop Lab
                </button>
            </div>

            <div class="lab-content">
                <div class="session-status" id="sessionStatus">
                    <div class="spinner"></div>
                    <h3 id="statusTitle">Starting Lab Environment</h3>
                    <p id="statusMessage">Provisioning your lab session...</p>
                </div>

                <iframe id="workbenchFrame" style="display: none;"></iframe>
            </div>
        </div>
    </div>

    <!-- Idle Warning Toast -->
    <div class="idle-warning" id="idleWarning">
        ⚠️ Lab will stop in <span id="idleCountdown">30</span>s due to inactivity
        <button onclick="resetIdleTimer()"
            style="background:#0f172a;color:#fbbf24;border:none;padding:0.5rem 1rem;border-radius:6px;cursor:pointer;font-weight:600;">
            I'm Here!
        </button>
    </div>
@endsection

@push('scripts')
    <script>
        const sessionId = '{{ $session->id }}';
        const IDLE_TIMEOUT_MS = 2 * 60 * 1000; // 2 minutes
        const HEARTBEAT_INTERVAL_MS = 30 * 1000; // 30 seconds
        const IDLE_WARNING_BEFORE_MS = 30 * 1000; // Show warning 30s before timeout

        let idleTimer = null;
        let heartbeatInterval = null;
        let idleCountdownInterval = null;
        let lastActivity = Date.now();
        let labRunning = false;
        let labStopped = false;

        const statusEl = document.getElementById('sessionStatus');
        const messageEl = document.getElementById('statusMessage');
        const titleEl = document.getElementById('statusTitle');
        const frameEl = document.getElementById('workbenchFrame');
        const badgeEl = document.getElementById('labStatusBadge');
        const badgeTextEl = document.getElementById('labStatusText');
        const idleTimerEl = document.getElementById('idleTimer');
        const idleWarningEl = document.getElementById('idleWarning');
        const stopBtn = document.getElementById('stopLabBtn');

        // ========================================
        // SESSION STATUS CHECKING
        // ========================================
        function checkSessionStatus() {
            if (labStopped) return;

            fetch(`/api/lab-sessions/${sessionId}/status`)
                .then(res => res.json())
                .then(data => {
                    const session = data.data;

                    if (session.status === 'running') {
                        onLabReady();
                        return;
                    }

                    if (session.status === 'error' || session.status === 'destroyed') {
                        onLabStopped(session.status === 'error' ? 'Error provisioning lab' : 'Lab session ended');
                        return;
                    }

                    if (session.status === 'provisioning') {
                        messageEl.textContent = 'Setting up your Kubernetes environment...';
                    }

                    setTimeout(checkSessionStatus, 3000);
                })
                .catch(err => {
                    console.error('Status check failed:', err);
                    setTimeout(checkSessionStatus, 5000);
                });
        }

        function onLabReady() {
            labRunning = true;
            statusEl.classList.add('ready');
            frameEl.style.display = 'block';
            frameEl.src = 'http://localhost:9000';

            badgeEl.classList.remove('provisioning');
            badgeTextEl.textContent = 'Running';

            startHeartbeat();
            startIdleTracking();
        }

        function onLabStopped(reason = 'Lab stopped') {
            labRunning = false;
            labStopped = true;

            stopHeartbeat();
            stopIdleTracking();

            frameEl.style.display = 'none';
            statusEl.classList.remove('ready');
            statusEl.classList.add('stopped');

            titleEl.textContent = reason;
            messageEl.innerHTML = 'Your lab session has ended. <a href="' + window.location.href + '" class="btn-restart">🔄 Start New Lab</a>';

            badgeEl.classList.add('provisioning');
            badgeTextEl.textContent = 'Stopped';
            stopBtn.disabled = true;
        }

        // ========================================
        // HEARTBEAT (Keeps session alive)
        // ========================================
        function startHeartbeat() {
            heartbeatInterval = setInterval(sendHeartbeat, HEARTBEAT_INTERVAL_MS);
            sendHeartbeat(); // Send immediately
        }

        function stopHeartbeat() {
            if (heartbeatInterval) {
                clearInterval(heartbeatInterval);
                heartbeatInterval = null;
            }
        }

        function sendHeartbeat() {
            if (!labRunning || labStopped) return;

            fetch(`/api/lab-sessions/${sessionId}/heartbeat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).catch(err => console.error('Heartbeat failed:', err));
        }

        // ========================================
        // IDLE DETECTION
        // ========================================
        function startIdleTracking() {
            resetIdleTimer();

            // Track user activity
            document.addEventListener('mousemove', onActivity);
            document.addEventListener('keydown', onActivity);
            document.addEventListener('click', onActivity);
            document.addEventListener('scroll', onActivity);
        }

        function stopIdleTracking() {
            if (idleTimer) clearTimeout(idleTimer);
            if (idleCountdownInterval) clearInterval(idleCountdownInterval);

            document.removeEventListener('mousemove', onActivity);
            document.removeEventListener('keydown', onActivity);
            document.removeEventListener('click', onActivity);
            document.removeEventListener('scroll', onActivity);

            idleWarningEl.classList.remove('show');
        }

        function onActivity() {
            if (!labRunning || labStopped) return;
            lastActivity = Date.now();
            resetIdleTimer();
        }

        function resetIdleTimer() {
            if (idleTimer) clearTimeout(idleTimer);
            if (idleCountdownInterval) clearInterval(idleCountdownInterval);

            idleWarningEl.classList.remove('show');
            idleTimerEl.textContent = 'Active';

            // Set timeout to show warning
            idleTimer = setTimeout(() => {
                showIdleWarning();
            }, IDLE_TIMEOUT_MS - IDLE_WARNING_BEFORE_MS);
        }

        function showIdleWarning() {
            if (!labRunning || labStopped) return;

            let secondsLeft = IDLE_WARNING_BEFORE_MS / 1000;
            document.getElementById('idleCountdown').textContent = secondsLeft;
            idleWarningEl.classList.add('show');
            idleTimerEl.textContent = `Idle: ${secondsLeft}s`;

            idleCountdownInterval = setInterval(() => {
                secondsLeft--;
                document.getElementById('idleCountdown').textContent = secondsLeft;
                idleTimerEl.textContent = `Idle: ${secondsLeft}s`;

                if (secondsLeft <= 0) {
                    clearInterval(idleCountdownInterval);
                    stopLab('Stopped due to inactivity');
                }
            }, 1000);
        }

        // ========================================
        // STOP LAB
        // ========================================
        function stopLab(reason = 'Lab stopped by user') {
            if (labStopped) return;

            stopBtn.disabled = true;
            stopBtn.textContent = '⏳ Stopping...';

            fetch(`/api/lab-sessions/${sessionId}/stop`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                onLabStopped(reason);
            }).catch(err => {
                console.error('Failed to stop lab:', err);
                onLabStopped(reason);
            });
        }

        // ========================================
        // TAB CLOSE / NAVIGATE AWAY
        // ========================================
        window.addEventListener('beforeunload', function (e) {
            if (labRunning && !labStopped) {
                // Stop the lab when user closes tab/navigates away
                navigator.sendBeacon(`/api/lab-sessions/${sessionId}/stop`, JSON.stringify({
                    _token: '{{ csrf_token() }}'
                }));
            }
        });

        // Also handle visibility change (user switches tab)
        document.addEventListener('visibilitychange', function () {
            if (document.hidden && labRunning && !labStopped) {
                // User switched away - start a shorter idle timer
                // In production, you might want to stop after longer hidden time
            }
        });

        // Start checking status
        checkSessionStatus();
    </script>
@endpush