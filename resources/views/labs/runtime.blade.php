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
            overflow: hidden;
        }

        /* ========== HEADER ========== */
        .runtime-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 1rem;
            background: var(--gk-slate);
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .back-btn {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .back-btn:hover { color: var(--gk-cyan); }

        .module-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text);
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .header-center {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .layout-btn {
            padding: 0.4rem 0.7rem;
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border);
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.2s ease;
        }

        .layout-btn:first-child { border-radius: 6px 0 0 6px; }
        .layout-btn:last-child { border-radius: 0 6px 6px 0; }
        .layout-btn:not(:last-child) { border-right: none; }

        .layout-btn:hover {
            background: rgba(210, 180, 140, 0.1);
            color: var(--gk-cyan);
        }

        .layout-btn.active {
            background: rgba(210, 180, 140, 0.2);
            color: var(--gk-cyan);
            border-color: var(--gk-cyan);
        }

        .layout-btn.active + .layout-btn { border-left-color: var(--gk-cyan); }

        .layout-icon {
            font-size: 1rem;
            line-height: 1;
        }

        .header-right {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.6rem;
            border-radius: 50px;
            font-size: 0.7rem;
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
            width: 7px;
            height: 7px;
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
            font-size: 0.8rem;
        }

        .btn-stop {
            padding: 0.35rem 0.75rem;
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.2s ease;
        }

        .btn-stop:hover { background: rgba(239, 68, 68, 0.25); }

        /* ========== MAIN CONTAINER ========== */
        .runtime-container {
            flex: 1;
            display: flex;
            overflow: hidden;
            position: relative;
        }

        /* ========== LESSONS SIDEBAR (always visible) ========== */
        .lessons-panel {
            width: 280px;
            min-width: 280px;
            background: var(--gk-slate);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            flex-shrink: 0;
        }

        .lessons-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .lessons-header-icon {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        .lessons-header h2 {
            font-size: 0.9rem;
            font-weight: 700;
        }

        .lessons-list {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem;
        }

        .lesson-nav-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            margin-bottom: 2px;
            border: 1px solid transparent;
        }

        .lesson-nav-item:hover {
            background: rgba(210, 180, 140, 0.08);
            border-color: rgba(210, 180, 140, 0.1);
        }

        .lesson-nav-item.active {
            background: rgba(210, 180, 140, 0.12);
            border-color: var(--gk-cyan);
        }

        .lesson-nav-item.completed .lesson-num {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .lesson-num {
            width: 24px;
            height: 24px;
            background: rgba(210, 180, 140, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--gk-cyan);
            flex-shrink: 0;
        }

        .lesson-nav-info {
            flex: 1;
            min-width: 0;
        }

        .lesson-nav-title {
            font-weight: 600;
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }

        .lesson-nav-badges {
            display: flex;
            gap: 0.25rem;
            margin-top: 2px;
        }

        .badge-sm {
            font-size: 0.55rem;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-sm.video { background: rgba(251, 191, 36, 0.15); color: var(--gk-gold); }
        .badge-sm.quiz { background: rgba(139, 92, 246, 0.15); color: var(--gk-purple); }
        .badge-sm.reading { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }

        /* ========== CONTENT PANEL ========== */
        .content-panel {
            display: flex;
            flex-direction: column;
            background: var(--gk-navy);
            overflow: hidden;
        }

        .content-inner {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .content-video-wrapper {
            width: 100%;
            aspect-ratio: 16/9;
            background: #000;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .content-video-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .content-reading {
            font-size: 0.9rem;
            line-height: 1.8;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .content-reading h1, .content-reading h2, .content-reading h3 {
            color: var(--text);
            margin: 1.25rem 0 0.5rem;
        }

        .content-reading code {
            background: rgba(210, 180, 140, 0.1);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-size: 0.85em;
            color: var(--gk-cyan);
        }

        .content-reading pre {
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem;
            overflow-x: auto;
            margin: 1rem 0;
        }

        .content-reading pre code {
            background: none;
            padding: 0;
        }

        .content-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            padding: 2rem;
            text-align: center;
        }

        .content-empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .content-empty h3 {
            font-size: 1.1rem;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        /* ========== RESIZER HANDLES ========== */
        .resizer {
            background: var(--border);
            position: relative;
            flex-shrink: 0;
            z-index: 10;
            transition: background 0.15s ease;
        }

        .resizer:hover, .resizer.dragging {
            background: var(--gk-cyan);
        }

        .resizer-h {
            width: 100%;
            height: 6px;
            cursor: row-resize;
        }

        .resizer-v {
            width: 6px;
            height: 100%;
            cursor: col-resize;
        }

        .resizer::after {
            content: '';
            position: absolute;
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }

        .resizer-h::after {
            width: 40px;
            height: 3px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .resizer-v::after {
            width: 3px;
            height: 40px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* ========== WORKBENCH PANEL ========== */
        .workbench-panel {
            display: flex;
            flex-direction: column;
            background: #1e1e1e;
            overflow: hidden;
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
            width: 50px;
            height: 50px;
            border: 3px solid rgba(210, 180, 140, 0.2);
            border-top-color: var(--gk-cyan);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1.25rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .workbench-status h3 { font-size: 1.1rem; margin-bottom: 0.4rem; }
        .workbench-status p { color: var(--text-muted); font-size: 0.85rem; }

        .workbench-iframe {
            flex: 1;
            width: 100%;
            border: none;
        }

        /* ========== QUIZ STYLES ========== */
        .quiz-section {
            margin-top: 1.5rem;
            padding: 1.25rem;
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.15);
            border-radius: 12px;
        }

        .quiz-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--gk-purple);
        }

        .quiz-question {
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }

        .quiz-question-text {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text);
        }

        .quiz-options { display: flex; flex-direction: column; gap: 0.4rem; }

        .quiz-option {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 0.75rem;
            background: var(--gk-navy);
            border: 1px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .quiz-option:hover { border-color: var(--gk-purple); }
        .quiz-option input[type="radio"] { width: 16px; height: 16px; accent-color: var(--gk-purple); }
        .quiz-option-text { color: var(--text); font-size: 0.85rem; }

        .quiz-input-wrapper { display: flex; gap: 0.5rem; }

        .quiz-input {
            flex: 1;
            background: var(--gk-navy);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 0.6rem;
            color: var(--text);
            font-size: 0.85rem;
        }

        .quiz-input:focus { outline: none; border-color: var(--gk-cyan); }

        .quiz-feedback { margin-top: 0.5rem; padding: 0.6rem; border-radius: 6px; }
        .quiz-correct { color: #10b981; font-weight: 600; display: none; }
        .quiz-incorrect { color: #ef4444; font-weight: 600; display: none; }
        .quiz-explanation { margin-top: 0.4rem; font-size: 0.8rem; color: var(--text-muted); font-style: italic; }

        /* ========== COMPLETION ========== */
        .completion-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        .completion-done {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 8px;
            color: #10b981;
        }

        .completion-quiz-pending {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: rgba(251, 191, 36, 0.1);
            border: 1px solid rgba(251, 191, 36, 0.3);
            border-radius: 8px;
            color: #fbbf24;
        }

        .btn-mark-complete {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            color: var(--gk-navy);
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-mark-complete:hover { filter: brightness(1.1); }

        .quiz-submit-btn {
            width: 100%;
            margin-top: 0.75rem;
            padding: 0.65rem;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .quiz-submit-btn:hover { filter: brightness(1.1); }

        /* ========== LAYOUT MODES ========== */

        /* --- SPLIT (top/bottom) --- */
        body.layout-split .runtime-container { flex-direction: row; }
        body.layout-split .lessons-panel { width: 280px; }
        body.layout-split .main-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        body.layout-split .content-panel { flex: 0 0 45%; }
        body.layout-split .resizer { width: 100%; height: 6px; cursor: row-resize; }
        body.layout-split .workbench-panel { flex: 1; }

        /* --- SIDE (left/right) --- */
        body.layout-side .runtime-container { flex-direction: row; }
        body.layout-side .lessons-panel { width: 280px; }
        body.layout-side .main-area {
            flex: 1;
            display: flex;
            flex-direction: row;
            overflow: hidden;
        }
        body.layout-side .content-panel { flex: 0 0 45%; }
        body.layout-side .resizer { width: 6px; height: 100%; cursor: col-resize; }
        body.layout-side .workbench-panel { flex: 1; }

        /* --- FOCUS --- */
        body.layout-focus .runtime-container { flex-direction: row; }
        body.layout-focus .lessons-panel { width: 280px; }
        body.layout-focus .main-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }
        body.layout-focus .content-panel { flex: 1; display: none; }
        body.layout-focus .resizer { display: none; }
        body.layout-focus .workbench-panel { flex: 1; }

        body.layout-focus.focus-content .content-panel { display: flex; flex: 1; }
        body.layout-focus.focus-content .workbench-panel { display: none; }
        body.layout-focus.focus-content .resizer { display: none; }

        body.layout-focus .focus-toggle {
            display: flex !important;
        }

        .focus-toggle {
            display: none !important;
            position: absolute;
            bottom: 1.25rem;
            right: 1.25rem;
            z-index: 50;
            padding: 0.6rem 1rem;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            color: var(--gk-navy);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
            transition: all 0.2s;
        }

        .focus-toggle:hover { transform: translateY(-2px); box-shadow: 0 6px 25px rgba(0,0,0,0.5); }

        /* ========== FLOATING PIP ========== */
        .pip-window {
            display: none;
            position: fixed;
            bottom: 5rem;
            right: 1.5rem;
            width: 320px;
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(0,0,0,0.6);
            border: 2px solid var(--border);
            z-index: 200;
            resize: both;
            background: #000;
        }

        .pip-window.show { display: block; }

        .pip-window iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .pip-close {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 22px;
            height: 22px;
            background: rgba(0,0,0,0.7);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 201;
        }

        /* ========== PASSWORD INFO ========== */
        .password-badge {
            display: none;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            font-size: 0.7rem;
            align-items: center;
            gap: 0.3rem;
        }

        /* ========== IDLE WARNING ========== */
        .idle-warning {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: rgba(251, 191, 36, 0.95);
            color: #0f172a;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            z-index: 1000;
            display: none;
        }

        .idle-warning.show {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* ========== DRAGGING STATE ========== */
        body.is-resizing {
            user-select: none;
            -webkit-user-select: none;
        }

        body.is-resizing iframe {
            pointer-events: none;
        }

        /* ========== SCROLLBAR ========== */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }
    </style>
</head>

<body class="layout-split">
    <header class="runtime-header">
        <div class="header-left">
            <a href="{{ route('courses.show', $module->slug ?? 'k8s-basics') }}" class="back-btn">
                ← Back
            </a>
            <span class="module-title">{{ $module->title ?? $session->lab->title }}</span>
        </div>
        <div class="header-right">
            <div class="header-center">
                <button class="layout-btn active" data-layout="split" title="Split View (top/bottom)">
                    <span class="layout-icon">⬜</span> Split
                </button>
                <button class="layout-btn" data-layout="side" title="Side by Side">
                    <span class="layout-icon">◫</span> Side
                </button>
                <button class="layout-btn" data-layout="focus" title="Focus Mode (toggle)">
                    <span class="layout-icon">⧉</span> Focus
                </button>
            </div>
            <div class="status-badge provisioning" id="statusBadge">
                <span class="status-dot"></span>
                <span id="statusText">Provisioning</span>
            </div>
            <div class="password-badge" id="headerPassword">
                <span style="color: var(--text-muted);">🔐</span>
                <code style="color: #10b981; font-family: monospace; margin: 0 0.3rem;">{{ $session->session_token }}</code>
                <button onclick="copyPassword()" style="background: transparent; border: none; cursor: pointer; font-size: 0.75rem;" title="Copy password">📋</button>
            </div>
            <span class="timer" id="timer">Loading...</span>
            <button class="btn-stop" id="stopBtn">⏹ Stop</button>
        </div>
    </header>

    <div class="runtime-container">
        <!-- Lessons Sidebar (always visible, compact navigation) -->
        <div class="lessons-panel">
            <div class="lessons-header">
                <div class="lessons-header-icon">📚</div>
                <h2>Lessons</h2>
            </div>
            <div class="lessons-list">
                @forelse($lessons as $lesson)
                    <div class="lesson-nav-item {{ $loop->first ? 'active' : '' }} @if(Auth::check() && Auth::user()->hasCompletedLesson($lesson)) completed @endif"
                         data-lesson-id="{{ $lesson->id }}" onclick="selectLesson({{ $lesson->id }})">
                        <div class="lesson-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="lesson-nav-info">
                            <div class="lesson-nav-title">{{ $lesson->title }}</div>
                            <div class="lesson-nav-badges">
                                @if($lesson->video_url)<span class="badge-sm video">📹</span>@endif
                                @if($lesson->reading_md)<span class="badge-sm reading">📖</span>@endif
                                @if($lesson->hasQuiz())<span class="badge-sm quiz">❓</span>@endif
                            </div>
                        </div>
                    </div>
                @empty
                    @foreach($session->lab->steps as $step)
                        <div class="lesson-nav-item {{ $loop->first ? 'active' : '' }}" data-step="{{ $loop->index }}">
                            <div class="lesson-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="lesson-nav-info">
                                <div class="lesson-nav-title">{{ $step->payload_json['title'] ?? 'Step ' . $loop->iteration }}</div>
                            </div>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>

        <!-- Main Area (content + resizer + workbench) -->
        <div class="main-area">
            <!-- Content Panel -->
            <div class="content-panel" id="contentPanel">
                {{-- Lesson content containers (hidden, JS shows the active one) --}}
                @forelse($lessons as $lesson)
                    <div class="content-inner lesson-content-block" id="lessonContent_{{ $lesson->id }}" style="{{ $loop->first ? '' : 'display:none;' }}">
                        @if($lesson->video_url)
                            <div class="content-video-wrapper">
                                <iframe src="{{ $lesson->embed_video_url }}" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen></iframe>
                            </div>
                        @endif

                        @if($lesson->reading_md)
                            <div class="content-reading">
                                {!! Str::markdown($lesson->reading_md) !!}
                            </div>
                        @endif

                        @if($lesson->hasQuiz())
                            <div class="quiz-section" id="quiz_{{ $lesson->id }}">
                                <div class="quiz-header">
                                    <span>❓</span>
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
                                        <div class="quiz-feedback" style="display: none;">
                                            <div class="quiz-correct">✅ Correct!</div>
                                            <div class="quiz-incorrect">❌ Incorrect</div>
                                            @if(!empty($quiz['explanation']))
                                                <div class="quiz-explanation">{{ $quiz['explanation'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                <button type="button" class="quiz-submit-btn" onclick="submitQuiz({{ $lesson->id }})">
                                    Submit Answers
                                </button>
                                <div id="quiz_result_{{ $lesson->id }}" style="display: none; margin-top: 0.75rem; padding: 0.75rem; border-radius: 8px; text-align: center;"></div>
                            </div>
                        @endif

                        <!-- Completion -->
                        @auth
                            @php $isCompleted = Auth::user()->hasCompletedLesson($lesson); @endphp
                            <div class="completion-section">
                                @if($isCompleted)
                                    <div class="completion-done">
                                        <span style="font-size: 1.1rem;">✅</span>
                                        <div>
                                            <strong>Lesson Completed!</strong>
                                            <div style="font-size: 0.75rem; opacity: 0.8;">Great job! Continue to the next lesson.</div>
                                        </div>
                                    </div>
                                @elseif($lesson->hasQuiz())
                                    <div class="completion-quiz-pending" id="quizCompletionBtn_{{ $lesson->id }}">
                                        <span style="font-size: 1.1rem;">❓</span>
                                        <div>
                                            <strong>Complete the quiz above</strong>
                                            <div style="font-size: 0.75rem; opacity: 0.8;">Answer questions correctly to complete.</div>
                                        </div>
                                    </div>
                                @else
                                    <button onclick="markLessonComplete({{ $lesson->id }}, this)" class="btn-mark-complete">
                                        ✓ Mark as Complete
                                    </button>
                                @endif
                            </div>
                        @endauth
                    </div>
                @empty
                    @foreach($session->lab->steps as $step)
                        <div class="content-inner lesson-content-block" data-step="{{ $loop->index }}" style="{{ $loop->first ? '' : 'display:none;' }}">
                            <div class="content-reading">
                                @if($step->type === 'instruction')
                                    {!! nl2br(e($step->payload_json['content'] ?? '')) !!}
                                @elseif($step->type === 'task')
                                    {{ $step->payload_json['description'] ?? '' }}
                                @elseif($step->type === 'quiz')
                                    {{ $step->payload_json['question'] ?? '' }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforelse
            </div>

            <!-- Resizer -->
            <div class="resizer" id="resizer"></div>

            <!-- Workbench Panel -->
            <div class="workbench-panel" id="workbenchPanel">
                <div class="workbench-status" id="workbenchStatus">
                    <div class="spinner"></div>
                    <h3>Starting Lab Environment</h3>
                    <p id="statusMessage">Provisioning your Kubernetes environment...</p>
                    <div id="passwordInfo" style="display: none; margin-top: 1.25rem; padding: 0.75rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; text-align: left; max-width: 400px;">
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.4rem;">🔐 Lab Password:</div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <code style="flex: 1; background: var(--gk-navy); padding: 0.4rem 0.6rem; border-radius: 4px; font-family: monospace; font-size: 0.85rem; color: var(--gk-cyan); word-break: break-all;">{{ $session->session_token }}</code>
                            <button onclick="copyPassword()" style="padding: 0.4rem 0.6rem; background: var(--gk-cyan); color: var(--gk-navy); border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 0.8rem;">📋 Copy</button>
                        </div>
                        <button onclick="openLabInNewTab()" style="width: 100%; padding: 0.6rem; background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal)); color: var(--gk-navy); border: none; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.9rem;">
                            🚀 Open Lab in New Tab
                        </button>
                    </div>
                </div>
            </div>

            <!-- Focus Mode Toggle Button -->
            <button class="focus-toggle" id="focusToggle" onclick="toggleFocus()">
                📹 Switch to Lesson
            </button>
        </div>
    </div>

    <!-- Idle Warning -->
    <div class="idle-warning" id="idleWarning">
        ⚠️ Lab will stop in <span id="idleCountdown">30</span>s
        <button onclick="resetIdleTimer()" style="background:#0f172a;color:#fbbf24;border:none;padding:0.4rem 0.8rem;border-radius:6px;cursor:pointer;font-weight:600;font-size:0.8rem;">
            I'm Here!
        </button>
    </div>

    <script>
        /* ========== STATE ========== */
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
        let currentLayout = localStorage.getItem('gk_layout') || 'split';
        let focusOnContent = false;
        let activeLessonId = null;

        /* ========== LAYOUT SWITCHING ========== */
        function setLayout(layout) {
            currentLayout = layout;
            document.body.className = `layout-${layout}`;
            if (focusOnContent) document.body.classList.add('focus-content');

            // Update active button
            document.querySelectorAll('.layout-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.layout === layout);
            });

            // Save preference
            localStorage.setItem('gk_layout', layout);

            // Update resizer orientation data
            const resizer = document.getElementById('resizer');
            resizer.className = layout === 'side' ? 'resizer resizer-v' : 'resizer resizer-h';
            if (layout === 'focus') resizer.style.display = 'none';

            // Reset flex sizes when switching layouts
            const contentPanel = document.getElementById('contentPanel');
            const workbenchPanel = document.getElementById('workbenchPanel');
            
            // Load saved ratio or use default
            const savedRatio = localStorage.getItem(`gk_ratio_${layout}`);
            if (savedRatio) {
                contentPanel.style.flexBasis = savedRatio + '%';
            } else {
                contentPanel.style.flexBasis = '45%';
            }
        }

        function toggleFocus() {
            focusOnContent = !focusOnContent;
            document.body.classList.toggle('focus-content', focusOnContent);
            const btn = document.getElementById('focusToggle');
            btn.textContent = focusOnContent ? '💻 Switch to IDE' : '📹 Switch to Lesson';
        }

        // Layout button clicks
        document.querySelectorAll('.layout-btn').forEach(btn => {
            btn.addEventListener('click', () => setLayout(btn.dataset.layout));
        });

        // Initialize layout
        setLayout(currentLayout);

        /* ========== LESSON NAVIGATION ========== */
        function selectLesson(lessonId) {
            // Update sidebar active state
            document.querySelectorAll('.lesson-nav-item').forEach(item => {
                item.classList.toggle('active', item.dataset.lessonId == lessonId);
            });

            // Show/hide content blocks
            document.querySelectorAll('.lesson-content-block').forEach(block => {
                block.style.display = 'none';
            });

            const target = document.getElementById(`lessonContent_${lessonId}`);
            if (target) {
                target.style.display = '';
                target.scrollTop = 0;
            }

            activeLessonId = lessonId;

            // In focus mode, switch to content view when clicking a lesson
            if (currentLayout === 'focus' && !focusOnContent) {
                toggleFocus();
            }
        }

        // Initialize first lesson
        const firstLessonNav = document.querySelector('.lesson-nav-item');
        if (firstLessonNav) {
            activeLessonId = firstLessonNav.dataset.lessonId;
        }

        /* ========== RESIZER DRAG ========== */
        (function() {
            const resizer = document.getElementById('resizer');
            const contentPanel = document.getElementById('contentPanel');
            const workbenchPanel = document.getElementById('workbenchPanel');
            const mainArea = document.querySelector('.main-area');

            let isDragging = false;

            resizer.addEventListener('mousedown', (e) => {
                if (currentLayout === 'focus') return;
                isDragging = true;
                document.body.classList.add('is-resizing');
                resizer.classList.add('dragging');
                e.preventDefault();
            });

            document.addEventListener('mousemove', (e) => {
                if (!isDragging) return;

                if (currentLayout === 'split') {
                    // top/bottom — use Y
                    const rect = mainArea.getBoundingClientRect();
                    const offset = e.clientY - rect.top;
                    const pct = (offset / rect.height) * 100;
                    const clamped = Math.max(15, Math.min(85, pct));
                    contentPanel.style.flexBasis = clamped + '%';
                    localStorage.setItem('gk_ratio_split', clamped);
                } else if (currentLayout === 'side') {
                    // left/right — use X
                    const rect = mainArea.getBoundingClientRect();
                    const offset = e.clientX - rect.left;
                    const pct = (offset / rect.width) * 100;
                    const clamped = Math.max(15, Math.min(85, pct));
                    contentPanel.style.flexBasis = clamped + '%';
                    localStorage.setItem('gk_ratio_side', clamped);
                }
            });

            document.addEventListener('mouseup', () => {
                if (isDragging) {
                    isDragging = false;
                    document.body.classList.remove('is-resizing');
                    resizer.classList.remove('dragging');
                }
            });

            // Double-click to reset to 50/50
            resizer.addEventListener('dblclick', () => {
                contentPanel.style.flexBasis = '50%';
                localStorage.setItem(`gk_ratio_${currentLayout}`, 50);
            });
        })();

        /* ========== TIMER ========== */
        function updateTimer() {
            const now = new Date();
            const diff = EXPIRES_AT - now;
            if (diff <= 0) {
                document.getElementById('timer').textContent = 'Expired';
                return;
            }
            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            document.getElementById('timer').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        /* ========== POLLING ========== */
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

        async function probeAndLoad(codeUrl, attempt = 1) {
            const maxAttempts = 20;
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
                console.warn('Probe timeout — loading iframe anyway');
                onLabReady(codeUrl);
            }
        }

        function onLabReady(codeUrl) {
            labRunning = true;

            const badge = document.getElementById('statusBadge');
            badge.classList.remove('provisioning');
            badge.classList.add('running');
            document.getElementById('statusText').textContent = 'Running';

            const panel = document.getElementById('workbenchPanel');
            panel.innerHTML = '<iframe class="workbench-iframe" src="' + codeUrl + '"></iframe>';

            startHeartbeat();
            startIdleTracking();
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

        /* ========== HEARTBEAT ========== */
        function startHeartbeat() {
            heartbeatInterval = setInterval(sendHeartbeat, HEARTBEAT_INTERVAL_MS);
            sendHeartbeat();
        }

        async function sendHeartbeat() {
            if (!labRunning || labStopped) return;
            try {
                await fetch(`/api/lab-sessions/${SESSION_ID}/heartbeat`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                });
            } catch (error) {
                console.error('Heartbeat error:', error);
            }
        }

        /* ========== IDLE TRACKING ========== */
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

        /* ========== STOP LAB ========== */
        async function stopLab(reason = 'Lab stopped') {
            if (labStopped) return;
            labStopped = true;
            try {
                await fetch(`/api/lab-sessions/${SESSION_ID}/stop`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                });
            } catch (error) {
                console.error('Stop error:', error);
            }
            window.location.href = '{{ route("courses.show", $module->slug ?? "k8s-basics") }}';
        }

        document.getElementById('stopBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to stop this lab?')) stopLab();
        });

        window.addEventListener('beforeunload', function() {
            if (labRunning && !labStopped) {
                navigator.sendBeacon(`/api/lab-sessions/${SESSION_ID}/stop`, JSON.stringify({
                    _token: document.querySelector('meta[name="csrf-token"]').content
                }));
            }
        });

        /* ========== QUIZ ========== */
        function submitQuiz(lessonId) {
            const quizSection = document.getElementById(`quiz_${lessonId}`);
            const questions = quizSection.querySelectorAll('.quiz-question');
            const resultDiv = document.getElementById(`quiz_result_${lessonId}`);

            let correctCount = 0;
            let totalQuestions = questions.length;

            questions.forEach((question) => {
                const correctAnswer = question.dataset.correct;
                const feedback = question.querySelector('.quiz-feedback');
                const correctEl = feedback.querySelector('.quiz-correct');
                const incorrectEl = feedback.querySelector('.quiz-incorrect');
                const options = question.querySelectorAll('.quiz-option');

                let selectedAnswer = '';
                const selectedRadio = question.querySelector('input[type="radio"]:checked');
                const textInput = question.querySelector('.quiz-input');

                if (selectedRadio) selectedAnswer = selectedRadio.value;
                else if (textInput) selectedAnswer = textInput.value.trim();

                const isCorrect = selectedAnswer.toLowerCase() === correctAnswer.toLowerCase();

                options.forEach(opt => {
                    opt.style.borderColor = 'var(--border)';
                    opt.style.background = '';
                });

                feedback.style.display = 'block';

                if (isCorrect) {
                    correctCount++;
                    correctEl.style.display = 'block';
                    incorrectEl.style.display = 'none';
                    feedback.style.background = 'rgba(16, 185, 129, 0.1)';
                    feedback.style.border = '1px solid rgba(16, 185, 129, 0.3)';
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

            const percentage = Math.round((correctCount / totalQuestions) * 100);
            const passed = percentage >= 70;

            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `
                <div style="font-size: 1.3rem; margin-bottom: 0.4rem;">${passed ? '🎉' : '📝'}</div>
                <div style="font-weight: 700; color: ${passed ? '#10b981' : '#ef4444'};">
                    ${correctCount} of ${totalQuestions} correct (${percentage}%)
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">
                    ${passed ? 'Great job! Quiz passed.' : 'You need 70% to pass. Try again!'}
                </div>
            `;
            resultDiv.style.background = passed ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)';
            resultDiv.style.border = `1px solid ${passed ? 'rgba(16, 185, 129, 0.3)' : 'rgba(239, 68, 68, 0.3)'}`;

            if (passed) {
                const completionBtn = document.getElementById(`quizCompletionBtn_${lessonId}`);
                if (completionBtn) {
                    completionBtn.innerHTML = `
                        <span style="font-size: 1.1rem;">✅</span>
                        <div><strong>Quiz Passed!</strong><div style="font-size: 0.75rem; opacity: 0.8;">Lesson marked as complete.</div></div>
                    `;
                    completionBtn.style.background = 'rgba(16, 185, 129, 0.1)';
                    completionBtn.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                    completionBtn.style.color = '#10b981';
                    completionBtn.className = 'completion-done';
                }

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

        /* ========== MARK LESSON COMPLETE ========== */
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
                        <div class="completion-done">
                            <span style="font-size: 1.1rem;">✅</span>
                            <div><strong>Lesson Completed!</strong><div style="font-size: 0.75rem; opacity: 0.8;">Great job!</div></div>
                        </div>
                    `;
                    // Update sidebar
                    const navItem = document.querySelector(`.lesson-nav-item[data-lesson-id="${lessonId}"]`);
                    if (navItem) navItem.classList.add('completed');
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

        /* ========== INIT ========== */
        updateTimer();
        setInterval(updateTimer, 1000);

        @if($session->status === 'running' && $session->code_url)
            probeAndLoad('{{ $codeUrl }}');
        @else
            pollInterval = setInterval(pollStatus, 3000);
            pollStatus();
        @endif
    </script>
</body>

</html>