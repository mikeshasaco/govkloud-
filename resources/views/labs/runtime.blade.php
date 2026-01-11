<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $module->title ?? $session->lab->title }} - GovKloud Labs</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --gk-navy: #0f172a;
            --gk-slate: #1e293b;
            --gk-cyan: #06b6d4;
            --gk-teal: #14b8a6;
            --gk-gold: #fbbf24;
            --gk-purple: #8b5cf6;
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
            background: var(--gk-navy);
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
            padding: 0.75rem 1.5rem;
            background: var(--gk-slate);
            border-bottom: 1px solid var(--border);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .back-btn {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-btn:hover {
            color: var(--gk-cyan);
        }

        .module-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
        }

        .header-right {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.provisioning {
            background: rgba(251, 191, 36, 0.15);
            color: var(--gk-gold);
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .status-badge.running {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .timer {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .btn-stop {
            padding: 0.5rem 1rem;
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        .btn-stop:hover {
            background: rgba(239, 68, 68, 0.25);
        }

        /* Split layout */
        .runtime-container {
            flex: 1;
            display: flex;
            overflow: hidden;
        }

        /* Left panel - Lessons Curriculum */
        .lessons-panel {
            width: 380px;
            background: var(--gk-slate);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .lessons-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .lessons-header-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .lessons-header h2 {
            font-size: 1rem;
            font-weight: 700;
        }

        .lessons-list {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        /* Lesson Item */
        .lesson-item {
            background: var(--gk-navy);
            border: 1px solid var(--border);
            border-radius: 10px;
            margin-bottom: 0.75rem;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .lesson-item:hover {
            border-color: var(--gk-cyan);
        }

        .lesson-item.active {
            border-color: var(--gk-cyan);
            box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2);
        }

        .lesson-item.completed .lesson-number {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .lesson-main {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
        }

        .lesson-number {
            width: 28px;
            height: 28px;
            background: rgba(6, 182, 212, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--gk-cyan);
            flex-shrink: 0;
        }

        .lesson-info {
            flex: 1;
            min-width: 0;
        }

        .lesson-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lesson-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: flex;
            gap: 0.75rem;
        }

        .lesson-badges {
            display: flex;
            gap: 0.3rem;
            flex-shrink: 0;
        }

        .badge {
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-video {
            background: rgba(251, 191, 36, 0.15);
            color: var(--gk-gold);
        }

        .badge-quiz {
            background: rgba(139, 92, 246, 0.15);
            color: var(--gk-purple);
        }

        /* Expanded lesson content */
        .lesson-content {
            display: none;
            padding: 0 1rem 1rem;
            border-top: 1px solid var(--border);
            background: rgba(0, 0, 0, 0.2);
        }

        .lesson-item.active .lesson-content {
            display: block;
        }

        .lesson-video {
            width: 100%;
            aspect-ratio: 16/9;
            border-radius: 8px;
            margin-top: 1rem;
            background: #000;
        }

        .lesson-text {
            margin-top: 1rem;
            font-size: 0.85rem;
            line-height: 1.7;
            color: var(--text-muted);
        }

        /* Quiz Styles */
        .quiz-section {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        .quiz-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--gk-purple);
        }

        .quiz-icon {
            font-size: 1.25rem;
        }

        .quiz-question {
            background: rgba(139, 92, 246, 0.08);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }

        .quiz-question-text {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text);
        }

        .quiz-input-wrapper {
            display: flex;
            gap: 0.5rem;
        }

        .quiz-input {
            flex: 1;
            background: var(--gk-navy);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 0.75rem;
            color: var(--text);
            font-size: 0.9rem;
        }

        .quiz-input:focus {
            outline: none;
            border-color: var(--gk-cyan);
        }

        .quiz-check-btn {
            background: var(--gk-purple);
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .quiz-check-btn:hover {
            background: #7c3aed;
        }

        .quiz-options {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .quiz-option {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--gk-navy);
            border: 1px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .quiz-option:hover {
            border-color: var(--gk-purple);
        }

        .quiz-option input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: var(--gk-purple);
        }

        .quiz-option-text {
            color: var(--text);
            font-size: 0.9rem;
        }

        .quiz-feedback {
            margin-top: 0.75rem;
            padding: 0.75rem;
            border-radius: 6px;
        }

        .quiz-correct {
            color: #10b981;
            font-weight: 600;
            display: none;
        }

        .quiz-incorrect {
            color: #ef4444;
            font-weight: 600;
            display: none;
        }

        .quiz-explanation {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            font-style: italic;
        }

        /* Right panel - Workbench */
        .workbench-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #1e1e1e;
        }

        .workbench-status {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(6, 182, 212, 0.2);
            border-top-color: var(--gk-cyan);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1.5rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .workbench-status h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .workbench-status p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .workbench-iframe {
            flex: 1;
            width: 100%;
            border: none;
        }

        /* Idle warning */
        .idle-warning {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: rgba(251, 191, 36, 0.95);
            color: #0f172a;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            z-index: 1000;
            display: none;
        }

        .idle-warning.show {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
    </style>
</head>

<body>
    <header class="runtime-header">
        <div class="header-left">
            <a href="{{ route('modules.show', $module->slug ?? 'k8s-basics') }}" class="back-btn">
                ← Back
            </a>
            <span class="module-title">{{ $module->title ?? $session->lab->title }}</span>
        </div>
        <div class="header-right">
            <div class="status-badge provisioning" id="statusBadge">
                <span class="status-dot"></span>
                <span id="statusText">Provisioning</span>
            </div>
            <span class="timer" id="timer">Loading...</span>
            <button class="btn-stop" id="stopBtn">⏹️ Stop Lab</button>
        </div>
    </header>

    <div class="runtime-container">
        <!-- Left panel - Lessons -->
        <div class="lessons-panel">
            <div class="lessons-header">
                <div class="lessons-header-icon">📚</div>
                <h2>Course Content</h2>
            </div>
            <div class="lessons-list">
                @forelse($lessons as $lesson)
                    <div class="lesson-item {{ $loop->first ? 'active' : '' }}" data-lesson-id="{{ $lesson->id }}">
                        <div class="lesson-main">
                            <div class="lesson-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="lesson-info">
                                <div class="lesson-title">{{ $lesson->title }}</div>
                                <div class="lesson-meta">
                                    <span>📖 Lesson</span>
                                    @if($lesson->video_url)<span>📹 Video</span>@endif
                                    @if($lesson->hasQuiz())<span>❓ Quiz</span>@endif
                                </div>
                            </div>
                            <div class="lesson-badges">
                                @if($lesson->video_url)
                                    <span class="badge badge-video">Video</span>
                                @endif
                                @if($lesson->hasQuiz())
                                    <span class="badge badge-quiz">Quiz</span>
                                @endif
                            </div>
                        </div>
                        <div class="lesson-content">
                            @if($lesson->video_url)
                                <iframe class="lesson-video" src="{{ $lesson->video_url }}" 
                                    frameborder="0" allowfullscreen></iframe>
                            @endif
                            @if($lesson->reading_md)
                                <div class="lesson-text">
                                    {!! Str::markdown($lesson->reading_md) !!}
                                </div>
                            @endif
                            @if($lesson->hasQuiz())
                                <div class="quiz-section">
                                    <div class="quiz-header">
                                        <span class="quiz-icon">❓</span>
                                        <span>Knowledge Check</span>
                                    </div>
                                    @foreach($lesson->getQuizQuestions() as $qIndex => $quiz)
                                        <div class="quiz-question" data-quiz-index="{{ $qIndex }}">
                                            <div class="quiz-question-text">{{ $quiz['question'] ?? '' }}</div>
                                            @if(($quiz['type'] ?? 'text') === 'multiple_choice')
                                                <div class="quiz-options">
                                                    @foreach($quiz['options'] ?? [] as $oIndex => $option)
                                                        <label class="quiz-option">
                                                            <input type="radio" name="quiz_{{ $lesson->id }}_{{ $qIndex }}" value="{{ $option }}">
                                                            <span class="quiz-option-text">{{ $option }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="quiz-input-wrapper">
                                                    <input type="text" class="quiz-input" 
                                                        placeholder="Type your answer..." 
                                                        data-correct="{{ $quiz['correct_answer'] ?? '' }}">
                                                    <button type="button" class="quiz-check-btn" onclick="checkQuizAnswer(this)">
                                                        Check
                                                    </button>
                                                </div>
                                            @endif
                                            <div class="quiz-feedback" style="display: none;">
                                                <div class="quiz-correct">✅ Correct!</div>
                                                <div class="quiz-incorrect">❌ Try again</div>
                                                @if(!empty($quiz['explanation']))
                                                    <div class="quiz-explanation">{{ $quiz['explanation'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- Lab steps fallback -->
                    @foreach($session->lab->steps as $step)
                        <div class="lesson-item {{ $loop->first ? 'active' : '' }}" data-step="{{ $loop->index }}">
                            <div class="lesson-main">
                                <div class="lesson-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                                <div class="lesson-info">
                                    <div class="lesson-title">
                                        {{ $step->payload_json['title'] ?? 'Step ' . $loop->iteration }}
                                    </div>
                                    <div class="lesson-meta">
                                        <span>{{ ucfirst($step->type) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="lesson-content">
                                <div class="lesson-text">
                                    @if($step->type === 'instruction')
                                        {!! nl2br(e($step->payload_json['content'] ?? '')) !!}
                                    @elseif($step->type === 'task')
                                        {{ $step->payload_json['description'] ?? '' }}
                                    @elseif($step->type === 'quiz')
                                        {{ $step->payload_json['question'] ?? '' }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>

        <!-- Right panel - Workbench -->
        <div class="workbench-panel" id="workbenchPanel">
            <div class="workbench-status" id="workbenchStatus">
                <div class="spinner"></div>
                <h3>Starting Lab Environment</h3>
                <p id="statusMessage">Provisioning your Kubernetes environment...</p>
            </div>
        </div>
    </div>

    <!-- Idle Warning -->
    <div class="idle-warning" id="idleWarning">
        ⚠️ Lab will stop in <span id="idleCountdown">30</span>s due to inactivity
        <button onclick="resetIdleTimer()" style="background:#0f172a;color:#fbbf24;border:none;padding:0.5rem 1rem;border-radius:6px;cursor:pointer;font-weight:600;">
            I'm Here!
        </button>
    </div>

    <script>
        const SESSION_ID = '{{ $session->id }}';
        const EXPIRES_AT = new Date('{{ $session->expires_at->toIso8601String() }}');
        const IDLE_TIMEOUT_MS = 2 * 60 * 1000;
        const IDLE_WARNING_BEFORE_MS = 30 * 1000;
        const HEARTBEAT_INTERVAL_MS = 30 * 1000;
        
        let pollInterval;
        let heartbeatInterval;
        let idleTimer;
        let idleCountdownInterval;
        let lastActivity = Date.now();
        let labRunning = false;
        let labStopped = false;

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
            if (labStopped) return;
            try {
                const response = await fetch(`/api/lab-sessions/${SESSION_ID}/status`);
                const data = await response.json();
                const session = data.data;

                if (session.status === 'running') {
                    clearInterval(pollInterval);
                    onLabReady();
                } else if (session.status === 'error') {
                    clearInterval(pollInterval);
                    document.getElementById('statusMessage').textContent = session.error_message || 'Error provisioning lab';
                    document.querySelector('.spinner').style.display = 'none';
                }
            } catch (error) {
                console.error('Poll error:', error);
            }
        }

        function onLabReady() {
            labRunning = true;
            
            // Update status badge
            const badge = document.getElementById('statusBadge');
            badge.classList.remove('provisioning');
            badge.classList.add('running');
            document.getElementById('statusText').textContent = 'Running';
            
            // Show iframe - use localhost:9000 for local dev
            const panel = document.getElementById('workbenchPanel');
            panel.innerHTML = '<iframe class="workbench-iframe" src="http://localhost:9000"></iframe>';
            
            startHeartbeat();
            startIdleTracking();
        }

        // Heartbeat
        function startHeartbeat() {
            heartbeatInterval = setInterval(sendHeartbeat, HEARTBEAT_INTERVAL_MS);
            sendHeartbeat();
        }

        async function sendHeartbeat() {
            if (!labRunning || labStopped) return;
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

        // Idle tracking
        function startIdleTracking() {
            resetIdleTimer();
            document.addEventListener('mousemove', onActivity);
            document.addEventListener('keydown', onActivity);
            document.addEventListener('click', onActivity);
        }

        function onActivity() {
            if (!labRunning || labStopped) return;
            lastActivity = Date.now();
            resetIdleTimer();
        }

        function resetIdleTimer() {
            if (idleTimer) clearTimeout(idleTimer);
            if (idleCountdownInterval) clearInterval(idleCountdownInterval);
            document.getElementById('idleWarning').classList.remove('show');
            
            idleTimer = setTimeout(showIdleWarning, IDLE_TIMEOUT_MS - IDLE_WARNING_BEFORE_MS);
        }

        function showIdleWarning() {
            if (!labRunning || labStopped) return;
            
            let secondsLeft = IDLE_WARNING_BEFORE_MS / 1000;
            document.getElementById('idleCountdown').textContent = secondsLeft;
            document.getElementById('idleWarning').classList.add('show');
            
            idleCountdownInterval = setInterval(() => {
                secondsLeft--;
                document.getElementById('idleCountdown').textContent = secondsLeft;
                if (secondsLeft <= 0) {
                    clearInterval(idleCountdownInterval);
                    stopLab('Stopped due to inactivity');
                }
            }, 1000);
        }

        // Stop lab
        async function stopLab(reason = 'Lab stopped') {
            if (labStopped) return;
            labStopped = true;
            
            try {
                await fetch(`/api/lab-sessions/${SESSION_ID}/stop`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
            } catch (error) {
                console.error('Stop error:', error);
            }
            
            window.location.href = '{{ route("modules.show", $module->slug ?? "k8s-basics") }}';
        }

        document.getElementById('stopBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to stop this lab?')) {
                stopLab();
            }
        });

        // Tab close detection
        window.addEventListener('beforeunload', function() {
            if (labRunning && !labStopped) {
                navigator.sendBeacon(`/api/lab-sessions/${SESSION_ID}/stop`, JSON.stringify({
                    _token: document.querySelector('meta[name="csrf-token"]').content
                }));
            }
        });

        // Quiz answer checking
        function checkQuizAnswer(btn) {
            const wrapper = btn.parentElement;
            const input = wrapper.querySelector('.quiz-input');
            const question = wrapper.closest('.quiz-question');
            const feedback = question.querySelector('.quiz-feedback');
            const correct = input.dataset.correct.toLowerCase().trim();
            const answer = input.value.toLowerCase().trim();
            
            feedback.style.display = 'block';
            
            if (answer === correct || correct.includes(answer) && answer.length > 3) {
                feedback.querySelector('.quiz-correct').style.display = 'block';
                feedback.querySelector('.quiz-incorrect').style.display = 'none';
                input.style.borderColor = '#10b981';
            } else {
                feedback.querySelector('.quiz-correct').style.display = 'none';
                feedback.querySelector('.quiz-incorrect').style.display = 'block';
                input.style.borderColor = '#ef4444';
            }
        }

        // Multiple choice quiz checking
        document.querySelectorAll('.quiz-option input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const question = this.closest('.quiz-question');
                const feedback = question.querySelector('.quiz-feedback');
                const options = question.querySelectorAll('.quiz-option');
                
                // Get correct answer from data attribute (would need to be added)
                // For now, just show feedback
                feedback.style.display = 'block';
                
                options.forEach(opt => {
                    opt.style.borderColor = 'var(--border)';
                });
                this.closest('.quiz-option').style.borderColor = 'var(--gk-purple)';
            });
        });

        // Lesson toggle
        document.querySelectorAll('.lesson-item').forEach(item => {
            item.querySelector('.lesson-main').addEventListener('click', () => {
                document.querySelectorAll('.lesson-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            });
        });

        // Initialize
        updateTimer();
        setInterval(updateTimer, 1000);

        @if($session->status === 'running' && $session->code_url)
            onLabReady();
        @else
            pollInterval = setInterval(pollStatus, 3000);
            pollStatus();
        @endif
    </script>
</body>

</html>