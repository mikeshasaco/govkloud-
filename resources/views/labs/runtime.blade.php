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
            --gk-cyan: #D2B48C;
            --gk-teal: #C4A77D;
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
            box-shadow: 0 0 0 2px rgba(210, 180, 140, 0.2);
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
            background: rgba(210, 180, 140, 0.2);
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
            border: 4px solid rgba(210, 180, 140, 0.2);
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
            <a href="{{ route('courses.show', $module->slug ?? 'k8s-basics') }}" class="back-btn">
                ← Back
            </a>
            <span class="module-title">{{ $module->title ?? $session->lab->title }}</span>
        </div>
        <div class="header-right">
            <div class="status-badge provisioning" id="statusBadge">
                <span class="status-dot"></span>
                <span id="statusText">Provisioning</span>
            </div>
            <div class="password-badge" id="headerPassword" style="display: none; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.75rem;">
                <span style="color: var(--text-muted);">🔐</span>
                <code style="color: #10b981; font-family: monospace; margin: 0 0.4rem;">{{ $session->session_token }}</code>
                <button onclick="copyPassword()" style="background: transparent; border: none; cursor: pointer; font-size: 0.8rem;" title="Copy password">📋</button>
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
                                <iframe class="lesson-video" src="{{ $lesson->embed_video_url }}" 
                                    frameborder="0" allowfullscreen></iframe>
                            @endif
                            @if($lesson->reading_md)
                                <div class="lesson-text">
                                    {!! Str::markdown($lesson->reading_md) !!}
                                </div>
                            @endif
                            @if($lesson->hasQuiz())
                                <div class="quiz-section" id="quiz_{{ $lesson->id }}">
                                    <div class="quiz-header">
                                        <span class="quiz-icon">❓</span>
                                        <span>Knowledge Check</span>
                                    </div>
                                    @foreach($lesson->getQuizQuestions() as $qIndex => $quiz)
                                        <div class="quiz-question" data-question-index="{{ $qIndex }}" data-correct="{{ $quiz['correct_answer'] ?? '' }}" data-lesson-id="{{ $lesson->id }}">
                                            <div class="quiz-question-text">{{ $qIndex + 1 }}. {{ $quiz['question'] ?? '' }}</div>
                                            @if(($quiz['type'] ?? 'multiple_choice') === 'multiple_choice')
                                                <div class="quiz-options">
                                                    @foreach($quiz['options'] ?? [] as $oIndex => $option)
                                                        @php $optionText = is_array($option) ? ($option['text'] ?? '') : $option; @endphp
                                                        <label class="quiz-option" data-option="{{ $optionText }}">
                                                            <input type="radio" name="quiz_{{ $lesson->id }}_{{ $qIndex }}" value="{{ $optionText }}">
                                                            <span class="quiz-option-text">{{ $optionText }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="quiz-input-wrapper">
                                                    <input type="text" class="quiz-input" 
                                                        placeholder="Type your answer..." 
                                                        data-correct="{{ $quiz['correct_answer'] ?? '' }}">
                                                </div>
                                            @endif
                                            <div class="quiz-feedback" style="display: none; margin-top: 0.75rem; padding: 0.75rem; border-radius: 8px;">
                                                <div class="quiz-correct" style="display: none; color: #10b981;">✅ Correct!</div>
                                                <div class="quiz-incorrect" style="display: none; color: #ef4444;">❌ Incorrect</div>
                                                @if(!empty($quiz['explanation']))
                                                    <div class="quiz-explanation" style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--text-muted);">{{ $quiz['explanation'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <button type="button" class="quiz-submit-btn" onclick="submitQuiz({{ $lesson->id }})" 
                                        style="width: 100%; margin-top: 1rem; padding: 0.75rem; background: linear-gradient(135deg, #8b5cf6, #6366f1); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                                        Submit Answers
                                    </button>
                                    <div id="quiz_result_{{ $lesson->id }}" style="display: none; margin-top: 1rem; padding: 1rem; border-radius: 8px; text-align: center;"></div>
                                </div>
                            @endif
                            
                            <!-- Completion Section -->
                            @auth
                                @php $isCompleted = Auth::user()->hasCompletedLesson($lesson); @endphp
                                <div class="lesson-completion" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                                    @if($isCompleted)
                                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; color: #10b981;">
                                            <span style="font-size: 1.25rem;">✅</span>
                                            <div>
                                                <strong>Lesson Completed!</strong>
                                                <div style="font-size: 0.8rem; opacity: 0.8;">Great job! Continue to the next lesson.</div>
                                            </div>
                                        </div>
                                    @elseif($lesson->hasQuiz())
                                        <div id="quizCompletionBtn_{{ $lesson->id }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.3); border-radius: 8px; color: #fbbf24;">
                                            <span style="font-size: 1.25rem;">❓</span>
                                            <div>
                                                <strong>Complete the quiz above</strong>
                                                <div style="font-size: 0.8rem; opacity: 0.8;">Answer all questions correctly to mark this lesson complete.</div>
                                            </div>
                                        </div>
                                    @else
                                        <button onclick="markLessonComplete({{ $lesson->id }}, this)" 
                                            class="btn-mark-complete"
                                            style="width: 100%; padding: 1rem; background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal)); color: var(--gk-navy); border: none; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;">
                                            ✓ Mark as Complete
                                        </button>
                                    @endif
                                </div>
                            @endauth
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
                <div class="password-info" id="passwordInfo" style="display: none; margin-top: 1.5rem; padding: 1rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; text-align: left; max-width: 450px;">
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">🔐 Lab Password (use when prompted):</div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                        <code id="passwordValue" style="flex: 1; background: var(--gk-navy); padding: 0.5rem 0.75rem; border-radius: 4px; font-family: monospace; font-size: 0.9rem; color: var(--gk-cyan); word-break: break-all;">{{ $session->session_token }}</code>
                        <button onclick="copyPassword()" style="padding: 0.5rem 0.75rem; background: var(--gk-cyan); color: var(--gk-navy); border: none; border-radius: 4px; cursor: pointer; font-weight: 600; white-space: nowrap;">📋 Copy</button>
                    </div>
                    <button onclick="openLabInNewTab()" style="width: 100%; padding: 0.75rem 1rem; background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal)); color: var(--gk-navy); border: none; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 1rem;">
                        🚀 Open Lab in New Tab
                    </button>
                    <div style="margin-top: 0.75rem; font-size: 0.75rem; color: var(--text-muted); text-align: center;">
                        Safari users: Open in new tab for best experience
                    </div>
                </div>
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

                if (session.status === 'running' && session.code_url) {
                    clearInterval(pollInterval);
                    document.getElementById('statusMessage').textContent = 'Verifying lab connection...';
                    probeAndLoad(session.code_url);
                } else if (session.status === 'error') {
                    clearInterval(pollInterval);
                    document.getElementById('statusMessage').textContent = session.error_message || 'Error provisioning lab';
                    document.querySelector('.spinner').style.display = 'none';
                }
            } catch (error) {
                console.error('Poll error:', error);
            }
        }

        // Probe the code-server URL via server-side check, then load iframe
        async function probeAndLoad(codeUrl, attempt = 1) {
            const maxAttempts = 20;  // 20 × 3s = 60s max
            try {
                const response = await fetch(`/api/lab-sessions/${SESSION_ID}/probe`);
                const data = await response.json();

                if (data.ready) {
                    onLabReady(codeUrl);
                    return;
                }
            } catch (e) {
                console.error('Probe error:', e);
            }

            if (attempt < maxAttempts) {
                document.getElementById('statusMessage').textContent = 
                    `Waiting for lab environment... (${attempt}/${maxAttempts})`;
                setTimeout(() => probeAndLoad(codeUrl, attempt + 1), 3000);
            } else {
                // Timeout — try loading iframe anyway (might work)
                console.warn('Probe timeout — loading iframe anyway');
                onLabReady(codeUrl);
            }
        }

        function onLabReady(codeUrl) {
            labRunning = true;
            
            // Update status badge
            const badge = document.getElementById('statusBadge');
            badge.classList.remove('provisioning');
            badge.classList.add('running');
            document.getElementById('statusText').textContent = 'Running';
            
            // Load the code-server iframe — URL is confirmed reachable by probe
            const panel = document.getElementById('workbenchPanel');
            panel.innerHTML = '<iframe class="workbench-iframe" src="' + codeUrl + '"></iframe>';
            
            startHeartbeat();
            startIdleTracking();
        }
        
        function showPasswordInfo() {
            const passwordInfo = document.getElementById('passwordInfo');
            if (passwordInfo) {
                passwordInfo.style.display = 'block';
            }
            // Also show the header password badge
            const headerPassword = document.getElementById('headerPassword');
            if (headerPassword) {
                headerPassword.style.display = 'flex';
            }
        }
        
        function copyPassword() {
            const password = '{{ $session->session_token }}';
            navigator.clipboard.writeText(password).then(() => {
                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = '✓';
                setTimeout(() => btn.textContent = originalText, 2000);
            });
        }
        
        async function openLabInNewTab() {
            try {
                const response = await fetch(`/api/lab-sessions/${SESSION_ID}/status`);
                const data = await response.json();
                if (data.data.code_url) {
                    window.open(data.data.code_url, '_blank');
                }
            } catch (e) {
                console.error('Failed to get lab URL:', e);
            }
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
            
            window.location.href = '{{ route("courses.show", $module->slug ?? "k8s-basics") }}';
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
            probeAndLoad('{{ $codeUrl }}');
        @else
            pollInterval = setInterval(pollStatus, 3000);
            pollStatus();
        @endif

        // Mark lesson as complete
        async function markLessonComplete(lessonId, btn) {
            btn.disabled = true;
            btn.innerHTML = '⏳ Saving...';
            
            try {
                const response = await fetch(`/lessons/${lessonId}/complete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    btn.outerHTML = `
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; color: #10b981;">
                            <span style="font-size: 1.25rem;">✅</span>
                            <div>
                                <strong>Lesson Completed!</strong>
                                <div style="font-size: 0.8rem; opacity: 0.8;">Great job! Continue to the next lesson.</div>
                            </div>
                        </div>
                    `;
                } else {
                    btn.disabled = false;
                    btn.innerHTML = '✓ Mark as Complete';
                    alert('Error marking lesson complete');
                }
            } catch (error) {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = '✓ Mark as Complete';
            }
        }

        // Submit quiz and show results
        function submitQuiz(lessonId) {
            const quizSection = document.getElementById(`quiz_${lessonId}`);
            const questions = quizSection.querySelectorAll('.quiz-question');
            const resultDiv = document.getElementById(`quiz_result_${lessonId}`);
            
            let correctCount = 0;
            let totalQuestions = questions.length;
            
            questions.forEach((question, index) => {
                const correctAnswer = question.dataset.correct;
                const feedback = question.querySelector('.quiz-feedback');
                const correctEl = feedback.querySelector('.quiz-correct');
                const incorrectEl = feedback.querySelector('.quiz-incorrect');
                const options = question.querySelectorAll('.quiz-option');
                
                // Get selected answer
                let selectedAnswer = '';
                const selectedRadio = question.querySelector('input[type="radio"]:checked');
                const textInput = question.querySelector('.quiz-input');
                
                if (selectedRadio) {
                    selectedAnswer = selectedRadio.value;
                } else if (textInput) {
                    selectedAnswer = textInput.value.trim();
                }
                
                // Check if correct
                const isCorrect = selectedAnswer.toLowerCase() === correctAnswer.toLowerCase();
                
                // Reset option styles
                options.forEach(opt => {
                    opt.style.borderColor = 'var(--border)';
                    opt.style.background = '';
                });
                
                // Show feedback
                feedback.style.display = 'block';
                
                if (isCorrect) {
                    correctCount++;
                    correctEl.style.display = 'block';
                    incorrectEl.style.display = 'none';
                    feedback.style.background = 'rgba(16, 185, 129, 0.1)';
                    feedback.style.border = '1px solid rgba(16, 185, 129, 0.3)';
                    
                    // Highlight correct option in green
                    options.forEach(opt => {
                        if (opt.dataset.option === correctAnswer) {
                            opt.style.borderColor = '#10b981';
                            opt.style.background = 'rgba(16, 185, 129, 0.15)';
                        }
                    });
                } else {
                    correctEl.style.display = 'none';
                    incorrectEl.style.display = 'block';
                    feedback.style.background = 'rgba(239, 68, 68, 0.1)';
                    feedback.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                    
                    // Highlight selected option in red, correct in green
                    options.forEach(opt => {
                        if (opt.dataset.option === selectedAnswer) {
                            opt.style.borderColor = '#ef4444';
                            opt.style.background = 'rgba(239, 68, 68, 0.15)';
                        }
                        if (opt.dataset.option === correctAnswer) {
                            opt.style.borderColor = '#10b981';
                            opt.style.background = 'rgba(16, 185, 129, 0.15)';
                        }
                    });
                }
            });
            
            // Show overall result
            const percentage = Math.round((correctCount / totalQuestions) * 100);
            const passed = percentage >= 70;
            
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">${passed ? '🎉' : '📝'}</div>
                <div style="font-weight: 700; font-size: 1.1rem; color: ${passed ? '#10b981' : '#ef4444'};">
                    ${correctCount} of ${totalQuestions} correct (${percentage}%)
                </div>
                <div style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.25rem;">
                    ${passed ? 'Great job! Quiz passed.' : 'You need 70% to pass. Try again!'}
                </div>
            `;
            resultDiv.style.background = passed ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)';
            resultDiv.style.border = `1px solid ${passed ? 'rgba(16, 185, 129, 0.3)' : 'rgba(239, 68, 68, 0.3)'}`;
            
            // If passed, update the completion section
            if (passed) {
                const completionBtn = document.getElementById(`quizCompletionBtn_${lessonId}`);
                if (completionBtn) {
                    completionBtn.innerHTML = `
                        <span style="font-size: 1.25rem;">✅</span>
                        <div>
                            <strong>Quiz Passed!</strong>
                            <div style="font-size: 0.8rem; opacity: 0.8;">Lesson marked as complete.</div>
                        </div>
                    `;
                    completionBtn.style.background = 'rgba(16, 185, 129, 0.1)';
                    completionBtn.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                    completionBtn.style.color = '#10b981';
                }
                
                // Mark lesson as complete via API (need to add route for quiz completion)
                fetch(`/lessons/${lessonId}/complete-quiz`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ score: percentage })
                }).catch(e => console.error('Error saving progress:', e));
            }
        }
    </script>
</body>

</html>