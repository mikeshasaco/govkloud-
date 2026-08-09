@extends('layouts.govkloud')

@section('title', $challenge->title . ' - Problems - GovKloud')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Override main container for full-width workspace */
        .container {
            max-width: 100% !important;
            padding: 0 !important;
        }

        /* Top Bar */
        .workspace-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1.25rem;
            background: var(--gk-dark);
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .back-btn {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 1.25rem;
            transition: color 0.2s;
            display: flex;
            align-items: center;
        }

        .back-btn:hover {
            color: var(--gk-cyan);
        }

        .topbar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
        }

        .topbar-badges {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .tb-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .tb-badge.kubernetes { background: rgba(6, 182, 212, 0.15); color: #06b6d4; }
        .tb-badge.terraform { background: rgba(139, 92, 246, 0.15); color: #a78bfa; }
        .tb-badge.docker { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }

        .tb-badge.beginner { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
        .tb-badge.medium { background: rgba(249, 115, 22, 0.15); color: #f97316; }
        .tb-badge.hard { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .timer {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-family: 'JetBrains Mono', monospace;
        }

        .progress-dots {
            display: flex;
            gap: 0.3rem;
            align-items: center;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .progress-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--border);
        }

        .progress-dot.completed {
            background: #22c55e;
        }

        .progress-dot.active {
            background: var(--gk-cyan);
        }

        .submit-btn {
            padding: 0.5rem 1.25rem;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            color: var(--gk-navy);
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(210, 180, 140, 0.3);
        }

        .submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Three-Panel Layout */
        .workspace {
            display: flex;
            height: calc(100vh - 140px);
            overflow: hidden;
        }

        /* Left Panel - Description */
        .panel-left {
            width: 280px;
            min-width: 250px;
            background: var(--gk-slate);
            border-right: 1px solid var(--border);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .panel-section {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .panel-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
        }

        .challenge-desc {
            font-size: 0.85rem;
            line-height: 1.7;
            color: var(--text);
        }

        .challenge-desc code {
            background: rgba(210, 180, 140, 0.1);
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            color: var(--gk-cyan);
        }

        /* Objectives */
        .objectives-list {
            list-style: none;
            padding: 0;
        }

        .objectives-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
            color: var(--text);
        }

        .obj-check {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .obj-check.done {
            background: #22c55e;
            border-color: #22c55e;
            color: #fff;
        }

        /* Tutorial Video */
        .video-section {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            background: #000;
            aspect-ratio: 16 / 9;
        }

        .video-section iframe,
        .video-section video {
            width: 100%;
            height: 100%;
            border: none;
        }

        .video-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.6);
            color: var(--text-muted);
            gap: 0.5rem;
        }

        .video-placeholder .play-icon {
            width: 48px;
            height: 48px;
            background: rgba(210, 180, 140, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .video-placeholder .play-icon:hover {
            background: rgba(210, 180, 140, 0.5);
        }

        .video-caption {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        /* Hints */
        .hint-item {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 0.5rem;
            overflow: hidden;
        }

        .hint-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.6rem 0.75rem;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text);
        }

        .hint-header:hover {
            background: rgba(210, 180, 140, 0.05);
        }

        .hint-reveal-btn {
            padding: 0.2rem 0.6rem;
            background: rgba(210, 180, 140, 0.15);
            border: 1px solid rgba(210, 180, 140, 0.3);
            border-radius: 6px;
            color: var(--gk-cyan);
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .hint-reveal-btn:hover {
            background: rgba(210, 180, 140, 0.25);
        }

        .hint-content {
            padding: 0 0.75rem 0.75rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .hint-locked {
            filter: blur(4px);
            user-select: none;
            pointer-events: none;
        }

        .hint-lock-icon {
            opacity: 0.5;
        }

        /* Show Solution Button */
        .solution-btn {
            width: 100%;
            padding: 0.6rem;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .solution-btn:hover {
            border-color: var(--gk-cyan);
            color: var(--gk-cyan);
        }

        /* Solution Panel (shown after reveal) */
        .solution-panel {
            display: none;
            padding: 1rem;
            background: rgba(34, 197, 94, 0.05);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 8px;
            margin-top: 0.75rem;
        }

        .solution-panel.visible {
            display: block;
        }

        .solution-panel h4 {
            font-size: 0.85rem;
            color: #22c55e;
            margin-bottom: 0.5rem;
        }

        .solution-panel pre {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 6px;
            padding: 0.75rem;
            overflow-x: auto;
            font-size: 0.75rem;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text);
            margin-bottom: 0.75rem;
        }

        .solution-explanation {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Center Panel - Code Editor */
        .panel-center {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 300px;
            border-right: 1px solid var(--border);
        }

        .editor-tabs {
            display: flex;
            background: var(--gk-dark);
            border-bottom: 1px solid var(--border);
            overflow-x: auto;
        }

        .editor-tab {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 1rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            white-space: nowrap;
            background: transparent;
            border-top: none;
            border-left: none;
            border-right: 1px solid var(--border);
            font-family: 'JetBrains Mono', monospace;
            transition: all 0.15s;
        }

        .editor-tab:hover {
            color: var(--text);
            background: rgba(210, 180, 140, 0.05);
        }

        .editor-tab.active {
            color: var(--text);
            border-bottom-color: var(--gk-cyan);
            background: rgba(210, 180, 140, 0.05);
        }

        .editor-tab .tab-icon {
            font-size: 0.75rem;
        }

        .editor-area {
            flex: 1;
            position: relative;
        }

        .code-editor {
            width: 100%;
            height: 100%;
            background: #0d1117;
            color: #e6edf3;
            border: none;
            padding: 1rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            line-height: 1.6;
            resize: none;
            outline: none;
            tab-size: 2;
        }

        .editor-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.4rem 0.75rem;
            background: var(--gk-dark);
            border-top: 1px solid var(--border);
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .editor-footer-btns {
            display: flex;
            gap: 0.5rem;
        }

        .editor-btn {
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-muted);
        }

        .editor-btn:hover {
            border-color: var(--gk-cyan);
            color: var(--gk-cyan);
        }

        .editor-btn.save {
            background: rgba(210, 180, 140, 0.15);
            border-color: rgba(210, 180, 140, 0.3);
            color: var(--gk-cyan);
        }

        .apply-cluster-btn {
            background: rgba(34, 197, 94, 0.15) !important;
            border-color: rgba(34, 197, 94, 0.4) !important;
            color: #22c55e !important;
            font-weight: 600 !important;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .apply-cluster-btn:hover {
            background: rgba(34, 197, 94, 0.25) !important;
            border-color: rgba(34, 197, 94, 0.6) !important;
            transform: translateY(-1px);
        }

        .apply-cluster-btn.applying {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Right Panel - Terminal */
        .panel-right {
            width: 380px;
            min-width: 280px;
            display: flex;
            flex-direction: column;
            background: #000;
        }

        .terminal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.75rem;
            background: #111;
            border-bottom: 1px solid #222;
        }

        .terminal-header-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #888;
            font-family: 'JetBrains Mono', monospace;
        }

        .terminal-clear-btn {
            padding: 0.15rem 0.5rem;
            background: transparent;
            border: 1px solid #333;
            border-radius: 4px;
            color: #666;
            font-size: 0.65rem;
            cursor: pointer;
            font-family: 'JetBrains Mono', monospace;
        }

        .terminal-clear-btn:hover {
            border-color: #555;
            color: #999;
        }

        .terminal-body {
            flex: 1;
            padding: 0.75rem;
            overflow-y: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            line-height: 1.5;
        }

        .terminal-output {
            white-space: pre-wrap;
            word-break: break-all;
        }

        .terminal-output .cmd {
            color: #e6edf3;
        }

        .terminal-output .prompt {
            color: #22c55e;
        }

        .terminal-output .output {
            color: #06b6d4;
        }

        .terminal-output .error {
            color: #ef4444;
        }

        .terminal-input-line {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .terminal-prompt {
            color: #22c55e;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            white-space: pre;
        }

        .terminal-input {
            flex: 1;
            background: transparent;
            border: none;
            color: #e6edf3;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            outline: none;
            caret-color: #22c55e;
        }

        /* Resize handles */
        .resize-handle {
            width: 4px;
            cursor: col-resize;
            background: transparent;
            transition: background 0.2s;
        }

        .resize-handle:hover {
            background: var(--gk-cyan);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .workspace {
                flex-direction: column;
                height: auto;
            }

            .panel-left, .panel-center, .panel-right {
                width: 100% !important;
                min-height: 300px;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }
        }

        /* ── Problem Type Badge ── */
        .tb-badge.problem-type {
            background: rgba(210, 180, 140, 0.15);
            color: var(--gk-tan);
            font-size: 0.65rem;
        }

        /* ── Environment Overlay ── */
        .env-overlay {
            position: fixed;
            inset: 0;
            background: rgba(10, 15, 30, 0.92);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .env-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
        .env-overlay-content {
            text-align: center;
            max-width: 420px;
        }
        .env-overlay-spinner {
            width: 56px;
            height: 56px;
            border: 3px solid rgba(210, 180, 140, 0.15);
            border-top-color: var(--gk-cyan);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1.5rem;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .env-overlay h3 {
            color: var(--text);
            font-size: 1.2rem;
            margin-bottom: 0.75rem;
        }
        .env-overlay-steps {
            list-style: none;
            padding: 0;
            text-align: left;
            margin: 1.5rem auto;
            max-width: 280px;
        }
        .env-overlay-steps li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
            color: var(--text-muted);
            font-size: 0.85rem;
            transition: color 0.3s;
        }
        .env-overlay-steps li.active { color: var(--gk-cyan); }
        .env-overlay-steps li.done { color: #22c55e; }
        .env-overlay-steps li .step-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid currentColor;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            flex-shrink: 0;
        }
        .env-overlay-steps li.done .step-icon { background: #22c55e; border-color: #22c55e; color: #fff; }
        .env-overlay-steps li.active .step-icon { border-color: var(--gk-cyan); }
        .env-overlay-error {
            color: #ef4444;
            margin-top: 1rem;
            font-size: 0.85rem;
        }

        /* ── Start Button (for cluster problems) ── */
        .start-env-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .start-env-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3); }
        .start-env-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .start-env-btn.running { background: #22c55e; }

        /* ── Validation Results Panel ── */
        .validation-overlay {
            position: fixed;
            inset: 0;
            background: rgba(10, 15, 30, 0.92);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .validation-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
        .validation-card {
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            max-width: 480px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        .validation-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .validation-header h3 {
            font-size: 1.3rem;
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        .validation-header .score-badge {
            display: inline-block;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .score-badge.passed { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
        .score-badge.failed { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
        .validation-checks {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0;
        }
        .validation-checks li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.6rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 0.85rem;
            color: var(--text);
        }
        .validation-checks li:last-child { border-bottom: none; }
        .check-icon {
            font-size: 1rem;
            flex-shrink: 0;
            line-height: 1.4;
        }
        .validation-points {
            text-align: center;
            font-size: 1.1rem;
            color: var(--gk-cyan);
            font-weight: 700;
            margin: 1rem 0;
        }
        .validation-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            margin-top: 1.5rem;
        }
        .validation-actions button {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .val-btn-close {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-muted);
        }
        .val-btn-close:hover { border-color: var(--text); color: var(--text); }
        .val-btn-retry {
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            color: var(--gk-navy);
            border: none;
        }
        .val-btn-retry:hover { transform: translateY(-1px); }
    </style>
@endpush

@section('content')
    <!-- Top Bar -->
    <div class="workspace-topbar">
        <div class="topbar-left">
            <a href="{{ route('problems.index') }}" class="back-btn" title="Back to Problems">←</a>
            <span class="topbar-title">{{ $challenge->title }}</span>
            <div class="topbar-badges">
                <span class="tb-badge {{ $challenge->category }}">{{ ucfirst($challenge->category) }}</span>
                <span class="tb-badge {{ $challenge->difficulty }}">{{ ucfirst($challenge->difficulty) }}</span>
                @if($challenge->problem_type)
                    <span class="tb-badge problem-type">{{ $challenge->getProblemTypeLabel() }}</span>
                @endif
                @if($challenge->points)
                    <span class="tb-badge problem-type">{{ $challenge->points }} pts</span>
                @endif
            </div>
        </div>
        <div class="topbar-right">
            <div class="timer">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
                <span id="timerDisplay">00:00</span>
            </div>
            @if($challenge->needsCluster())
                <button class="start-env-btn" id="startEnvBtn" onclick="startEnvironment()">
                    ▶ Start Environment
                </button>
            @endif
            @if($prevChallenge)
                <a href="{{ route('problems.show', $prevChallenge->slug) }}" style="color:var(--text-muted);text-decoration:none;font-size:0.8rem;">← Prev</a>
            @endif
            @if($nextChallenge)
                <a href="{{ route('problems.show', $nextChallenge->slug) }}" style="color:var(--text-muted);text-decoration:none;font-size:0.8rem;">Next →</a>
            @endif
            <button class="submit-btn" id="submitBtn" onclick="submitChallenge()">Submit</button>
        </div>
    </div>

    <!-- Environment Provisioning Overlay -->
    <div class="env-overlay" id="envOverlay">
        <div class="env-overlay-content">
            <div class="env-overlay-spinner" id="envSpinner"></div>
            <h3 id="envOverlayTitle">Starting Environment...</h3>
            <ul class="env-overlay-steps">
                <li id="envStep1" class="active"><span class="step-icon">1</span> Provisioning cluster</li>
                <li id="envStep2"><span class="step-icon">2</span> Loading scenario</li>
                <li id="envStep3"><span class="step-icon">3</span> Environment ready</li>
            </ul>
            <div class="env-overlay-error" id="envError" style="display:none;"></div>
        </div>
    </div>

    <!-- Validation Results Overlay -->
    <div class="validation-overlay" id="validationOverlay">
        <div class="validation-card">
            <div class="validation-header">
                <h3 id="valTitle">Checking...</h3>
                <span class="score-badge" id="valBadge"></span>
            </div>
            <ul class="validation-checks" id="valChecks"></ul>
            <div class="validation-points" id="valPoints" style="display:none;"></div>
            <div class="validation-actions">
                <button class="val-btn-close" onclick="closeValidation()">Close</button>
                <button class="val-btn-retry" id="valRetryBtn" onclick="closeValidation()" style="display:none;">Try Again</button>
            </div>
        </div>
    </div>

    <!-- Three-Panel Workspace -->
    <div class="workspace">
        <!-- LEFT PANEL: Description -->
        <div class="panel-left">
            <!-- Challenge Description -->
            <div class="panel-section">
                <div class="panel-section-label">Challenge</div>
                <div class="challenge-desc">{!! nl2br(e($challenge->description)) !!}</div>
            </div>

            <!-- Objectives -->
            @if($challenge->getCommandFlows() && isset($challenge->getCommandFlows()['required_commands']))
                <div class="panel-section">
                    <div class="panel-section-label">Objectives</div>
                    <ul class="objectives-list" id="objectivesList">
                        @foreach($challenge->getCommandFlows()['required_commands'] as $i => $cmd)
                            <li>
                                <span class="obj-check" id="obj-{{ $i }}">{{ ' ' }}</span>
                                <span>{{ is_array($cmd) ? ($cmd['label'] ?? $cmd['command'] ?? '') : $cmd }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tutorial Video -->
            @if($challenge->hasVideo())
                <div class="panel-section">
                    <div class="panel-section-label">📹 Tutorial Video</div>
                    <div class="video-section">
                        @if($challenge->video_url)
                            <iframe src="{{ $challenge->embed_video_url }}"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    loading="lazy"></iframe>
                        @elseif($challenge->video_file)
                            <video controls preload="metadata">
                                <source src="{{ $challenge->getVideoSource() }}" type="video/mp4">
                                Your browser does not support video playback.
                            </video>
                        @endif
                    </div>
                    <div class="video-caption">Watch before starting</div>
                </div>
            @endif

            <!-- Hints -->
            @if($challenge->getHints())
                <div class="panel-section">
                    <div class="panel-section-label">
                        💡 Hints (<span id="hintsUsedCount">{{ $attempt->hints_used }}</span>/{{ count($challenge->getHints()) }} revealed)
                    </div>
                    @foreach($challenge->getHints() as $i => $hint)
                        <div class="hint-item" id="hint-{{ $i }}">
                            <div class="hint-header" onclick="revealHint({{ $i }})">
                                <span>Hint {{ $i + 1 }}</span>
                                @if($i < $attempt->hints_used)
                                    <span style="font-size:0.7rem;color:var(--text-muted);">▲</span>
                                @else
                                    <button class="hint-reveal-btn" id="hintBtn-{{ $i }}">
                                        <span class="hint-lock-icon">🔒</span> Reveal
                                    </button>
                                @endif
                            </div>
                            <div class="hint-content {{ $i >= $attempt->hints_used ? 'hint-locked' : '' }}" id="hintContent-{{ $i }}">
                                {{ $hint }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Show Solution -->
            <div class="panel-section" style="border-bottom: none;">
                <button class="solution-btn" id="solutionBtn" onclick="showSolution()">
                    Show Solution
                </button>
                <div class="solution-panel" id="solutionPanel">
                    <h4>✅ Solution</h4>
                    <div id="solutionContent">
                        {{-- Populated via JS after clicking Show Solution --}}
                    </div>
                    @if($challenge->solution_explanation)
                        <div class="solution-explanation">
                            {!! nl2br(e($challenge->solution_explanation)) !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- CENTER PANEL: Code Editor -->
        <div class="panel-center">
            <!-- File Tabs -->
            <div class="editor-tabs" id="editorTabs">
                @php $files = $challenge->getInitialFiles(); $first = true; @endphp
                @forelse($files as $filename => $content)
                    <button class="editor-tab {{ $first ? 'active' : '' }}"
                            data-file="{{ $filename }}"
                            onclick="switchTab('{{ $filename }}')">
                        <span class="tab-icon">📄</span> {{ $filename }}
                    </button>
                    @php $first = false; @endphp
                @empty
                    <button class="editor-tab active" data-file="main" onclick="switchTab('main')">
                        <span class="tab-icon">📄</span> main
                    </button>
                @endforelse
            </div>

            <!-- Code Editor Area -->
            <div class="editor-area">
                <textarea class="code-editor" id="codeEditor"
                          spellcheck="false"
                          placeholder="Write your code here...">{{ $files ? reset($files) : '' }}</textarea>
            </div>

            <!-- Editor Footer -->
            <div class="editor-footer">
                <span id="cursorPos">Ln 1, Col 1</span>
                <span id="langLabel">{{ ($langMap = $challenge->getFileLanguageMap()) ? reset($langMap) : 'YAML' }}</span>
                <div class="editor-footer-btns">
                    @if($challenge->needsCluster())
                    <button class="editor-btn apply-cluster-btn" id="applyToClusterBtn" onclick="applyToCluster()" title="Apply the editor YAML to your cluster">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Apply to Cluster
                    </button>
                    @endif
                    <button class="editor-btn save" onclick="saveProgress()">Save</button>
                    <button class="editor-btn" onclick="resetEditor()">Reset</button>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Terminal -->
        <div class="panel-right">
            <div class="terminal-header">
                <span class="terminal-header-label">Terminal</span>
                <button class="terminal-clear-btn" onclick="clearTerminal()">Clear</button>
            </div>
            <div class="terminal-body" id="terminalBody">
                <div class="terminal-output" id="terminalOutput"></div>
                <div class="terminal-input-line">
                    <span class="terminal-prompt">$ </span>
                    <input type="text" class="terminal-input" id="terminalInput"
                           autofocus autocomplete="off" spellcheck="false"
                           onkeydown="handleTerminalInput(event)">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════════
// CHALLENGE CONFIGURATION (from database)
// ═══════════════════════════════════════════════════════════════
const CHALLENGE = {
    slug: @json($challenge->slug),
    category: @json($challenge->category),
    problemType: @json($challenge->problem_type ?? 'build'),
    requiresCluster: @json((bool)$challenge->needsCluster()),
    initialFiles: @json($challenge->getInitialFiles()),
    fileLanguageMap: @json($challenge->getFileLanguageMap()),
    commandFlows: @json($challenge->getCommandFlows()),
    initialState: @json($challenge->getInitialState()),
    solutionFiles: @json($challenge->getSolutionFiles()),
    hints: @json($challenge->getHints()),
    validationRules: @json($challenge->getValidationRules()),
};

const ATTEMPT = {
    savedFiles: @json($attempt->user_files_json),
    commandsExecuted: @json($attempt->commands_executed ?? []),
    hintsUsed: {{ $attempt->hints_used }},
    status: @json($attempt->status),
    labSessionId: @json($attempt->lab_session_id),
};

const CSRF_TOKEN = '{{ csrf_token() }}';

// ═══════════════════════════════════════════════════════════════
// FILE STATE
// ═══════════════════════════════════════════════════════════════
let currentFile = Object.keys(CHALLENGE.initialFiles)[0] || 'main';
let files = ATTEMPT.savedFiles || { ...CHALLENGE.initialFiles };
let commandHistory = [];
let historyIndex = -1;
let commandsExecuted = ATTEMPT.commandsExecuted || [];
let startTime = Date.now();
let envSessionReady = !CHALLENGE.requiresCluster; // cluster problems start as false, verified via /status API
let isSubmitting = false;

// Auto-stop environment when user leaves the page
function stopEnvironment() {
    if (!envSessionReady || !CHALLENGE.requiresCluster) return;
    // Use FormData with CSRF token so Laravel accepts it
    const formData = new FormData();
    formData.append('_token', CSRF_TOKEN);
    navigator.sendBeacon(`/api/problems/${CHALLENGE.slug}/stop`, formData);
    envSessionReady = false; // Prevent double-stops
}

// Stop on tab close or navigate away
window.addEventListener('beforeunload', stopEnvironment);

// Stop on back/forward navigation
window.addEventListener('pagehide', stopEnvironment);


// ═══════════════════════════════════════════════════════════════
// TERMINAL STATE ENGINE
// ═══════════════════════════════════════════════════════════════
let clusterState = {
    pods: [],
    secrets: [],
    deployments: [],
    services: [],
    configmaps: [],
    namespaces: [{ name: 'default' }],
    ingresses: [],
    networkpolicies: [],
    roles: [],
    rolebindings: [],
    serviceaccounts: [{ name: 'default', namespace: 'default' }],
    persistentvolumes: [],
    persistentvolumeclaims: [],
    ...(CHALLENGE.initialState || {}),
};

// ═══════════════════════════════════════════════════════════════
// TAB MANAGEMENT
// ═══════════════════════════════════════════════════════════════
function switchTab(filename) {
    // Save current file content
    files[currentFile] = document.getElementById('codeEditor').value;

    // Switch
    currentFile = filename;
    document.getElementById('codeEditor').value = files[filename] || '';

    // Update active tab
    document.querySelectorAll('.editor-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.file === filename);
    });

    // Update language label
    const langMap = CHALLENGE.fileLanguageMap || {};
    document.getElementById('langLabel').textContent =
        (langMap[filename] || filename.split('.').pop() || 'text').toUpperCase();
}

// ═══════════════════════════════════════════════════════════════
// TIMER
// ═══════════════════════════════════════════════════════════════
setInterval(() => {
    const elapsed = Math.floor((Date.now() - startTime) / 1000);
    const min = String(Math.floor(elapsed / 60)).padStart(2, '0');
    const sec = String(elapsed % 60).padStart(2, '0');
    document.getElementById('timerDisplay').textContent = `${min}:${sec}`;
}, 1000);

// ═══════════════════════════════════════════════════════════════
// CURSOR POSITION TRACKING
// ═══════════════════════════════════════════════════════════════
document.getElementById('codeEditor').addEventListener('keyup', function(e) {
    const val = this.value.substring(0, this.selectionStart);
    const lines = val.split('\n');
    document.getElementById('cursorPos').textContent =
        `Ln ${lines.length}, Col ${lines[lines.length - 1].length + 1}`;
});

// Handle Tab key in editor
document.getElementById('codeEditor').addEventListener('keydown', function(e) {
    if (e.key === 'Tab') {
        e.preventDefault();
        const start = this.selectionStart;
        const end = this.selectionEnd;
        this.value = this.value.substring(0, start) + '  ' + this.value.substring(end);
        this.selectionStart = this.selectionEnd = start + 2;
    }
});

// ═══════════════════════════════════════════════════════════════
// TERMINAL COMMAND HANDLER
// ═══════════════════════════════════════════════════════════════
function handleTerminalInput(event) {
    if (event.key === 'Enter') {
        const input = document.getElementById('terminalInput');
        const command = input.value.trim();
        input.value = '';

        if (!command) return;

        commandHistory.push(command);
        historyIndex = commandHistory.length;
        commandsExecuted.push(command);

        // Route to real cluster or simulated terminal
        if (CHALLENGE.requiresCluster && envSessionReady) {
            executeRealCommand(command);
        } else {
            const output = processCommand(command);
            appendToTerminal(command, output);
            checkObjectives();
        }
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        if (historyIndex > 0) {
            historyIndex--;
            document.getElementById('terminalInput').value = commandHistory[historyIndex];
        }
    } else if (event.key === 'ArrowDown') {
        event.preventDefault();
        if (historyIndex < commandHistory.length - 1) {
            historyIndex++;
            document.getElementById('terminalInput').value = commandHistory[historyIndex];
        } else {
            historyIndex = commandHistory.length;
            document.getElementById('terminalInput').value = '';
        }
    }
}

/**
 * Execute a command against the real vcluster via the kubectl proxy API.
 */
async function executeRealCommand(command) {
    // Show command immediately with a loading indicator
    const termOut = document.getElementById('terminalOutput');
    termOut.innerHTML += `<span class="prompt">$ </span><span class="cmd">${escapeHtml(command)}</span>\n`;
    termOut.innerHTML += `<span class="output" style="color:var(--text-muted);font-style:italic;">executing...</span>\n`;
    const body = document.getElementById('terminalBody');
    body.scrollTop = body.scrollHeight;

    try {
        const response = await fetch(`/api/problems/${CHALLENGE.slug}/exec`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ command }),
        });
        const data = await response.json();

        // Remove the "executing..." line and show real output
        const lines = termOut.innerHTML.split('\n');
        // Remove the last "executing..." line
        const execIdx = lines.findLastIndex(l => l.includes('executing...'));
        if (execIdx >= 0) lines.splice(execIdx, 1);
        termOut.innerHTML = lines.join('\n');

        if (data.output) {
            const isError = data.exit_code !== 0;
            termOut.innerHTML += `<span class="${isError ? 'error' : 'output'}">${escapeHtml(data.output)}</span>\n`;
        }
        body.scrollTop = body.scrollHeight;
    } catch (err) {
        const lines = termOut.innerHTML.split('\n');
        const execIdx = lines.findLastIndex(l => l.includes('executing...'));
        if (execIdx >= 0) lines.splice(execIdx, 1);
        termOut.innerHTML = lines.join('\n');
        termOut.innerHTML += `<span class="error">Error: Could not connect to environment. Try clicking "Start Environment".</span>\n`;
        body.scrollTop = body.scrollHeight;
    }
}

/**
 * Apply the current editor YAML to the real cluster.
 * Takes editor content → POST /api/problems/{slug}/apply → kubectl apply -f -
 */
async function applyToCluster() {
    if (!envSessionReady) {
        const termOut = document.getElementById('terminalOutput');
        termOut.innerHTML += `<span class="error">Error: No active environment. Click 'Start Environment' first.</span>\n`;
        document.getElementById('terminalBody').scrollTop = document.getElementById('terminalBody').scrollHeight;
        return;
    }

    // Get current editor content
    files[currentFile] = document.getElementById('codeEditor').value;
    const yaml = document.getElementById('codeEditor').value.trim();

    if (!yaml) {
        const termOut = document.getElementById('terminalOutput');
        termOut.innerHTML += `<span class="error">Error: Editor is empty. Write your YAML first.</span>\n`;
        return;
    }

    // Update button state
    const btn = document.getElementById('applyToClusterBtn');
    if (btn) {
        btn.classList.add('applying');
        btn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Applying...';
    }

    // Show in terminal
    const termOut = document.getElementById('terminalOutput');
    const body = document.getElementById('terminalBody');
    termOut.innerHTML += `<span class="prompt">$ </span><span class="cmd">kubectl apply -f ${escapeHtml(currentFile)}</span>\n`;
    termOut.innerHTML += `<span class="output" style="color:var(--text-muted);font-style:italic;">applying ${escapeHtml(currentFile)}...</span>\n`;
    body.scrollTop = body.scrollHeight;

    try {
        const response = await fetch(`/api/problems/${CHALLENGE.slug}/apply`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ yaml }),
        });
        const data = await response.json();

        // Remove "applying..." line
        const lines = termOut.innerHTML.split('\n');
        const applyIdx = lines.findLastIndex(l => l.includes('applying'));
        if (applyIdx >= 0) lines.splice(applyIdx, 1);
        termOut.innerHTML = lines.join('\n');

        const isError = data.exit_code !== 0;
        termOut.innerHTML += `<span class="${isError ? 'error' : 'output'}">${escapeHtml(data.output)}</span>\n`;
        body.scrollTop = body.scrollHeight;
    } catch (err) {
        const lines = termOut.innerHTML.split('\n');
        const applyIdx = lines.findLastIndex(l => l.includes('applying'));
        if (applyIdx >= 0) lines.splice(applyIdx, 1);
        termOut.innerHTML = lines.join('\n');
        termOut.innerHTML += `<span class="error">Error: Could not connect to environment.</span>\n`;
        body.scrollTop = body.scrollHeight;
    } finally {
        // Reset button
        if (btn) {
            btn.classList.remove('applying');
            btn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Apply to Cluster';
        }
    }
}
function appendToTerminal(command, output) {
    const termOut = document.getElementById('terminalOutput');
    let html = `<span class="prompt">$ </span><span class="cmd">${escapeHtml(command)}</span>\n`;

    if (output) {
        const isError = output.startsWith('error:') || output.startsWith('Error:');
        html += `<span class="${isError ? 'error' : 'output'}">${escapeHtml(output)}</span>\n`;
    }

    termOut.innerHTML += html;

    // Scroll to bottom
    const body = document.getElementById('terminalBody');
    body.scrollTop = body.scrollHeight;
}

function clearTerminal() {
    document.getElementById('terminalOutput').innerHTML = '';
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// ═══════════════════════════════════════════════════════════════
// COMMAND PROCESSING ENGINE
// ═══════════════════════════════════════════════════════════════
function processCommand(command) {
    // Save current editor content to files
    files[currentFile] = document.getElementById('codeEditor').value;

    // Check custom command flows first
    const flows = CHALLENGE.commandFlows || {};
    const customOutputs = flows.custom_outputs || {};

    // Check for exact match in custom outputs
    for (const [pattern, customOutput] of Object.entries(customOutputs)) {
        if (matchCommand(command, pattern)) {
            return resolveOutput(customOutput, command);
        }
    }

    // Parse command
    const parts = command.split(/\s+/);
    const tool = parts[0];

    switch (tool) {
        case 'kubectl':
            return handleKubectl(parts.slice(1), command);
        case 'terraform':
            return handleTerraform(parts.slice(1), command);
        case 'docker':
            return handleDocker(parts.slice(1), command);
        case 'cat':
            return handleCat(parts.slice(1));
        case 'ls':
            return Object.keys(files).join('\n') || '(no files)';
        case 'clear':
            clearTerminal();
            return '';
        case 'help':
            return 'Available commands: kubectl, terraform, docker, cat, ls, clear, help';
        default:
            return `error: command not found: ${tool}\nTry: kubectl, terraform, docker, cat, ls, help`;
    }
}

// ═══════════════════════════════════════════════════════════════
// KUBECTL HANDLER
// ═══════════════════════════════════════════════════════════════
function handleKubectl(args, fullCmd) {
    if (args.length === 0) return 'kubectl controls the Kubernetes cluster manager.\n\nUsage: kubectl [command]\n\nCommands:\n  apply, create, get, describe, delete, logs';

    const action = args[0];

    switch (action) {
        case 'apply': return kubectlApply(args.slice(1));
        case 'create': return kubectlCreate(args.slice(1));
        case 'get': return kubectlGet(args.slice(1));
        case 'describe': return kubectlDescribe(args.slice(1));
        case 'delete': return kubectlDelete(args.slice(1));
        case 'logs': return kubectlLogs(args.slice(1));
        case 'auth': return kubectlAuth(args.slice(1));
        default: return `error: unknown command "${action}" for "kubectl"`;
    }
}

function kubectlApply(args) {
    const fileFlag = args.indexOf('-f');
    if (fileFlag === -1) return 'error: required flag(s) "filename" not set';

    const filename = args[fileFlag + 1];
    if (!filename) return 'error: filename cannot be empty';

    const content = files[filename];
    if (!content) return `error: the path "${filename}" does not exist`;

    // Parse YAML (basic)
    try {
        const parsed = parseBasicYAML(content);
        if (!parsed.kind) return 'error: unable to decode: Object kind is missing in declaration';
        if (!parsed.metadata || !parsed.metadata.name) return 'error: metadata.name is required';

        // Validate against challenge requirements
        const flows = CHALLENGE.commandFlows || {};
        const validations = flows.validations || {};
        const key = `kubectl apply -f ${filename}`;
        if (validations[key]) {
            const v = validations[key];
            if (v.must_contain) {
                for (const req of v.must_contain) {
                    if (!content.includes(req)) {
                        return `error: validation failed: "${req}" is required in ${filename}`;
                    }
                }
            }
            if (v.must_have_fields) {
                for (const field of v.must_have_fields) {
                    if (!checkNestedField(parsed, field)) {
                        return `error: validation failed: field "${field}" is required`;
                    }
                }
            }
        }

        // Add to cluster state
        const kind = parsed.kind.toLowerCase();
        const name = parsed.metadata.name;
        const resource = { name, ...parsed.spec, _raw: parsed };

        const stateKey = kind + 's';
        if (clusterState[stateKey]) {
            // Remove existing with same name
            clusterState[stateKey] = clusterState[stateKey].filter(r => r.name !== name);
            clusterState[stateKey].push(resource);
        } else if (kind === 'networkpolicy') {
            clusterState.networkpolicies = clusterState.networkpolicies.filter(r => r.name !== name);
            clusterState.networkpolicies.push(resource);
        }

        return `${parsed.kind.toLowerCase()}/${name} created`;
    } catch (e) {
        return `error: error parsing ${filename}: ${e.message}`;
    }
}

function kubectlCreate(args) {
    if (args[0] === 'secret' && args[1] === 'generic') {
        const name = args[2];
        if (!name) return 'error: secret name is required';

        const data = {};
        args.slice(3).forEach(arg => {
            if (arg.startsWith('--from-literal=')) {
                const kv = arg.replace('--from-literal=', '');
                const eqIdx = kv.indexOf('=');
                if (eqIdx > 0) {
                    data[kv.substring(0, eqIdx)] = kv.substring(eqIdx + 1);
                }
            }
        });

        clusterState.secrets = clusterState.secrets.filter(s => s.name !== name);
        clusterState.secrets.push({ name, data, type: 'Opaque' });
        return `secret/${name} created`;
    }

    if (args[0] === 'configmap') {
        const name = args[1];
        if (!name) return 'error: configmap name is required';
        const data = {};
        args.slice(2).forEach(arg => {
            if (arg.startsWith('--from-literal=')) {
                const kv = arg.replace('--from-literal=', '');
                const eqIdx = kv.indexOf('=');
                if (eqIdx > 0) data[kv.substring(0, eqIdx)] = kv.substring(eqIdx + 1);
            }
        });
        clusterState.configmaps = clusterState.configmaps.filter(c => c.name !== name);
        clusterState.configmaps.push({ name, data });
        return `configmap/${name} created`;
    }

    if (args[0] === 'namespace') {
        const name = args[1];
        if (!name) return 'error: namespace name is required';
        clusterState.namespaces.push({ name });
        return `namespace/${name} created`;
    }

    if (args[0] === 'serviceaccount') {
        const name = args[1];
        if (!name) return 'error: serviceaccount name is required';
        clusterState.serviceaccounts.push({ name, namespace: 'default' });
        return `serviceaccount/${name} created`;
    }

    return `error: unknown resource type "${args[0]}"`;
}

function kubectlGet(args) {
    if (args.length === 0) return 'error: you must specify the type of resource to get';

    const resourceType = args[0].toLowerCase().replace(/s$/, '');

    const formatTable = (headers, rows) => {
        if (rows.length === 0) return `No resources found in default namespace.`;
        const colWidths = headers.map((h, i) =>
            Math.max(h.length, ...rows.map(r => String(r[i] || '').length))
        );
        const headerLine = headers.map((h, i) => h.padEnd(colWidths[i])).join('   ');
        const dataLines = rows.map(r =>
            r.map((cell, i) => String(cell || '').padEnd(colWidths[i])).join('   ')
        );
        return [headerLine, ...dataLines].join('\n');
    };

    switch (resourceType) {
        case 'pod':
            return formatTable(
                ['NAME', 'READY', 'STATUS', 'RESTARTS', 'AGE'],
                clusterState.pods.map(p => [p.name, '1/1', 'Running', '0', '5s'])
            );
        case 'secret':
            return formatTable(
                ['NAME', 'TYPE', 'DATA', 'AGE'],
                clusterState.secrets.map(s => [s.name, s.type || 'Opaque', Object.keys(s.data || {}).length, '5s'])
            );
        case 'deployment':
            return formatTable(
                ['NAME', 'READY', 'UP-TO-DATE', 'AVAILABLE', 'AGE'],
                clusterState.deployments.map(d => [d.name, '1/1', '1', '1', '5s'])
            );
        case 'service':
            return formatTable(
                ['NAME', 'TYPE', 'CLUSTER-IP', 'EXTERNAL-IP', 'PORT(S)', 'AGE'],
                [{ name: 'kubernetes', type: 'ClusterIP', ip: '10.0.0.1', ports: '443/TCP' },
                 ...clusterState.services].map(s => [s.name, s.type || 'ClusterIP', s.ip || '10.0.' + Math.floor(Math.random()*255) + '.' + Math.floor(Math.random()*255), '<none>', s.ports || '80/TCP', '5s'])
            );
        case 'configmap':
            return formatTable(
                ['NAME', 'DATA', 'AGE'],
                clusterState.configmaps.map(c => [c.name, Object.keys(c.data || {}).length, '5s'])
            );
        case 'namespace':
            return formatTable(
                ['NAME', 'STATUS', 'AGE'],
                clusterState.namespaces.map(n => [n.name, 'Active', '5s'])
            );
        case 'networkpolic':
        case 'networkpolicy':
            return formatTable(
                ['NAME', 'POD-SELECTOR', 'AGE'],
                clusterState.networkpolicies.map(n => [n.name, '<all>', '5s'])
            );
        case 'serviceaccount':
            return formatTable(
                ['NAME', 'SECRETS', 'AGE'],
                clusterState.serviceaccounts.map(s => [s.name, '0', '5s'])
            );
        default:
            return `error: the server doesn't have a resource type "${args[0]}"`;
    }
}

function kubectlDescribe(args) {
    if (args.length < 2) return 'error: you must specify a resource type and name';
    const type = args[0].toLowerCase().replace(/s$/, '');
    const name = args[1];

    if (type === 'pod') {
        const pod = clusterState.pods.find(p => p.name === name);
        if (!pod) return `Error from server (NotFound): pods "${name}" not found`;

        let desc = `Name:         ${name}\nNamespace:    default\nStatus:       Running\nIP:           10.244.0.${Math.floor(Math.random() * 255)}`;

        // Show containers
        if (pod._raw && pod._raw.spec && pod._raw.spec.containers) {
            desc += '\nContainers:';
            pod._raw.spec.containers.forEach(c => {
                desc += `\n  ${c.name}:`;
                desc += `\n    Image:    ${c.image || 'unknown'}`;
                if (c.ports) desc += `\n    Ports:    ${c.ports.map(p => p.containerPort + '/TCP').join(', ')}`;
                if (c.env) {
                    desc += '\n    Environment:';
                    c.env.forEach(e => {
                        if (e.valueFrom && e.valueFrom.secretKeyRef) {
                            desc += `\n      ${e.name}:  <set to the key '${e.valueFrom.secretKeyRef.key}' of secret '${e.valueFrom.secretKeyRef.name}'>`;
                        } else if (e.valueFrom && e.valueFrom.configMapKeyRef) {
                            desc += `\n      ${e.name}:  <set to the key '${e.valueFrom.configMapKeyRef.key}' of config map '${e.valueFrom.configMapKeyRef.name}'>`;
                        } else {
                            desc += `\n      ${e.name}:  ${e.value || ''}`;
                        }
                    });
                }
                if (c.livenessProbe) desc += `\n    Liveness:   ${formatProbe(c.livenessProbe)}`;
                if (c.readinessProbe) desc += `\n    Readiness:  ${formatProbe(c.readinessProbe)}`;
            });
        }

        return desc;
    }

    if (type === 'secret') {
        const secret = clusterState.secrets.find(s => s.name === name);
        if (!secret) return `Error from server (NotFound): secrets "${name}" not found`;
        let desc = `Name:         ${name}\nNamespace:    default\nType:         ${secret.type || 'Opaque'}\n\nData\n====`;
        Object.keys(secret.data || {}).forEach(k => {
            desc += `\n${k}:  ${String(secret.data[k]).length} bytes`;
        });
        return desc;
    }

    return `Error from server (NotFound): ${type} "${name}" not found`;
}

function kubectlDelete(args) {
    if (args.length < 2) return 'error: you must specify a resource type and name';
    const type = args[0].toLowerCase().replace(/s$/, '');
    const name = args[1];
    const key = type + 's';
    if (clusterState[key]) {
        clusterState[key] = clusterState[key].filter(r => r.name !== name);
        return `${type} "${name}" deleted`;
    }
    return `error: the server doesn't have a resource type "${args[0]}"`;
}

function kubectlLogs(args) {
    const name = args.find(a => !a.startsWith('-'));
    if (!name) return 'error: expected pod name';
    const pod = clusterState.pods.find(p => p.name === name);
    if (!pod) return `Error from server (NotFound): pods "${name}" not found`;
    return `[${new Date().toISOString()}] Container started successfully\n[${new Date().toISOString()}] Ready to accept connections`;
}

function kubectlAuth(args) {
    if (args[0] === 'can-i') {
        return 'yes';
    }
    return 'error: unknown command';
}

function formatProbe(probe) {
    if (probe.httpGet) return `http-get http://:${probe.httpGet.port}${probe.httpGet.path} delay=${probe.initialDelaySeconds || 0}s period=${probe.periodSeconds || 10}s`;
    if (probe.tcpSocket) return `tcp-socket :${probe.tcpSocket.port} delay=${probe.initialDelaySeconds || 0}s period=${probe.periodSeconds || 10}s`;
    if (probe.exec) return `exec [${(probe.exec.command || []).join(' ')}] delay=${probe.initialDelaySeconds || 0}s period=${probe.periodSeconds || 10}s`;
    return 'unknown';
}

// ═══════════════════════════════════════════════════════════════
// TERRAFORM HANDLER
// ═══════════════════════════════════════════════════════════════
function handleTerraform(args, fullCmd) {
    if (args.length === 0) return 'Usage: terraform [command]\n\nCommands:\n  init, validate, plan, apply, destroy, state, output';

    switch (args[0]) {
        case 'init':
            return 'Initializing the backend...\n\nInitializing provider plugins...\n- Finding latest version of hashicorp/local...\n- Installing hashicorp/local v2.4.0...\n- Installed hashicorp/local v2.4.0\n\nTerraform has been successfully initialized!';

        case 'validate':
            // Check if any .tf files exist
            const tfFiles = Object.keys(files).filter(f => f.endsWith('.tf'));
            if (tfFiles.length === 0) return 'Error: No configuration files found in the current directory.';
            return 'Success! The configuration is valid.';

        case 'plan':
            return generateTerraformPlan();

        case 'apply':
            const plan = generateTerraformPlan();
            if (plan.includes('No changes')) return plan;
            return plan + '\n\nApply complete! Resources: ' +
                (plan.match(/will be created/g) || []).length + ' added, 0 changed, 0 destroyed.';

        case 'state':
            if (args[1] === 'list') {
                const resources = extractTerraformResources();
                return resources.length > 0 ? resources.join('\n') : 'No state found.';
            }
            return 'Usage: terraform state list';

        case 'output':
            return 'No outputs found.';

        case 'destroy':
            return 'Plan: 0 to add, 0 to change, 0 to destroy.\n\nDestroy complete! Resources: 0 destroyed.';

        default:
            return `error: unknown command "${args[0]}"`;
    }
}

function generateTerraformPlan() {
    const resources = extractTerraformResources();
    if (resources.length === 0) return 'No changes. Your infrastructure matches the configuration.';

    let plan = 'Terraform will perform the following actions:\n';
    resources.forEach(r => {
        plan += `\n  # ${r} will be created\n  + resource "${r.split('.')[0]}" "${r.split('.')[1] || 'this'}" {\n      + id = (known after apply)\n    }\n`;
    });
    plan += `\nPlan: ${resources.length} to add, 0 to change, 0 to destroy.`;
    return plan;
}

function extractTerraformResources() {
    const resources = [];
    Object.values(files).forEach(content => {
        const matches = content.matchAll(/resource\s+"([^"]+)"\s+"([^"]+)"/g);
        for (const match of matches) {
            resources.push(`${match[1]}.${match[2]}`);
        }
    });
    return resources;
}

// ═══════════════════════════════════════════════════════════════
// DOCKER HANDLER
// ═══════════════════════════════════════════════════════════════
function handleDocker(args, fullCmd) {
    if (args.length === 0) return 'Usage: docker [command]\n\nCommands:\n  build, run, ps, images, inspect';

    switch (args[0]) {
        case 'build':
            const dockerfile = files['Dockerfile'] || files['dockerfile'];
            if (!dockerfile) return 'error: unable to find Dockerfile';
            // Count steps
            const steps = (dockerfile.match(/^(FROM|RUN|COPY|ADD|CMD|ENTRYPOINT|EXPOSE|ENV|WORKDIR)/gm) || []).length;
            let buildOutput = '';
            for (let i = 1; i <= steps; i++) {
                buildOutput += `Step ${i}/${steps} : ...\n`;
            }
            buildOutput += `Successfully built abc123def456\nSuccessfully tagged ${args.find(a => a.startsWith('-t'))?.replace('-t', '').trim() || 'app'}:latest`;
            return buildOutput;

        case 'run':
            return `Container started: ${Math.random().toString(36).substring(2, 14)}`;

        case 'ps':
            return 'CONTAINER ID   IMAGE   COMMAND   CREATED   STATUS   PORTS   NAMES\n(no running containers)';

        case 'images':
            return 'REPOSITORY   TAG   IMAGE ID   CREATED   SIZE\n(no images)';

        default:
            return `error: unknown command "${args[0]}"`;
    }
}

// ═══════════════════════════════════════════════════════════════
// UTILITY: cat command
// ═══════════════════════════════════════════════════════════════
function handleCat(args) {
    const filename = args[0];
    if (!filename) return 'usage: cat <filename>';
    if (files[filename]) return files[filename];
    return `cat: ${filename}: No such file or directory`;
}

// ═══════════════════════════════════════════════════════════════
// YAML PARSER (basic)
// ═══════════════════════════════════════════════════════════════
function parseBasicYAML(text) {
    const result = {};
    const lines = text.split('\n');
    const stack = [{ obj: result, indent: -1 }];

    for (let line of lines) {
        if (line.trim() === '' || line.trim().startsWith('#')) continue;

        const indent = line.search(/\S/);
        const content = line.trim();

        // Pop stack to correct level
        while (stack.length > 1 && stack[stack.length - 1].indent >= indent) {
            stack.pop();
        }

        const parent = stack[stack.length - 1].obj;

        if (content.startsWith('- ')) {
            // Array item
            const item = content.substring(2);
            if (!Array.isArray(parent)) {
                // Find last key added
                const keys = Object.keys(typeof parent === 'object' ? parent : {});
                const lastKey = keys[keys.length - 1];
                if (lastKey && !Array.isArray(parent[lastKey])) {
                    parent[lastKey] = [];
                }
                if (lastKey) {
                    if (item.includes(':')) {
                        const obj = {};
                        const [k, ...v] = item.split(':');
                        obj[k.trim()] = v.join(':').trim();
                        parent[lastKey].push(obj);
                        stack.push({ obj: obj, indent: indent });
                    } else {
                        parent[lastKey].push(item);
                    }
                }
            }
        } else if (content.includes(':')) {
            const colonIdx = content.indexOf(':');
            const key = content.substring(0, colonIdx).trim();
            const value = content.substring(colonIdx + 1).trim();

            if (value === '' || value === '|' || value === '>') {
                parent[key] = {};
                stack.push({ obj: parent[key], indent: indent });
            } else {
                // Parse value
                if (value === 'true') parent[key] = true;
                else if (value === 'false') parent[key] = false;
                else if (!isNaN(value) && value !== '') parent[key] = Number(value);
                else parent[key] = value.replace(/^["']|["']$/g, '');
            }
        }
    }

    return result;
}

function checkNestedField(obj, path) {
    const parts = path.replace(/\[(\d+)\]/g, '.$1').split('.');
    let current = obj;
    for (const part of parts) {
        if (current === undefined || current === null) return false;
        if (Array.isArray(current)) {
            current = current[parseInt(part)] || current.find(item => item && item[part] !== undefined);
            if (current && current[part] !== undefined) current = current[part];
        } else {
            current = current[part];
        }
    }
    return current !== undefined && current !== null;
}

function matchCommand(input, pattern) {
    if (pattern.includes('*')) {
        const regex = new RegExp('^' + pattern.replace(/\*/g, '.*') + '$');
        return regex.test(input);
    }
    return input === pattern;
}

function resolveOutput(output, command) {
    // Replace placeholders
    return output.replace(/\{([^}]+)\}/g, (match, key) => {
        if (key === 'pod_name') {
            const parts = command.split(/\s+/);
            return parts[parts.length - 1] || 'unknown';
        }
        return match;
    });
}

// ═══════════════════════════════════════════════════════════════
// OBJECTIVES TRACKING
// ═══════════════════════════════════════════════════════════════
function checkObjectives() {
    const flows = CHALLENGE.commandFlows || {};
    const required = flows.required_commands || [];

    required.forEach((cmd, i) => {
        const cmdStr = typeof cmd === 'string' ? cmd : (cmd.command || '');
        const matched = commandsExecuted.some(exec => matchCommand(exec, cmdStr));

        const el = document.getElementById(`obj-${i}`);
        if (el && matched) {
            el.classList.add('done');
            el.textContent = '✓';
        }
    });
}

// ═══════════════════════════════════════════════════════════════
// HINTS
// ═══════════════════════════════════════════════════════════════
function revealHint(index) {
    if (index < ATTEMPT.hintsUsed) return; // Already revealed

    fetch(`/problems/${CHALLENGE.slug}/hint`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
    })
    .then(r => r.json())
    .then(data => {
        ATTEMPT.hintsUsed = data.hints_used;
        document.getElementById('hintsUsedCount').textContent = data.hints_used;

        // Reveal the hint
        const content = document.getElementById(`hintContent-${index}`);
        const btn = document.getElementById(`hintBtn-${index}`);
        if (content) content.classList.remove('hint-locked');
        if (btn) btn.remove();
    });
}

// ═══════════════════════════════════════════════════════════════
// SAVE / SUBMIT / SOLUTION
// ═══════════════════════════════════════════════════════════════
function saveProgress() {
    files[currentFile] = document.getElementById('codeEditor').value;

    fetch(`/problems/${CHALLENGE.slug}/save`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({
            files: files,
            commands: commandsExecuted,
            time_spent_seconds: Math.floor((Date.now() - startTime) / 1000),
        }),
    })
    .then(r => r.json())
    .then(() => {
        // Brief save confirmation
        const btn = document.querySelector('.editor-btn.save');
        btn.textContent = 'Saved ✓';
        setTimeout(() => btn.textContent = 'Save', 1500);
    });
}

function completeChallenge() {
    // Legacy path — only used for non-cluster problems
    files[currentFile] = document.getElementById('codeEditor').value;

    fetch(`/problems/${CHALLENGE.slug}/complete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({
            files: files,
            commands: commandsExecuted,
            time_spent_seconds: Math.floor((Date.now() - startTime) / 1000),
        }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.completed) {
            const btn = document.getElementById('submitBtn');
            btn.textContent = '✓ Completed';
            btn.disabled = true;
            btn.style.background = '#22c55e';
            btn.style.color = '#fff';
            showSolutionFromData(data.solution_files, data.solution_explanation);
        }
    });
}

// ═══════════════════════════════════════════════════════════════
// SUBMIT — Routes between real cluster validation and legacy
// ═══════════════════════════════════════════════════════════════
async function submitChallenge() {
    if (isSubmitting) return;

    if (CHALLENGE.requiresCluster) {
        // Real cluster validation
        await submitToRealCluster();
    } else {
        // Legacy simulated path
        completeChallenge();
    }
}

async function submitToRealCluster() {
    if (!envSessionReady) {
        appendToTerminal('', 'Error: Environment not started. Click "▶ Start Environment" first.');
        return;
    }

    isSubmitting = true;
    const btn = document.getElementById('submitBtn');
    btn.textContent = 'Validating...';
    btn.disabled = true;

    // Save current file state
    files[currentFile] = document.getElementById('codeEditor').value;

    try {
        const response = await fetch(`/api/problems/${CHALLENGE.slug}/submit`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({
                files: files,
                time_spent_seconds: Math.floor((Date.now() - startTime) / 1000),
            }),
        });
        const data = await response.json();

        showValidationResults(data);
    } catch (err) {
        alert('Error submitting: ' + err.message);
    } finally {
        isSubmitting = false;
        btn.disabled = false;
        btn.textContent = 'Submit';
    }
}

function showValidationResults(data) {
    const overlay = document.getElementById('validationOverlay');
    const title = document.getElementById('valTitle');
    const badge = document.getElementById('valBadge');
    const checks = document.getElementById('valChecks');
    const points = document.getElementById('valPoints');
    const retryBtn = document.getElementById('valRetryBtn');

    if (data.passed) {
        title.textContent = '🎉 Problem Solved!';
        badge.className = 'score-badge passed';
        badge.textContent = `${data.score}/${data.total} checks passed`;
        retryBtn.style.display = 'none';

        // Update submit button
        const btn = document.getElementById('submitBtn');
        btn.textContent = '✓ Solved';
        btn.disabled = true;
        btn.style.background = '#22c55e';
        btn.style.color = '#fff';

        // Show points
        if (data.points_earned) {
            points.textContent = `+${data.points_earned} points earned!`;
            points.style.display = 'block';
        }

        // Show solution
        if (data.solution_files || data.solution_explanation) {
            showSolutionFromData(data.solution_files, data.solution_explanation);
        }
    } else {
        title.textContent = 'Not Quite...';
        badge.className = 'score-badge failed';
        badge.textContent = `${data.score}/${data.total} checks passed`;
        retryBtn.style.display = 'inline-block';
        points.style.display = 'none';
    }

    // Populate check results
    checks.innerHTML = '';
    (data.results || []).forEach(r => {
        const li = document.createElement('li');
        li.innerHTML = `
            <span class="check-icon">${r.passed ? '✅' : '❌'}</span>
            <span>${escapeHtml(r.description || r.type)}</span>
        `;
        checks.appendChild(li);
    });

    overlay.classList.add('active');
}

function closeValidation() {
    document.getElementById('validationOverlay').classList.remove('active');
}

// ═══════════════════════════════════════════════════════════════
// START ENVIRONMENT (real cluster provisioning)
// ═══════════════════════════════════════════════════════════════
async function startEnvironment() {
    const btn = document.getElementById('startEnvBtn');
    const overlay = document.getElementById('envOverlay');
    const error = document.getElementById('envError');

    btn.disabled = true;
    btn.textContent = 'Starting...';
    error.style.display = 'none';

    // Show overlay
    overlay.classList.add('active');

    // Animate steps
    const steps = [
        document.getElementById('envStep1'),
        document.getElementById('envStep2'),
        document.getElementById('envStep3'),
    ];

    steps[0].className = 'active';
    steps[1].className = '';
    steps[2].className = '';

    try {
        // Step 1: Hit the start API (dispatches provisioning job)
        setTimeout(() => {
            steps[0].className = 'active';
        }, 100);

        const response = await fetch(`/api/problems/${CHALLENGE.slug}/start`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        });
        const data = await response.json();

        if (data.status === 'error') {
            throw new Error(data.message || 'Failed to start environment');
        }

        if (data.status === 'ready') {
            // Session already existed and is running (reused)
            steps[0].className = 'done';
        } else if (data.status === 'provisioning') {
            // Poll for status while provisioning runs async
            steps[0].className = 'active';
            let ready = false;
            let pollCount = 0;
            const maxPolls = 60; // 60 × 3s = 3 min max

            while (!ready && pollCount < maxPolls) {
                await new Promise(r => setTimeout(r, 3000));
                pollCount++;

                try {
                    const statusRes = await fetch(`/api/problems/${CHALLENGE.slug}/status`, {
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                    });
                    const statusData = await statusRes.json();

                    if (statusData.status === 'running') {
                        ready = true;
                        steps[0].className = 'done';
                    } else if (statusData.status === 'error') {
                        throw new Error(statusData.message || 'Provisioning failed');
                    }
                    // else still provisioning, continue polling
                } catch (pollErr) {
                    if (pollErr.message.includes('failed') || pollErr.message.includes('Failed')) {
                        throw pollErr;
                    }
                    // Network error, keep trying
                }
            }

            if (!ready) {
                throw new Error('Environment took too long to provision. Please try again.');
            }
        }

        // Step 2: Scenario loaded
        steps[0].className = 'done';
        steps[1].className = 'active';

        // Brief pause for visual effect
        await new Promise(r => setTimeout(r, 800));

        // Step 3: Ready
        steps[1].className = 'done';
        steps[2].className = 'done';

        document.getElementById('envOverlayTitle').textContent = '✅ Environment Ready!';
        document.getElementById('envSpinner').style.display = 'none';

        envSessionReady = true;

        // Update button
        btn.textContent = '✓ Running';
        btn.className = 'start-env-btn running';
        btn.disabled = true;

        // Print welcome message in terminal
        const termOut = document.getElementById('terminalOutput');
        termOut.innerHTML += `<span class="output" style="color:#22c55e;">✓ Environment ready. You can now run kubectl commands.</span>\n`;
        termOut.innerHTML += `<span class="output" style="color:var(--text-muted);">Try: kubectl get pods</span>\n`;

        // Close overlay after a moment
        await new Promise(r => setTimeout(r, 1200));
        overlay.classList.remove('active');

        // Reset spinner for next use
        document.getElementById('envSpinner').style.display = '';
        document.getElementById('envOverlayTitle').textContent = 'Starting Environment...';

        // Focus terminal
        document.getElementById('terminalInput').focus();

    } catch (err) {
        error.textContent = err.message;
        error.style.display = 'block';
        document.getElementById('envSpinner').style.display = 'none';
        document.getElementById('envOverlayTitle').textContent = '❌ Failed to Start';

        btn.disabled = false;
        btn.textContent = '▶ Retry';

        // Auto-close after 3s
        setTimeout(() => {
            overlay.classList.remove('active');
            document.getElementById('envSpinner').style.display = '';
            document.getElementById('envOverlayTitle').textContent = 'Starting Environment...';
        }, 4000);
    }
}

function showSolution() {
    const panel = document.getElementById('solutionPanel');
    const content = document.getElementById('solutionContent');

    if (panel.classList.contains('visible')) {
        panel.classList.remove('visible');
        return;
    }

    // Show solution files
    let html = '';
    for (const [filename, code] of Object.entries(CHALLENGE.solutionFiles || {})) {
        html += `<strong>${filename}:</strong>\n<pre>${escapeHtml(code)}</pre>`;
    }
    content.innerHTML = html;
    panel.classList.add('visible');
}

function showSolutionFromData(solutionFiles, explanation) {
    const panel = document.getElementById('solutionPanel');
    const content = document.getElementById('solutionContent');

    let html = '';
    for (const [filename, code] of Object.entries(solutionFiles || {})) {
        html += `<strong>${filename}:</strong>\n<pre>${escapeHtml(code)}</pre>`;
    }
    content.innerHTML = html;
    panel.classList.add('visible');
}

function resetEditor() {
    if (confirm('Reset all files to starter code?')) {
        files = { ...CHALLENGE.initialFiles };
        document.getElementById('codeEditor').value = files[currentFile] || '';
    }
}

// ═══════════════════════════════════════════════════════════════
// AUTO-SAVE (every 30 seconds)
// ═══════════════════════════════════════════════════════════════
setInterval(saveProgress, 30000);

// ═══════════════════════════════════════════════════════════════
// FOCUS TERMINAL ON CLICK
// ═══════════════════════════════════════════════════════════════
document.getElementById('terminalBody').addEventListener('click', function() {
    document.getElementById('terminalInput').focus();
});

// ═══════════════════════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════════════════════
// Run objective check on load (for resumed sessions)
checkObjectives();

// If cluster problem has an existing session, verify it's still alive
if (CHALLENGE.requiresCluster && ATTEMPT.labSessionId) {
    (async () => {
        try {
            const res = await fetch(`/api/problems/${CHALLENGE.slug}/status`, {
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
            });
            const data = await res.json();

            const btn = document.getElementById('startEnvBtn');
            if (data.status === 'running') {
                // Session is actually running
                if (btn) {
                    btn.textContent = '✓ Running';
                    btn.className = 'start-env-btn running';
                    btn.disabled = true;
                }
                envSessionReady = true;
                const termOut = document.getElementById('terminalOutput');
                termOut.innerHTML += `<span class="output" style="color:#22c55e;">✓ Reconnected to existing environment.</span>\n`;
                termOut.innerHTML += `<span class="output" style="color:var(--text-muted);">Try: kubectl get pods</span>\n`;
            } else {
                // Session is gone (destroyed/expired/not found) — reset button
                if (btn) {
                    btn.textContent = '▶ Start Environment';
                    btn.className = 'start-env-btn';
                    btn.disabled = false;
                }
                envSessionReady = false;
            }
        } catch (e) {
            // Can't check, reset to safe state
            envSessionReady = false;
        }
    })();
}

// If problem is already completed
if (ATTEMPT.status === 'completed') {
    const btn = document.getElementById('submitBtn');
    btn.textContent = '✓ Solved';
    btn.disabled = true;
    btn.style.background = '#22c55e';
    btn.style.color = '#fff';
}
</script>
@endpush
