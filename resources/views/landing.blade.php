<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="GovKloud - Enterprise Cloud & DevOps Training Platform. Hands-on Kubernetes labs with real clusters.">
    <title>GovKloud - Enterprise Cloud Training Platform</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --gk-navy: #0a0f1a;
            --gk-dark: #0f172a;
            --gk-slate: #1e293b;
            --gk-cyan: #D2B48C;
            --gk-teal: #C4A77D;
            --gk-gold: #fbbf24;
            --gk-purple: #8b5cf6;
            --gk-blue: #3b82f6;
            --gk-green: #22c55e;
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
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ===== Navigation ===== */
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(10, 15, 26, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(210, 180, 140, 0.1);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text);
            font-weight: 800;
            font-size: 1.5rem;
        }

        .nav-logo-icon {
            width: 44px;
            height: 44px;
            object-fit: contain;
            border-radius: 6px;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: var(--gk-cyan);
        }

        /* Dropdown Menu */
        .nav-dropdown {
            position: relative;
        }

        .nav-dropdown-trigger {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            cursor: pointer;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s ease;
            background: none;
            border: none;
            padding: 0;
        }

        .nav-dropdown-trigger:hover {
            color: var(--gk-cyan);
        }

        .nav-dropdown-trigger svg {
            width: 12px;
            height: 12px;
            transition: transform 0.2s ease;
        }

        .nav-dropdown:hover .nav-dropdown-trigger svg {
            transform: rotate(180deg);
        }

        .nav-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 0.75rem;
            min-width: 280px;
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .nav-dropdown:hover .nav-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            color: var(--text);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: rgba(210, 180, 140, 0.1);
            color: var(--gk-cyan);
        }

        .dropdown-item-info {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .dropdown-item-title {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .dropdown-item-desc {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .dropdown-item-count {
            background: rgba(210, 180, 140, 0.2);
            color: var(--gk-cyan);
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 0.5rem 0;
        }

        .dropdown-header {
            padding: 0.5rem 1rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
        }

        .nav-cta {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-ghost {
            background: transparent;
            color: var(--text);
        }

        .btn-ghost:hover {
            color: var(--gk-cyan);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            color: var(--gk-navy);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(210, 180, 140, 0.4);
        }

        .btn-large {
            padding: 1rem 2rem;
            font-size: 1rem;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            border-color: var(--gk-cyan);
            color: var(--gk-cyan);
        }

        /* ===== HERO ===== */
        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 7rem 2rem 0;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(ellipse at 50% 0%, rgba(6, 182, 212, 0.12) 0%, transparent 50%),
                radial-gradient(ellipse at 20% 30%, rgba(210, 180, 140, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 50%, rgba(139, 92, 246, 0.06) 0%, transparent 50%);
            pointer-events: none;
        }

        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            z-index: 2;
            width: 100%;
        }

        .hero-preview-wrapper {
            position: relative;
            width: 100%;
            max-width: 1100px;
            margin-top: 3rem;
        }

        .hero-preview-wrapper::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 120px;
            background: linear-gradient(to bottom, transparent 0%, var(--gk-dark) 100%);
            pointer-events: none;
            z-index: 3;
        }

        .hero-text {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(210, 180, 140, 0.1);
            border: 1px solid rgba(210, 180, 140, 0.3);
            border-radius: 50px;
            font-size: 0.85rem;
            color: var(--gk-cyan);
            margin-bottom: 2rem;
            width: fit-content;
        }

        .hero-badge-dot {
            width: 8px;
            height: 8px;
            background: var(--gk-green);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.3);
            }
        }

        .hero-text h1 {
            font-size: 3.8rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: #ffffff;
            -webkit-text-fill-color: unset;
        }

        .hero-text h1 span {
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            -webkit-background-clip: text;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: 2.5rem;
            max-width: 600px;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            margin-bottom: 2.5rem;
            justify-content: center;
        }

        .hero-social-proof {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .avatars {
            display: flex;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 3px solid var(--gk-navy);
            margin-left: -10px;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .avatar:first-child {
            margin-left: 0;
        }

        .social-proof-text {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .social-proof-text strong {
            color: var(--text);
        }

        /* ===== Terminal Window ===== */
        .terminal-window {
            background: #0d1117;
            border-radius: 14px;
            border: 1px solid rgba(210, 180, 140, 0.15);
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(210, 180, 140, 0.1),
                0 25px 80px rgba(0, 0, 0, 0.6),
                0 0 120px rgba(210, 180, 140, 0.06);
            animation: terminalFloat 6s ease-in-out infinite;
        }

        @keyframes terminalFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .terminal-titlebar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 18px;
            background: #161b22;
            border-bottom: 1px solid #30363d;
        }

        .terminal-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .terminal-dot.red {
            background: #ff5f56;
        }

        .terminal-dot.yellow {
            background: #ffbd2e;
        }

        .terminal-dot.green {
            background: #27c93f;
        }

        .terminal-title {
            flex: 1;
            text-align: center;
            font-size: 0.8rem;
            color: #8b949e;
            font-family: 'JetBrains Mono', monospace;
        }

        .terminal-body {
            padding: 20px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            line-height: 1.7;
            min-height: 380px;
            color: #c9d1d9;
        }

        .terminal-line {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 2px;
            opacity: 0;
            transform: translateY(6px);
            animation: lineAppear 0.3s forwards;
        }

        @keyframes lineAppear {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .terminal-prompt {
            color: var(--gk-green);
            margin-right: 6px;
            user-select: none;
        }

        .terminal-cmd {
            color: #e6edf3;
            font-weight: 500;
        }

        .terminal-output {
            color: #8b949e;
        }

        .terminal-highlight {
            color: #79c0ff;
        }

        .terminal-success {
            color: #56d364;
        }

        .terminal-warning {
            color: #e3b341;
        }

        .terminal-table-header {
            color: #d2a8ff;
            font-weight: 600;
        }

        .terminal-cursor {
            display: inline-block;
            width: 8px;
            height: 16px;
            background: var(--gk-cyan);
            animation: blink 1s step-end infinite;
            vertical-align: middle;
            margin-left: 2px;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }
        }

        /* ===== Trusted By ===== */
        .trusted-by {
            padding: 4rem 2rem;
            background: var(--gk-dark);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .trusted-by-inner {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .trusted-by h3 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
        }

        .logos-grid {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 3rem;
        }

        .logo-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            opacity: 0.7;
            transition: all 0.3s ease;
            filter: grayscale(1);
        }

        .logo-item:hover {
            opacity: 1;
            filter: grayscale(0);
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: var(--gk-slate);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            border: 1px solid var(--border);
        }

        .logo-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--text);
        }

        .logo-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* ===== Stats Counter Bar ===== */
        .stats-bar {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .stat-item {
            text-align: center;
            flex: 1;
            padding: 0 1rem;
            position: relative;
        }

        .stat-item:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 40px;
            width: 1px;
            background: var(--border);
        }

        .stat-number {
            font-size: 2.75rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .stat-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-muted);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .stats-bar {
                flex-wrap: wrap;
                gap: 2rem;
                justify-content: center;
            }

            .stat-item {
                flex: 0 0 45%;
            }

            .stat-item:not(:last-child)::after {
                display: none;
            }

            .stat-number {
                font-size: 2rem;
            }
        }

        /* ===== Technology Showcase ===== */
        .tech-showcase {
            padding: 5rem 0;
            overflow: hidden;
            -webkit-mask-image: linear-gradient(to right, transparent 0%, #000 10%, #000 90%, transparent 100%);
            mask-image: linear-gradient(to right, transparent 0%, #000 10%, #000 90%, transparent 100%);
        }

        .tech-showcase .section-header {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .tech-grid {
            display: flex;
            gap: 1.25rem;
            margin-top: 2.5rem;
            width: max-content;
            animation: tech-scroll 25s linear infinite;
        }

        .tech-grid:hover {
            animation-play-state: paused;
        }

        @keyframes tech-scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .tech-pill {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.75rem;
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 14px;
            transition: all 0.3s ease;
            cursor: default;
            text-decoration: none;
            color: var(--text);
        }

        .tech-pill:hover {
            border-color: var(--gk-cyan);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }

        .tech-pill-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .tech-pill-icon.docker {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(59, 130, 246, 0.1));
        }

        .tech-pill-icon.k8s {
            background: linear-gradient(135deg, rgba(50, 108, 229, 0.2), rgba(50, 108, 229, 0.1));
        }

        .tech-pill-icon.ansible {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.1));
        }

        .tech-pill-icon.aws {
            background: linear-gradient(135deg, rgba(255, 153, 0, 0.2), rgba(255, 153, 0, 0.1));
        }

        .tech-pill-icon.azure {
            background: linear-gradient(135deg, rgba(0, 120, 212, 0.2), rgba(0, 120, 212, 0.1));
        }

        .tech-pill-icon.security {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(34, 197, 94, 0.1));
        }

        .tech-pill-info {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        .tech-pill-name {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .tech-pill-desc {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* ===== Environment Preview (VS Code Mockup) ===== */
        .env-preview {
            padding: 6rem 2rem;
            background: linear-gradient(180deg, var(--gk-navy) 0%, var(--gk-dark) 50%, var(--gk-navy) 100%);
        }

        .env-preview-inner {
            max-width: 1100px;
            margin: 0 auto;
        }

        .vscode-window {
            background: #1e1e2e;
            border-radius: 14px;
            border: 1px solid rgba(210, 180, 140, 0.15);
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(210, 180, 140, 0.08),
                0 30px 80px rgba(0, 0, 0, 0.55),
                0 0 100px rgba(210, 180, 140, 0.04);
        }

        .vscode-titlebar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: #181825;
            border-bottom: 1px solid #313244;
        }

        .vscode-titlebar .terminal-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .vscode-titlebar .terminal-dot.red {
            background: #ff5f56;
        }

        .vscode-titlebar .terminal-dot.yellow {
            background: #ffbd2e;
        }

        .vscode-titlebar .terminal-dot.green {
            background: #27c93f;
        }

        .vscode-title-text {
            flex: 1;
            text-align: center;
            font-size: 0.78rem;
            color: #6c7086;
            font-family: 'Inter', sans-serif;
        }

        .vscode-layout {
            display: flex;
            height: 420px;
        }

        /* Sidebar */
        .vscode-sidebar {
            width: 220px;
            background: #181825;
            border-right: 1px solid #313244;
            display: flex;
            flex-direction: column;
        }

        .vscode-sidebar-header {
            padding: 10px 14px;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #6c7086;
            border-bottom: 1px solid #313244;
        }

        .vscode-file-tree {
            padding: 6px 0;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
        }

        .vscode-folder,
        .vscode-file {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 14px;
            cursor: default;
            color: #cdd6f4;
            transition: background 0.15s;
        }

        .vscode-folder:hover,
        .vscode-file:hover {
            background: rgba(210, 180, 140, 0.05);
        }

        .vscode-file.active {
            background: rgba(210, 180, 140, 0.1);
            color: var(--gk-cyan);
        }

        .vscode-folder {
            color: #89b4fa;
        }

        .vscode-file {
            padding-left: 28px;
        }

        .vscode-file .file-icon-yaml {
            color: #f9e2af;
        }

        .vscode-file .file-icon-md {
            color: #89b4fa;
        }

        .vscode-file .file-icon-sh {
            color: #a6e3a1;
        }

        /* Main editor */
        .vscode-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .vscode-tabs {
            display: flex;
            background: #181825;
            border-bottom: 1px solid #313244;
        }

        .vscode-tab {
            padding: 8px 16px;
            font-size: 0.78rem;
            font-family: 'JetBrains Mono', monospace;
            color: #6c7086;
            border-right: 1px solid #313244;
            cursor: default;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .vscode-tab.active {
            color: #cdd6f4;
            background: #1e1e2e;
            border-bottom: 2px solid var(--gk-cyan);
        }

        .vscode-editor {
            flex: 1;
            padding: 14px 0;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.78rem;
            line-height: 1.65;
            overflow: hidden;
            display: flex;
        }

        .editor-line-numbers {
            padding: 0 12px 0 16px;
            color: #45475a;
            text-align: right;
            user-select: none;
            min-width: 44px;
        }

        .editor-code {
            flex: 1;
            padding-right: 16px;
            color: #cdd6f4;
        }

        .editor-code .yaml-key {
            color: #89b4fa;
        }

        .editor-code .yaml-value {
            color: #a6e3a1;
        }

        .editor-code .yaml-string {
            color: #f9e2af;
        }

        .editor-code .yaml-comment {
            color: #6c7086;
            font-style: italic;
        }

        .editor-code .yaml-number {
            color: #fab387;
        }

        /* Integrated terminal */
        .vscode-integrated-terminal {
            height: 140px;
            background: #11111b;
            border-top: 1px solid #313244;
            padding: 10px 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            line-height: 1.6;
            color: #cdd6f4;
            overflow: hidden;
        }

        .vscode-term-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
            font-size: 0.7rem;
            color: #6c7086;
            border-bottom: 1px solid #313244;
            padding-bottom: 6px;
        }

        .vscode-term-tab {
            padding: 2px 8px;
            border-radius: 4px;
            cursor: default;
        }

        .vscode-term-tab.active {
            background: rgba(210, 180, 140, 0.1);
            color: var(--gk-cyan);
        }

        /* ===== Dynamic Benefits ===== */
        .benefits {
            padding: 6rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .section-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .benefit-card {
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2.5rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            transform: translateY(40px);
            position: relative;
            overflow: hidden;
        }

        .benefit-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--card-accent, linear-gradient(90deg, var(--gk-cyan), var(--gk-teal)));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .benefit-card:hover::before {
            opacity: 1;
        }

        .benefit-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .benefit-card:hover {
            border-color: var(--gk-cyan);
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .benefit-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
        }

        .benefit-icon.k8s {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
        }

        .benefit-icon.instant {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.2), rgba(249, 115, 22, 0.2));
        }

        .benefit-icon.fedramp {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(16, 185, 129, 0.2));
        }

        .benefit-icon.progress {
            background: linear-gradient(135deg, rgba(210, 180, 140, 0.2), rgba(196, 167, 125, 0.2));
        }

        .benefit-card h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .benefit-card p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .benefit-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 1rem;
            background: rgba(34, 197, 94, 0.1);
            color: var(--gk-green);
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        /* ===== Features Grid ===== */
        .features {
            padding: 6rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .feature-card {
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            border-color: var(--gk-cyan);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, rgba(210, 180, 140, 0.2), rgba(139, 92, 246, 0.2));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* ===== Mentorship Section ===== */
        .mentorship {
            padding: 6rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .mentorship-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .mentorship-text h2 {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.25rem;
        }

        .mentorship-text h2 .accent {
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .mentorship-text p {
            color: var(--text-muted);
            font-size: 1.05rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .mentorship-cards {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .mentorship-card {
            display: flex;
            align-items: flex-start;
            gap: 1.25rem;
            padding: 1.5rem;
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .mentorship-card:hover {
            border-color: var(--gk-cyan);
            transform: translateX(6px);
        }

        .mentorship-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .mentorship-card-icon.mentor {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(99, 102, 241, 0.1));
        }

        .mentorship-card-icon.resume {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(245, 158, 11, 0.1));
        }

        .mentorship-card-icon.placement {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(34, 197, 94, 0.1));
        }

        .mentorship-card h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .mentorship-card p {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin: 0;
        }

        @media (max-width: 768px) {
            .mentorship-inner {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }

            .mentorship-text h2 {
                font-size: 2rem;
            }
        }

        /* ===== CTA Section ===== */
        .cta-section {
            padding: 6rem 2rem;
            text-align: center;
        }

        .cta-box {
            max-width: 800px;
            margin: 0 auto;
            background: linear-gradient(135deg, var(--gk-slate), var(--gk-dark));
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 4rem;
            position: relative;
            overflow: hidden;
        }

        .cta-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--gk-cyan), var(--gk-purple), var(--gk-cyan));
        }

        .cta-box h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .cta-box p {
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        /* ===== Footer ===== */
        .footer {
            padding: 3rem 2rem;
            border-top: 1px solid var(--border);
            text-align: center;
        }

        .footer p {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1rem;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
        }

        .footer-links a:hover {
            color: var(--gk-cyan);
        }

        /* ===== Responsive ===== */
        @media (max-width: 1024px) {
            .hero-inner {
                gap: 2rem;
            }

            .hero-text h1 {
                font-size: 2.8rem;
            }

            .hero-subtitle {
                max-width: 100%;
            }

            .hero-cta {
                justify-content: center;
            }

            .hero-social-proof {
                justify-content: center;
            }

            .hero-badge {
                margin-left: auto;
                margin-right: auto;
            }

            .benefits-grid {
                grid-template-columns: 1fr;
            }

            .tech-pill {
                padding: 0.75rem 1.25rem;
            }
        }

        @media (max-width: 900px) {
            .features-grid {
                grid-template-columns: 1fr;
            }

            .vscode-sidebar {
                display: none;
            }

            .vscode-layout {
                height: 350px;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero-text h1 {
                font-size: 2.25rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .hero-cta {
                flex-direction: column;
                align-items: center;
            }

            .terminal-body {
                font-size: 0.72rem;
                min-height: 300px;
                padding: 14px;
            }

            .logos-grid {
                gap: 2rem;
            }

            .section-header h2 {
                font-size: 2rem;
            }

            .tech-grid {
                gap: 0.75rem;
            }

            .tech-pill-desc {
                display: none;
            }

            .vscode-layout {
                height: 300px;
            }

            .vscode-integrated-terminal {
                height: 100px;
            }
        }

        /* Mega Menu */
        .mega-menu {
            min-width: 560px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            padding: 0;
        }

        .mega-menu-column {
            padding: 0.75rem;
        }

        .mega-menu-column:first-child {
            border-right: 1px solid var(--border);
        }

        .mega-menu-header {
            padding: 0.5rem 1rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .mega-menu-footer {
            grid-column: 1 / -1;
            border-top: 1px solid var(--border);
            padding: 0.75rem 1rem;
            text-align: center;
        }

        .mega-menu-footer-link {
            color: var(--gk-cyan);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: opacity 0.2s ease;
        }

        .mega-menu-footer-link:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <x-navbar />

    <!-- ===== HERO ===== -->
    <section class="hero">
        <div class="hero-inner">
            <div class="hero-text">
                <h1>
                    Train. Certify. <span>Get Hired.</span>
                </h1>

                <p class="hero-subtitle">
                    Hands-on Cloud & DevOps training for GovTech careers.<br>
                    Real labs. Real skills. Real results.
                </p>

                <div class="hero-cta">
                    <a href="{{ route('courses.index') }}" class="btn btn-primary btn-large">
                        Start Learning Free
                    </a>
                </div>
            </div>

            <!-- VS Code Environment Preview -->
            <div class="hero-preview-wrapper">
                <div class="vscode-window">
                    <div class="vscode-titlebar">
                        <div class="terminal-dot red"></div>
                        <div class="terminal-dot yellow"></div>
                        <div class="terminal-dot green"></div>
                        <span class="vscode-title-text">deployment.yaml — k8s-lab — GovKloud Code Server</span>
                    </div>

                    <div class="vscode-layout">
                        <!-- Sidebar -->
                        <div class="vscode-sidebar">
                            <div class="vscode-sidebar-header">Explorer</div>
                            <div class="vscode-file-tree">
                                <div class="vscode-folder"><svg width="14" height="14" viewBox="0 0 24 24"
                                        fill="#e8a838" style="vertical-align: middle; margin-right: 4px;">
                                        <path
                                            d="M10 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-8l-2-2z" />
                                    </svg> k8s-lab</div>
                                <div class="vscode-file active"><svg width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" stroke="#a78bfa" stroke-width="1.5"
                                        style="vertical-align: middle; margin-right: 4px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg> deployment.yaml</div>
                                <div class="vscode-file"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="#a78bfa" stroke-width="1.5"
                                        style="vertical-align: middle; margin-right: 4px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg> service.yaml</div>
                                <div class="vscode-file"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="#a78bfa" stroke-width="1.5"
                                        style="vertical-align: middle; margin-right: 4px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg> ingress.yaml</div>
                                <div class="vscode-file"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="#22c55e" stroke-width="1.5"
                                        style="vertical-align: middle; margin-right: 4px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg> setup.sh</div>
                                <div class="vscode-folder"><svg width="14" height="14" viewBox="0 0 24 24"
                                        fill="#e8a838" style="vertical-align: middle; margin-right: 4px;">
                                        <path
                                            d="M10 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-8l-2-2z" />
                                    </svg> manifests</div>
                                <div class="vscode-file"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="#a78bfa" stroke-width="1.5"
                                        style="vertical-align: middle; margin-right: 4px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg> configmap.yaml</div>
                                <div class="vscode-file"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="#a78bfa" stroke-width="1.5"
                                        style="vertical-align: middle; margin-right: 4px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg> secrets.yaml</div>
                                <div class="vscode-folder"><svg width="14" height="14" viewBox="0 0 24 24"
                                        fill="#e8a838" style="vertical-align: middle; margin-right: 4px;">
                                        <path
                                            d="M10 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-8l-2-2z" />
                                    </svg> docs</div>
                                <div class="vscode-file"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="#3b82f6" stroke-width="1.5"
                                        style="vertical-align: middle; margin-right: 4px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg> README.md</div>
                                <div class="vscode-file"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="#3b82f6" stroke-width="1.5"
                                        style="vertical-align: middle; margin-right: 4px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg> WALKTHROUGH.md</div>
                            </div>
                        </div>

                        <!-- Main editor -->
                        <div class="vscode-main">
                            <div class="vscode-tabs">
                                <div class="vscode-tab active"><svg width="12" height="12" viewBox="0 0 24 24"
                                        fill="none" stroke="#a78bfa" stroke-width="1.5"
                                        style="vertical-align: middle; margin-right: 3px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg> deployment.yaml</div>
                                <div class="vscode-tab"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                        stroke="#a78bfa" stroke-width="1.5"
                                        style="vertical-align: middle; margin-right: 3px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg> service.yaml</div>
                            </div>

                            <div class="vscode-editor">
                                <div class="editor-line-numbers">
                                    1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9<br>10<br>11<br>12<br>13<br>14<br>15<br>16<br>17
                                </div>
                                <div class="editor-code">
                                    <span class="yaml-comment"># GovKloud Lab — Kubernetes Deployment</span><br>
                                    <span class="yaml-key">apiVersion:</span> <span
                                        class="yaml-value">apps/v1</span><br>
                                    <span class="yaml-key">kind:</span> <span class="yaml-value">Deployment</span><br>
                                    <span class="yaml-key">metadata:</span><br>
                                    &nbsp;&nbsp;<span class="yaml-key">name:</span> <span
                                        class="yaml-string">web-app</span><br>
                                    &nbsp;&nbsp;<span class="yaml-key">namespace:</span> <span
                                        class="yaml-string">production</span><br>
                                    <span class="yaml-key">spec:</span><br>
                                    &nbsp;&nbsp;<span class="yaml-key">replicas:</span> <span
                                        class="yaml-number">3</span><br>
                                    &nbsp;&nbsp;<span class="yaml-key">selector:</span><br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="yaml-key">matchLabels:</span><br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="yaml-key">app:</span> <span
                                        class="yaml-string">web-app</span><br>
                                    &nbsp;&nbsp;<span class="yaml-key">template:</span><br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="yaml-key">metadata:</span><br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="yaml-key">labels:</span><br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="yaml-key">app:</span>
                                    <span class="yaml-string">web-app</span><br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="yaml-key">spec:</span><br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="yaml-key">containers:</span><br>
                                </div>
                            </div>

                            <!-- Integrated terminal -->
                            <div class="vscode-integrated-terminal">
                                <div class="vscode-term-header">
                                    <span class="vscode-term-tab active">TERMINAL</span>
                                    <span class="vscode-term-tab">PROBLEMS</span>
                                    <span class="vscode-term-tab">OUTPUT</span>
                                </div>
                                <div id="vscode-term-content">
                                    <span style="color: #a6e3a1;">$</span> kubectl apply -f deployment.yaml<br>
                                    <span style="color: #a6e3a1;">deployment.apps/web-app created</span><br>
                                    <span style="color: #a6e3a1;">$</span> kubectl get pods -w<br>
                                    <span
                                        style="color: #cdd6f4;">web-app-6b8f9d4c77-kx9n2&nbsp;&nbsp;1/1&nbsp;&nbsp;Running&nbsp;&nbsp;0&nbsp;&nbsp;12s</span><br>
                                    <span style="color: #a6e3a1;">$</span> <span class="terminal-cursor"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Trusted By ===== -->
    <section class="trusted-by">
        <div class="trusted-by-inner">
            <h3>Learn the Tech Stack Trusted by Government & Industry Leaders</h3>
            <div class="logos-grid">
                <div class="logo-item">
                    <div class="logo-icon"><svg width="40" height="40" viewBox="0 0 48 48" fill="none">
                            <circle cx="24" cy="24" r="20" stroke="#049fd9" stroke-width="2" fill="none" />
                            <path
                                d="M14 20h4v-4h-4v4zm5 0h4v-4h-4v4zm5 0h4v-4h-4v4zm-10 5h4v-4h-4v4zm5 0h4v-4h-4v4zm5 0h4v-4h-4v4zm-10 5h4v-4h-4v4zm5 0h4v-4h-4v4zm10-10h4v-4h-4v4z"
                                fill="#049fd9" />
                        </svg></div>
                    <span class="logo-name">Cisco</span>
                    <span class="logo-role">Network Infrastructure</span>
                </div>
                <div class="logo-item">
                    <div class="logo-icon"><svg width="40" height="40" viewBox="0 0 48 48" fill="none">
                            <polygon points="24,6 42,42 6,42" stroke="#fff" stroke-width="2" fill="none" />
                            <line x1="24" y1="14" x2="24" y2="30" stroke="#fff" stroke-width="2" />
                            <polygon points="18,34 30,34 24,26" fill="#fff" opacity="0.5" />
                        </svg></div>
                    <span class="logo-name">Lockheed Martin</span>
                    <span class="logo-role">Defense & Aerospace</span>
                </div>
                <div class="logo-item">
                    <div class="logo-icon"><svg width="40" height="40" viewBox="0 0 48 48" fill="none">
                            <polygon points="24,4 29,18 44,18 32,27 36,42 24,33 12,42 16,27 4,18 19,18" fill="none"
                                stroke="#c9a84c" stroke-width="2" />
                        </svg></div>
                    <span class="logo-name">US Army</span>
                    <span class="logo-role">Military Operations</span>
                </div>
                <div class="logo-item">
                    <div class="logo-icon"><svg width="40" height="40" viewBox="0 0 48 48" fill="none">
                            <rect x="10" y="20" width="28" height="20" rx="2" stroke="#8b949e" stroke-width="2"
                                fill="none" />
                            <path d="M14 20V14a10 10 0 0120 0v6" stroke="#8b949e" stroke-width="2" fill="none" />
                            <line x1="18" y1="24" x2="18" y2="36" stroke="#8b949e" stroke-width="1.5" />
                            <line x1="24" y1="24" x2="24" y2="36" stroke="#8b949e" stroke-width="1.5" />
                            <line x1="30" y1="24" x2="30" y2="36" stroke="#8b949e" stroke-width="1.5" />
                        </svg></div>
                    <span class="logo-name">Federal Agencies</span>
                    <span class="logo-role">Government Cloud</span>
                </div>
                <div class="logo-item">
                    <div class="logo-icon"><svg width="40" height="40" viewBox="0 0 48 48" fill="none">
                            <path d="M24 6L6 42h36L24 6z" stroke="#22c55e" stroke-width="2" fill="none" />
                            <path d="M24 18v12" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" />
                            <circle cx="24" cy="35" r="1.5" fill="#22c55e" />
                        </svg></div>
                    <span class="logo-name">FedRAMP</span>
                    <span class="logo-role">Compliance Ready</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Stats Counter Bar ===== -->
    <section class="stats-bar">
        <div class="stat-item">
            <div class="stat-number">250+</div>
            <div class="stat-label">Hands-On Labs</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">1,200+</div>
            <div class="stat-label">Lessons</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">50+</div>
            <div class="stat-label">Government Agencies</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">12</div>
            <div class="stat-label">Compliance Frameworks</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">10K+</div>
            <div class="stat-label">Certified Engineers</div>
        </div>
    </section>

    <!-- ===== Technology Showcase ===== -->
    <section class="tech-showcase">
        <div class="section-header">
            <h2>Master the Technologies That Matter</h2>
            <p>Hands-on training across the most in-demand cloud and DevOps technologies.</p>
        </div>

        <div class="tech-grid">
            <div class="tech-pill">
                <div class="tech-pill-icon docker"><svg width="28" height="28" viewBox="0 0 24 24" fill="#2496ED">
                        <path
                            d="M13.983 11.078h2.119a.186.186 0 00.186-.185V9.006a.186.186 0 00-.186-.186h-2.119a.185.185 0 00-.185.185v1.888c0 .102.083.185.185.185m-2.954-5.43h2.118a.186.186 0 00.186-.186V3.574a.186.186 0 00-.186-.185h-2.118a.185.185 0 00-.185.185v1.888c0 .102.082.185.185.185m0 2.716h2.118a.187.187 0 00.186-.186V6.29a.186.186 0 00-.186-.185h-2.118a.185.185 0 00-.185.185v1.887c0 .102.082.185.185.186m-2.93 0h2.12a.186.186 0 00.184-.186V6.29a.185.185 0 00-.185-.185H8.1a.185.185 0 00-.185.185v1.887c0 .102.083.185.185.186m-2.964 0h2.119a.186.186 0 00.185-.186V6.29a.185.185 0 00-.185-.185H5.136a.186.186 0 00-.186.185v1.887c0 .102.084.185.186.186m5.893 2.715h2.118a.186.186 0 00.186-.185V9.006a.186.186 0 00-.186-.186h-2.118a.185.185 0 00-.185.185v1.888c0 .102.082.185.185.185m-2.93 0h2.12a.185.185 0 00.184-.185V9.006a.185.185 0 00-.184-.186h-2.12a.185.185 0 00-.184.185v1.888c0 .102.083.185.185.185m-2.964 0h2.119a.185.185 0 00.185-.185V9.006a.185.185 0 00-.184-.186h-2.12a.186.186 0 00-.186.186v1.887c0 .102.084.185.186.185m-2.92 0h2.12a.185.185 0 00.184-.185V9.006a.185.185 0 00-.184-.186h-2.12a.185.185 0 00-.184.185v1.888c0 .102.082.185.185.185M23.763 9.89c-.065-.051-.672-.51-1.954-.51-.338.001-.676.03-1.01.087-.248-1.7-1.653-2.53-1.716-2.566l-.344-.199-.226.327c-.284.438-.49.922-.612 1.43-.23.97-.09 1.882.403 2.661-.595.332-1.55.413-1.744.42H.751a.751.751 0 00-.75.748 11.376 11.376 0 00.692 4.062c.545 1.428 1.355 2.48 2.41 3.124 1.18.723 3.1 1.137 5.275 1.137.983.003 1.963-.086 2.93-.266a12.248 12.248 0 003.823-1.389c.98-.567 1.86-1.288 2.61-2.136 1.252-1.418 1.998-2.997 2.553-4.4h.221c1.372 0 2.215-.549 2.68-1.009.309-.293.55-.65.707-1.046l.098-.288Z" />
                    </svg></div>
                <div class="tech-pill-info">
                    <span class="tech-pill-name">Docker</span>
                    <span class="tech-pill-desc">Containerization</span>
                </div>
            </div>
            <div class="tech-pill">
                <div class="tech-pill-icon k8s"><svg width="28" height="28" viewBox="0 0 24 24" fill="#326CE5">
                        <path
                            d="M10.204 14.35l.007.01-.999 2.413a5.171 5.171 0 0 1-2.075-2.597l2.578-.437.004.005a.44.44 0 0 1 .484.606zm-.833-2.129a.44.44 0 0 0 .173-.756l.002-.011L7.585 9.7a5.143 5.143 0 0 0-.73 3.255l2.514-.725.002-.009zm1.145-1.98a.44.44 0 0 0 .699-.337l.01-.005.15-2.62a5.144 5.144 0 0 0-3.01 1.442l2.147 1.523.004-.002zm.76 2.75l.723.349.722-.347.18-.78-.5-.623h-.804l-.5.623.179.779zm1.5-3.095a.44.44 0 0 0 .7.336l.008.003 2.134-1.513a5.188 5.188 0 0 0-2.992-1.442l.148 2.615.002.001zm10.876 5.97l-5.773 7.181a1.6 1.6 0 0 1-1.248.594l-9.261.003a1.6 1.6 0 0 1-1.247-.596l-5.776-7.18a1.583 1.583 0 0 1-.307-1.34L2.1 5.573c.108-.47.425-.864.863-1.073L11.305.513a1.606 1.606 0 0 1 1.385 0l8.345 3.985c.438.209.755.604.863 1.073l2.062 8.955c.108.47-.005.963-.308 1.34zm-3.289-2.057c-.042-.01-.103-.026-.145-.034-.174-.033-.315-.025-.479-.038-.35-.037-.638-.067-.895-.148-.105-.04-.18-.165-.216-.216l-.201-.059a6.45 6.45 0 0 0-.105-2.332 6.465 6.465 0 0 0-.936-2.163c.052-.047.15-.133.177-.159.008-.09.001-.183.094-.282.197-.185.444-.338.743-.522.142-.084.273-.137.415-.242.032-.024.076-.062.11-.089.24-.191.295-.52.123-.736-.172-.216-.506-.236-.745-.045-.034.027-.08.062-.111.088-.134.116-.217.23-.33.35-.246.25-.45.458-.673.609-.097.056-.239.037-.303.033l-.19.135a6.545 6.545 0 0 0-4.146-2.003l-.012-.223c-.065-.062-.143-.115-.163-.25-.022-.268.015-.557.057-.905.023-.163.061-.298.068-.475.001-.04-.001-.099-.001-.142 0-.306-.224-.555-.5-.555-.275 0-.499.249-.499.555l.001.014c0 .041-.002.092 0 .128.006.177.044.312.067.475.042.348.078.637.056.906a.545.545 0 0 1-.162.258l-.012.211a6.424 6.424 0 0 0-4.166 2.003 8.373 8.373 0 0 1-.18-.128c-.09.012-.18.04-.297-.029-.223-.15-.427-.358-.673-.608-.113-.12-.195-.234-.329-.349-.03-.026-.077-.062-.111-.088a.594.594 0 0 0-.348-.132.481.481 0 0 0-.398.176c-.172.216-.117.546.123.737l.007.005.104.083c.142.105.272.159.414.242.299.185.546.338.743.522.076.082.09.226.1.288l.16.143a6.462 6.462 0 0 0-1.02 4.506l-.208.06c-.055.072-.133.184-.215.217-.257.081-.546.11-.895.147-.164.014-.305.006-.48.039-.037.007-.09.02-.133.03l-.004.002-.007.002c-.295.071-.484.342-.423.608.061.267.349.429.645.365l.007-.001.01-.003.129-.029c.17-.046.294-.113.448-.172.33-.118.604-.217.87-.256.112-.009.23.069.288.101l.217-.037a6.5 6.5 0 0 0 2.88 3.596l-.09.218c.033.084.069.199.044.282-.097.252-.263.517-.452.813-.091.136-.185.242-.268.399-.02.037-.045.095-.064.134-.128.275-.034.591.213.71.248.12.556-.007.69-.282v-.002c.02-.039.046-.09.062-.127.07-.162.094-.301.144-.458.132-.332.205-.68.387-.897.05-.06.13-.082.215-.105l.113-.205a6.453 6.453 0 0 0 4.609.012l.106.192c.086.028.18.042.256.155.136.232.229.507.342.84.05.156.074.295.145.457.016.037.043.09.062.129.133.276.442.402.69.282.247-.118.341-.435.213-.71-.02-.039-.045-.096-.065-.134-.083-.156-.177-.261-.268-.398-.19-.296-.346-.541-.443-.793-.04-.13.007-.21.038-.294-.018-.022-.059-.144-.083-.202a6.499 6.499 0 0 0 2.88-3.622c.064.01.176.03.213.038.075-.05.144-.114.28-.104.266.039.54.138.87.256.154.06.277.128.448.173.036.01.088.019.13.028l.009.003.007.001c.297.064.584-.098.645-.365.06-.266-.128-.537-.423-.608zM16.4 9.701l-1.95 1.746v.005a.44.44 0 0 0 .173.757l.003.01 2.526.728a5.199 5.199 0 0 0-.108-1.674A5.208 5.208 0 0 0 16.4 9.7zm-4.013 5.325a.437.437 0 0 0-.404-.232.44.44 0 0 0-.372.233h-.002l-1.268 2.292a5.164 5.164 0 0 0 3.326.003l-1.27-2.296h-.01zm1.888-1.293a.44.44 0 0 0-.27.036.44.44 0 0 0-.214.572l-.003.004 1.01 2.438a5.15 5.15 0 0 0 2.081-2.615l-2.6-.44-.004.005z" />
                    </svg></div>
                <div class="tech-pill-info">
                    <span class="tech-pill-name">Kubernetes</span>
                    <span class="tech-pill-desc">Orchestration</span>
                </div>
            </div>
            <div class="tech-pill">
                <div class="tech-pill-icon ansible"><svg width="28" height="28" viewBox="0 0 24 24" fill="#EE0000">
                        <path
                            d="M10.617 11.473l4.686 3.695-3.102-7.662zM12 0C5.371 0 0 5.371 0 12s5.371 12 12 12 12-5.371 12-12S18.629 0 12 0zm5.797 17.305c-.011.471-.403.842-.875.83-.236 0-.416-.09-.664-.293l-6.19-5-2.079 5.203H6.191L11.438 5.44c.124-.314.427-.52.764-.506.326-.014.63.189.742.506l4.774 11.494c.045.111.08.234.08.348-.001.009-.001.009-.001.023z" />
                    </svg></div>
                <div class="tech-pill-info">
                    <span class="tech-pill-name">Ansible</span>
                    <span class="tech-pill-desc">Automation</span>
                </div>
            </div>
            <div class="tech-pill">
                <div class="tech-pill-icon aws"><svg width="28" height="28" viewBox="0 0 24 24" fill="#FF9900">
                        <path
                            d="M6.763 10.036c0 .296.032.535.088.71.064.176.144.368.256.576.04.063.056.127.056.183 0 .08-.048.16-.152.24l-.503.335a.383.383 0 0 1-.208.072c-.08 0-.16-.04-.239-.112a2.47 2.47 0 0 1-.287-.375 6.18 6.18 0 0 1-.248-.471c-.622.734-1.405 1.101-2.347 1.101-.67 0-1.205-.191-1.596-.574-.391-.384-.59-.894-.59-1.533 0-.678.239-1.23.726-1.644.487-.415 1.133-.623 1.955-.623.272 0 .551.024.846.064.296.04.6.104.918.176v-.583c0-.607-.127-1.03-.375-1.277-.255-.248-.686-.367-1.3-.367-.28 0-.568.032-.863.104-.296.072-.583.16-.863.272a2.287 2.287 0 0 1-.28.104.488.488 0 0 1-.127.024c-.112 0-.168-.08-.168-.247v-.391c0-.128.016-.224.056-.28a.597.597 0 0 1 .224-.167c.279-.144.614-.264 1.005-.36a4.84 4.84 0 0 1 1.246-.151c.95 0 1.644.216 2.091.647.44.43.662 1.085.662 1.963v2.586zm-3.24 1.214c.263 0 .534-.048.822-.144.287-.096.543-.271.758-.51.128-.152.224-.32.272-.512.047-.191.08-.423.08-.694v-.335a6.66 6.66 0 0 0-.735-.136 6.02 6.02 0 0 0-.75-.048c-.535 0-.926.104-1.19.32-.263.215-.39.518-.39.917 0 .375.095.655.295.846.191.2.47.296.838.296zm6.41.862c-.144 0-.24-.024-.304-.08-.064-.048-.12-.16-.168-.311L7.586 5.55a1.398 1.398 0 0 1-.072-.335c0-.128.064-.2.191-.2h.783c.151 0 .255.025.31.08.065.048.113.16.16.312l1.342 5.284 1.245-5.284c.04-.16.088-.264.151-.312a.549.549 0 0 1 .32-.08h.638c.152 0 .256.025.32.08.063.048.12.16.151.312l1.261 5.348 1.381-5.348c.048-.16.104-.264.16-.312a.52.52 0 0 1 .311-.08h.743c.128 0 .2.065.2.2 0 .04-.009.08-.017.128a1.137 1.137 0 0 1-.056.215l-1.923 6.17c-.048.16-.104.263-.168.311a.51.51 0 0 1-.303.08h-.687c-.151 0-.255-.024-.32-.08-.063-.056-.119-.16-.15-.32l-1.238-5.148-1.23 5.14c-.04.16-.087.264-.15.32-.065.056-.177.08-.32.08zm10.256.215c-.415 0-.83-.048-1.229-.143-.399-.096-.71-.2-.918-.32-.128-.071-.216-.151-.248-.223a.563.563 0 0 1-.048-.224v-.407c0-.167.064-.247.183-.247.048 0 .096.008.144.024.048.016.12.048.2.08.271.12.566.215.878.279.319.064.63.096.95.096.502 0 .894-.088 1.165-.264a.86.86 0 0 0 .415-.758.777.777 0 0 0-.215-.559c-.144-.151-.415-.287-.806-.415l-1.157-.36c-.583-.183-1.013-.455-1.277-.815a1.902 1.902 0 0 1-.4-1.19c0-.343.072-.647.216-.903.144-.263.335-.487.575-.663.24-.183.503-.32.806-.407a3.37 3.37 0 0 1 1.005-.143c.183 0 .375.008.559.032.191.024.367.056.535.096.16.04.312.08.455.127.144.048.256.096.336.144a.69.69 0 0 1 .24.2.43.43 0 0 1 .071.263v.375c0 .168-.064.256-.184.256a.83.83 0 0 1-.303-.096 3.652 3.652 0 0 0-1.532-.311c-.455 0-.815.071-1.062.223-.248.152-.375.383-.375.71 0 .224.08.416.24.567.16.152.454.304.87.44l1.134.358c.574.184.99.44 1.237.767.248.328.375.703.375 1.118 0 .36-.072.686-.207.966-.144.279-.335.52-.583.718-.248.2-.543.343-.886.44-.36.104-.735.152-1.142.152z" />
                        <path
                            d="M21.725 16.166C19.13 18.098 15.302 19.1 12.01 19.1c-4.598 0-8.74-1.7-11.87-4.526-.247-.224-.024-.527.272-.352C3.66 16.182 7.637 17.4 11.762 17.4c2.875 0 6.037-.598 8.947-1.83.44-.191.806.287.016.596z" />
                        <path
                            d="M22.673 15.072c-.336-.43-2.22-.207-3.074-.104-.255.032-.295-.192-.063-.36 1.5-1.053 3.967-.75 4.254-.398.287.36-.08 2.826-1.485 4.007-.216.176-.423.08-.327-.152.32-.79 1.03-2.57.695-2.993z" />
                    </svg></div>
                <div class="tech-pill-info">
                    <span class="tech-pill-name">AWS</span>
                    <span class="tech-pill-desc">Cloud Platform</span>
                </div>
            </div>
            <div class="tech-pill">
                <div class="tech-pill-icon azure"><svg width="28" height="28" viewBox="0 0 96 96" fill="none">
                        <defs>
                            <linearGradient id="az1" x1="58.97" y1="9.92" x2="38.78" y2="87.08"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#114A8B" />
                                <stop offset="1" stop-color="#0669BC" />
                            </linearGradient>
                            <linearGradient id="az2" x1="60.23" y1="49.86" x2="54.17" y2="52.46"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-opacity=".3" />
                                <stop offset=".07" stop-opacity=".2" />
                                <stop offset=".32" stop-opacity=".1" />
                                <stop offset=".62" stop-opacity=".05" />
                                <stop offset="1" stop-opacity="0" />
                            </linearGradient>
                            <linearGradient id="az3" x1="46.1" y1="7.63" x2="64.56" y2="86.8"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#3CCBF4" />
                                <stop offset="1" stop-color="#2892DF" />
                            </linearGradient>
                        </defs>
                        <path
                            d="M33.34 6.54h26.04L32.47 88.04a5.09 5.09 0 0 1-4.82 3.42H9.75a5.1 5.1 0 0 1-4.81-6.76L30.51 9.96a5.09 5.09 0 0 1 4.83-3.42z"
                            fill="url(#az1)" />
                        <path
                            d="M64.88 62.17H29.56a2.33 2.33 0 0 0-1.59 4.03l22.76 21.22a5.15 5.15 0 0 0 3.51 1.38h22.4L64.88 62.17z"
                            fill="url(#az2)" />
                        <path
                            d="M33.34 6.54a5.05 5.05 0 0 0-4.84 3.52L3.01 84.61a5.1 5.1 0 0 0 4.81 6.85h18.28a5.23 5.23 0 0 0 4.42-3.52l5.5-16.1 19.31 17.96a5.2 5.2 0 0 0 3.29 1.66h22.07l-9.73-26.66-30.37.01L59.58 6.54z"
                            fill="url(#az3)" />
                    </svg></div>
                <div class="tech-pill-info">
                    <span class="tech-pill-name">Azure</span>
                    <span class="tech-pill-desc">Cloud Platform</span>
                </div>
            </div>
            <div class="tech-pill">
                <div class="tech-pill-icon security"><svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2L4 6v5c0 5.5 3.4 10.6 8 12 4.6-1.4 8-6.5 8-12V6l-8-4z" stroke="#22c55e"
                            stroke-width="1.5" fill="none" />
                        <path d="M9 12l2 2 4-4" stroke="#22c55e" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg></div>
                <div class="tech-pill-info">
                    <span class="tech-pill-name">GovKloud Security</span>
                    <span class="tech-pill-desc">FedRAMP & Compliance</span>
                </div>
            </div>

            <!-- Duplicated set for seamless infinite scroll -->
            <div class="tech-pill">
                <div class="tech-pill-icon docker"><svg width="28" height="28" viewBox="0 0 24 24" fill="#2496ED">
                        <path
                            d="M13.983 11.078h2.119a.186.186 0 00.186-.185V9.006a.186.186 0 00-.186-.186h-2.119a.185.185 0 00-.185.185v1.888c0 .102.083.185.185.185m-2.954-5.43h2.118a.186.186 0 00.186-.186V3.574a.186.186 0 00-.186-.185h-2.118a.185.185 0 00-.185.185v1.888c0 .102.082.185.185.185m0 2.716h2.118a.187.187 0 00.186-.186V6.29a.186.186 0 00-.186-.185h-2.118a.185.185 0 00-.185.185v1.887c0 .102.082.185.185.186m-2.93 0h2.12a.186.186 0 00.184-.186V6.29a.185.185 0 00-.185-.185H8.1a.185.185 0 00-.185.185v1.887c0 .102.083.185.185.186m-2.964 0h2.119a.186.186 0 00.185-.186V6.29a.185.185 0 00-.185-.185H5.136a.186.186 0 00-.186.185v1.887c0 .102.084.185.186.186m5.893 2.715h2.118a.186.186 0 00.186-.185V9.006a.186.186 0 00-.186-.186h-2.118a.185.185 0 00-.185.185v1.888c0 .102.082.185.185.185m-2.93 0h2.12a.185.185 0 00.184-.185V9.006a.185.185 0 00-.184-.186h-2.12a.185.185 0 00-.184.185v1.888c0 .102.083.185.185.185m-2.964 0h2.119a.185.185 0 00.185-.185V9.006a.185.185 0 00-.184-.186h-2.12a.186.186 0 00-.186.186v1.887c0 .102.084.185.186.185m-2.92 0h2.12a.185.185 0 00.184-.185V9.006a.185.185 0 00-.184-.186h-2.12a.185.185 0 00-.184.185v1.888c0 .102.082.185.185.185M23.763 9.89c-.065-.051-.672-.51-1.954-.51-.338.001-.676.03-1.01.087-.248-1.7-1.653-2.53-1.716-2.566l-.344-.199-.226.327c-.284.438-.49.922-.612 1.43-.23.97-.09 1.882.403 2.661-.595.332-1.55.413-1.744.42H.751a.751.751 0 00-.75.748 11.376 11.376 0 00.692 4.062c.545 1.428 1.355 2.48 2.41 3.124 1.18.723 3.1 1.137 5.275 1.137.983.003 1.963-.086 2.93-.266a12.248 12.248 0 003.823-1.389c.98-.567 1.86-1.288 2.61-2.136 1.252-1.418 1.998-2.997 2.553-4.4h.221c1.372 0 2.215-.549 2.68-1.009.309-.293.55-.65.707-1.046l.098-.288Z" />
                    </svg></div>
                <div class="tech-pill-info">
                    <span class="tech-pill-name">Docker</span>
                    <span class="tech-pill-desc">Containerization</span>
                </div>
            </div>
            <div class="tech-pill">
                <div class="tech-pill-icon k8s"><svg width="28" height="28" viewBox="0 0 24 24" fill="#326CE5">
                        <path
                            d="M10.204 14.35l.007.01-.999 2.413a5.171 5.171 0 0 1-2.075-2.597l2.578-.437.004.005a.44.44 0 0 1 .484.606zm-.833-2.129a.44.44 0 0 0 .173-.756l.002-.011L7.585 9.7a5.143 5.143 0 0 0-.73 3.255l2.514-.725.002-.009zm1.145-1.98a.44.44 0 0 0 .699-.337l.01-.005.15-2.62a5.144 5.144 0 0 0-3.01 1.442l2.147 1.523.004-.002zm.76 2.75l.723.349.722-.347.18-.78-.5-.623h-.804l-.5.623.179.779zm1.5-3.095a.44.44 0 0 0 .7.336l.008.003 2.134-1.513a5.188 5.188 0 0 0-2.992-1.442l.148 2.615.002.001z" />
                    </svg></div>
                <div class="tech-pill-info">
                    <span class="tech-pill-name">Kubernetes</span>
                    <span class="tech-pill-desc">Orchestration</span>
                </div>
            </div>
            <div class="tech-pill">
                <div class="tech-pill-icon ansible"><svg width="28" height="28" viewBox="0 0 24 24" fill="#EE0000">
                        <path
                            d="M10.617 11.473l4.686 3.695-3.102-7.662zM12 0C5.371 0 0 5.371 0 12s5.371 12 12 12 12-5.371 12-12S18.629 0 12 0zm5.797 17.305c-.011.471-.403.842-.875.83-.236 0-.416-.09-.664-.293l-6.19-5-2.079 5.203H6.191L11.438 5.44c.124-.314.427-.52.764-.506.326-.014.63.189.742.506l4.774 11.494c.045.111.08.234.08.348-.001.009-.001.009-.001.023z" />
                    </svg></div>
                <div class="tech-pill-info">
                    <span class="tech-pill-name">Ansible</span>
                    <span class="tech-pill-desc">Automation</span>
                </div>
            </div>
            <div class="tech-pill">
                <div class="tech-pill-icon aws"><svg width="28" height="28" viewBox="0 0 24 24" fill="#FF9900">
                        <path
                            d="M6.763 10.036c0 .296.032.535.088.71.064.176.144.368.256.576.04.063.056.127.056.183 0 .08-.048.16-.152.24l-.503.335a.383.383 0 0 1-.208.072c-.08 0-.16-.04-.239-.112a2.47 2.47 0 0 1-.287-.375 6.18 6.18 0 0 1-.248-.471c-.622.734-1.405 1.101-2.347 1.101-.67 0-1.205-.191-1.596-.574-.391-.384-.59-.894-.59-1.533 0-.678.239-1.23.726-1.644.487-.415 1.133-.623 1.955-.623.272 0 .551.024.846.064.296.04.6.104.918.176v-.583c0-.607-.127-1.03-.375-1.277-.255-.248-.686-.367-1.3-.367-.28 0-.568.032-.863.104-.296.072-.583.16-.863.272a2.287 2.287 0 0 1-.28.104.488.488 0 0 1-.127.024c-.112 0-.168-.08-.168-.247v-.391c0-.128.016-.224.056-.28a.597.597 0 0 1 .224-.167c.279-.144.614-.264 1.005-.36a4.84 4.84 0 0 1 1.246-.151c.95 0 1.644.216 2.091.647.44.43.662 1.085.662 1.963v2.586zm-3.24 1.214c.263 0 .534-.048.822-.144.287-.096.543-.271.758-.51.128-.152.224-.32.272-.512.047-.191.08-.423.08-.694v-.335a6.66 6.66 0 0 0-.735-.136 6.02 6.02 0 0 0-.75-.048c-.535 0-.926.104-1.19.32-.263.215-.39.518-.39.917 0 .375.095.655.295.846.191.2.47.296.838.296zm6.41.862c-.144 0-.24-.024-.304-.08-.064-.048-.12-.16-.168-.311L7.586 5.55a1.398 1.398 0 0 1-.072-.335c0-.128.064-.2.191-.2h.783c.151 0 .255.025.31.08.065.048.113.16.16.312l1.342 5.284 1.245-5.284c.04-.16.088-.264.151-.312a.549.549 0 0 1 .32-.08h.638c.152 0 .256.025.32.08.063.048.12.16.151.312l1.261 5.348 1.381-5.348c.048-.16.104-.264.16-.312a.52.52 0 0 1 .311-.08h.743c.128 0 .2.065.2.2 0 .04-.009.08-.017.128a1.137 1.137 0 0 1-.056.215l-1.923 6.17c-.048.16-.104.263-.168.311a.51.51 0 0 1-.303.08h-.687c-.151 0-.255-.024-.32-.08-.063-.056-.119-.16-.15-.32l-1.238-5.148-1.23 5.14c-.04.16-.087.264-.15.32-.065.056-.177.08-.32.08zm10.256.215c-.415 0-.83-.048-1.229-.143-.399-.096-.71-.2-.918-.32-.128-.071-.216-.151-.248-.223a.563.563 0 0 1-.048-.224v-.407c0-.167.064-.247.183-.247.048 0 .096.008.144.024.048.016.12.048.2.08.271.12.566.215.878.279.319.064.63.096.95.096.502 0 .894-.088 1.165-.264a.86.86 0 0 0 .415-.758.777.777 0 0 0-.215-.559c-.144-.151-.415-.287-.806-.415l-1.157-.36c-.583-.183-1.013-.455-1.277-.815a1.902 1.902 0 0 1-.4-1.19c0-.343.072-.647.216-.903.144-.263.335-.487.575-.663.24-.183.503-.32.806-.407a3.37 3.37 0 0 1 1.005-.143c.183 0 .375.008.559.032.191.024.367.056.535.096.16.04.312.08.455.127.144.048.256.096.336.144a.69.69 0 0 1 .24.2.43.43 0 0 1 .071.263v.375c0 .168-.064.256-.184.256a.83.83 0 0 1-.303-.096 3.652 3.652 0 0 0-1.532-.311c-.455 0-.815.071-1.062.223-.248.152-.375.383-.375.71 0 .224.08.416.24.567.16.152.454.304.87.44l1.134.358c.574.184.99.44 1.237.767.248.328.375.703.375 1.118 0 .36-.072.686-.207.966-.144.279-.335.52-.583.718-.248.2-.543.343-.886.44-.36.104-.735.152-1.142.152z" />
                        <path
                            d="M21.725 16.166C19.13 18.098 15.302 19.1 12.01 19.1c-4.598 0-8.74-1.7-11.87-4.526-.247-.224-.024-.527.272-.352C3.66 16.182 7.637 17.4 11.762 17.4c2.875 0 6.037-.598 8.947-1.83.44-.191.806.287.016.596z" />
                        <path
                            d="M22.673 15.072c-.336-.43-2.22-.207-3.074-.104-.255.032-.295-.192-.063-.36 1.5-1.053 3.967-.75 4.254-.398.287.36-.08 2.826-1.485 4.007-.216.176-.423.08-.327-.152.32-.79 1.03-2.57.695-2.993z" />
                    </svg></div>
                <div class="tech-pill-info">
                    <span class="tech-pill-name">AWS</span>
                    <span class="tech-pill-desc">Cloud Platform</span>
                </div>
            </div>
            <div class="tech-pill">
                <div class="tech-pill-icon azure"><svg width="28" height="28" viewBox="0 0 96 96" fill="none">
                        <defs>
                            <linearGradient id="az1b" x1="58.97" y1="9.92" x2="38.78" y2="87.08"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#114A8B" />
                                <stop offset="1" stop-color="#0669BC" />
                            </linearGradient>
                            <linearGradient id="az2b" x1="60.23" y1="49.86" x2="54.17" y2="52.46"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-opacity=".3" />
                                <stop offset=".07" stop-opacity=".2" />
                                <stop offset=".32" stop-opacity=".1" />
                                <stop offset=".62" stop-opacity=".05" />
                                <stop offset="1" stop-opacity="0" />
                            </linearGradient>
                            <linearGradient id="az3b" x1="46.1" y1="7.63" x2="64.56" y2="86.8"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#3CCBF4" />
                                <stop offset="1" stop-color="#2892DF" />
                            </linearGradient>
                        </defs>
                        <path
                            d="M33.34 6.54h26.04L32.47 88.04a5.09 5.09 0 0 1-4.82 3.42H9.75a5.1 5.1 0 0 1-4.81-6.76L30.51 9.96a5.09 5.09 0 0 1 4.83-3.42z"
                            fill="url(#az1b)" />
                        <path
                            d="M64.88 62.17H29.56a2.33 2.33 0 0 0-1.59 4.03l22.76 21.22a5.15 5.15 0 0 0 3.51 1.38h22.4L64.88 62.17z"
                            fill="url(#az2b)" />
                        <path
                            d="M33.34 6.54a5.05 5.05 0 0 0-4.84 3.52L3.01 84.61a5.1 5.1 0 0 0 4.81 6.85h18.28a5.23 5.23 0 0 0 4.42-3.52l5.5-16.1 19.31 17.96a5.2 5.2 0 0 0 3.29 1.66h22.07l-9.73-26.66-30.37.01L59.58 6.54z"
                            fill="url(#az3b)" />
                    </svg></div>
                <div class="tech-pill-info">
                    <span class="tech-pill-name">Azure</span>
                    <span class="tech-pill-desc">Cloud Platform</span>
                </div>
            </div>
            <div class="tech-pill">
                <div class="tech-pill-icon security"><svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2L4 6v5c0 5.5 3.4 10.6 8 12 4.6-1.4 8-6.5 8-12V6l-8-4z" stroke="#22c55e"
                            stroke-width="1.5" fill="none" />
                        <path d="M9 12l2 2 4-4" stroke="#22c55e" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg></div>
                <div class="tech-pill-info">
                    <span class="tech-pill-name">GovKloud Security</span>
                    <span class="tech-pill-desc">FedRAMP & Compliance</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Dynamic Benefits ===== -->
    <section class="benefits" id="benefits">
        <div class="section-header">
            <h2>Why Engineers Choose GovKloud</h2>
            <p>Built for professionals who need to master cloud infrastructure in secure, isolated environments.</p>
        </div>

        <div class="benefits-grid">
            <div class="benefit-card" data-delay="0">
                <div class="benefit-icon k8s"><svg width="24" height="24" viewBox="0 0 24 24" fill="#326CE5">
                        <path
                            d="M10.204 14.35l.007.01-.999 2.413a5.171 5.171 0 0 1-2.075-2.597l2.578-.437.004.005a.44.44 0 0 1 .484.606zm-.833-2.129a.44.44 0 0 0 .173-.756l.002-.011L7.585 9.7a5.143 5.143 0 0 0-.73 3.255l2.514-.725.002-.009zm1.145-1.98a.44.44 0 0 0 .699-.337l.01-.005.15-2.62a5.144 5.144 0 0 0-3.01 1.442l2.147 1.523.004-.002zm.76 2.75l.723.349.722-.347.18-.78-.5-.623h-.804l-.5.623.179.779zm1.5-3.095a.44.44 0 0 0 .7.336l.008.003 2.134-1.513a5.188 5.188 0 0 0-2.992-1.442l.148 2.615.002.001z" />
                    </svg></div>
                <h3>Real Kubernetes Clusters</h3>
                <p>Every lab spins up a real virtual cluster — not a simulator. You get actual <strong>kubectl</strong>
                    access to deploy pods, services, and ingress controllers in your own isolated namespace.</p>
                <div class="benefit-tag">✓ Production-grade environment</div>
            </div>
            <div class="benefit-card" data-delay="150">
                <div class="benefit-icon instant"><svg width="24" height="24" viewBox="0 0 24 24" fill="#f59e0b">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
                    </svg></div>
                <h3>Instant Lab Access</h3>
                <p>No local setup. No Docker installs. Click <strong>Start Lab</strong> and you're in a fully configured
                    VS Code environment with terminal access — ready in under 60 seconds.</p>
                <div class="benefit-tag">✓ Zero configuration needed</div>
            </div>
            <div class="benefit-card" data-delay="300">
                <div class="benefit-icon fedramp"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="#22c55e" stroke-width="1.5">
                        <path d="M12 2L4 6v5c0 5.5 3.4 10.6 8 12 4.6-1.4 8-6.5 8-12V6l-8-4z" />
                        <path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg></div>
                <h3>Built for Government</h3>
                <p>Training content aligned with <strong>FedRAMP</strong>, <strong>NIST 800-53</strong>, and
                    <strong>Zero Trust</strong> frameworks. Learn cloud security practices that meet federal compliance
                    standards.
                </p>
                <div class="benefit-tag">✓ FedRAMP-aligned content</div>
            </div>
            <div class="benefit-card" data-delay="450">
                <div class="benefit-icon progress"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="#a78bfa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg></div>
                <h3>Structured Learning Paths</h3>
                <p>From Kubernetes basics to Terraform IaC, follow curated paths with <strong>video lessons</strong>,
                    <strong>reading material</strong>, <strong>quizzes</strong>, and <strong>hands-on labs</strong>
                    designed by industry veterans.
                </p>
                <div class="benefit-tag">✓ Video + Reading + Labs</div>
            </div>
        </div>
    </section>


    <!-- ===== Features ===== -->
    <section class="features" id="features">
        <div class="section-header">
            <h2>Everything You Need to Learn DevOps</h2>
            <p>Comprehensive tools and content for mastering modern cloud infrastructure.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#06b6d4"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="5 3 19 12 5 21 5 3" />
                    </svg></div>
                <h3>Video Lessons</h3>
                <p>Watch expert-led video content covering Kubernetes, Docker, Helm, CI/CD, Terraform, and cloud
                    security fundamentals.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#a78bfa"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg></div>
                <h3>Rich Reading Content</h3>
                <p>In-depth markdown articles with code examples, architecture diagrams, and YAML snippets you can copy
                    directly into your labs.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg></div>
                <h3>Interactive Quizzes</h3>
                <p>Test your knowledge with multiple-choice questions after each lesson. Get instant feedback and
                    explanations.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                        <line x1="8" y1="21" x2="16" y2="21" />
                        <line x1="12" y1="17" x2="12" y2="21" />
                    </svg></div>
                <h3>Browser-Based IDE</h3>
                <p>Full VS Code editor running in your browser. Write YAML, run kubectl, and deploy workloads without
                    leaving the platform.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg></div>
                <h3>Isolated Sandboxes</h3>
                <p>Each session runs in a secure, isolated Kubernetes namespace. Practice freely without fear of
                    breaking anything.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ef4444"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <circle cx="12" cy="12" r="6" />
                        <circle cx="12" cy="12" r="2" />
                    </svg></div>
                <h3>Hands-On Challenges</h3>
                <p>Learn by doing. Each module includes practical challenges that test your skills in real-world
                    scenarios.</p>
            </div>
        </div>
    </section>

    <!-- ===== Mentorship & Career Placement ===== -->
    <section class="mentorship">
        <div class="mentorship-inner">
            <div class="mentorship-text">
                <h2>We Don't Just Train You.<br><span class="accent">We Get You Hired.</span></h2>
                <p>GovKloud goes beyond labs and lessons. Our career services team works directly with you — from resume
                    reviews to interview prep — so you land the GovTech role you've been working toward.</p>
                <a href="{{ route('register') }}" class="btn btn-primary">Join the Program</a>
            </div>
            <div class="mentorship-cards">
                <div class="mentorship-card">
                    <div class="mentorship-card-icon mentor">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="#6366f1" stroke-width="2"
                                stroke-linecap="round" />
                            <circle cx="9" cy="7" r="4" stroke="#6366f1" stroke-width="2" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="#6366f1" stroke-width="2"
                                stroke-linecap="round" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="#6366f1" stroke-width="2"
                                stroke-linecap="round" />
                        </svg>
                    </div>
                    <div>
                        <h4>1:1 Career Mentorship</h4>
                        <p>Get paired with a GovTech professional who guides your learning path, answers questions, and
                            helps you navigate the clearance process.</p>
                    </div>
                </div>
                <div class="mentorship-card">
                    <div class="mentorship-card-icon resume">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="#f59e0b"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <polyline points="14 2 14 8 20 8" stroke="#f59e0b" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <line x1="8" y1="13" x2="16" y2="13" stroke="#f59e0b" stroke-width="2"
                                stroke-linecap="round" />
                            <line x1="8" y1="17" x2="13" y2="17" stroke="#f59e0b" stroke-width="2"
                                stroke-linecap="round" />
                        </svg>
                    </div>
                    <div>
                        <h4>Resume & Portfolio Review</h4>
                        <p>Our team reviews your resume, LinkedIn profile, and project portfolio to make sure you stand
                            out to federal contractors and agencies.</p>
                    </div>
                </div>
                <div class="mentorship-card">
                    <div class="mentorship-card-icon placement">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="2" y="7" width="20" height="14" rx="2" stroke="#22c55e" stroke-width="2" />
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" stroke="#22c55e" stroke-width="2"
                                stroke-linecap="round" />
                            <path d="M12 12v4" stroke="#22c55e" stroke-width="2" stroke-linecap="round" />
                            <path d="M2 12h20" stroke="#22c55e" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <div>
                        <h4>Job Placement Assistance</h4>
                        <p>We connect you with hiring partners across government and defense sectors. Our network
                            includes DOD, VA, DHS, and top federal contractors.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="cta-section">
        <div class="cta-box">
            <h2>Ready to Launch Your Cloud Career?</h2>
            <p>Join thousands of engineers building job-ready DevOps skills. Start free and get instant access to
                hands-on Kubernetes labs, real cluster environments, and structured learning paths that get you hired.
            </p>
            <a href="{{ route('register') }}" class="btn btn-primary btn-large">
                Get Started Free
            </a>
        </div>
    </section>

    <!-- ===== Footer ===== -->
    <footer class="footer">
        <p>© {{ date('Y') }} GovKloud. Enterprise Cloud Training Platform.</p>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Contact</a>
        </div>
    </footer>

    <script>
        // ===== ANIMATED TERMINAL =====
        (function () {
            const terminal = document.getElementById('terminal');
            if (!terminal) return;

            const scenes = [
                {
                    lines: [
                        { type: 'cmd', text: 'kubectl get pods -n production' },
                        { type: 'blank' },
                        { type: 'header', text: 'NAME                              READY   STATUS    RESTARTS   AGE' },
                        {
                            type: 'output', parts: [
                                { text: 'nginx-7c5ddbdf54-p8z2j           ', cls: 'terminal-highlight' },
                                { text: '1/1     ', cls: 'terminal-success' },
                                { text: 'Running   0          2m14s', cls: 'terminal-output' }
                            ]
                        },
                        {
                            type: 'output', parts: [
                                { text: 'redis-master-0                    ', cls: 'terminal-highlight' },
                                { text: '1/1     ', cls: 'terminal-success' },
                                { text: 'Running   0          5m32s', cls: 'terminal-output' }
                            ]
                        },
                        {
                            type: 'output', parts: [
                                { text: 'api-gateway-6b8f9d4c77-kx9n2     ', cls: 'terminal-highlight' },
                                { text: '1/1     ', cls: 'terminal-success' },
                                { text: 'Running   0          8m01s', cls: 'terminal-output' }
                            ]
                        },
                    ]
                },
                {
                    lines: [
                        { type: 'cmd', text: 'kubectl apply -f deployment.yaml' },
                        { type: 'success', text: 'deployment.apps/web-app created' },
                        { type: 'success', text: 'service/web-app-svc created' },
                        { type: 'success', text: 'ingress.networking.k8s.io/web-app-ingress created' },
                    ]
                },
                {
                    lines: [
                        { type: 'cmd', text: 'kubectl get svc -n production' },
                        { type: 'blank' },
                        { type: 'header', text: 'NAME            TYPE           CLUSTER-IP      EXTERNAL-IP' },
                        {
                            type: 'output', parts: [
                                { text: 'web-app-svc     ', cls: 'terminal-highlight' },
                                { text: 'LoadBalancer   10.0.142.18     ', cls: 'terminal-output' },
                                { text: '52.168.1.42', cls: 'terminal-success' }
                            ]
                        },
                        {
                            type: 'output', parts: [
                                { text: 'redis-svc       ', cls: 'terminal-highlight' },
                                { text: 'ClusterIP      10.0.87.203     ', cls: 'terminal-output' },
                                { text: '<none>', cls: 'terminal-warning' }
                            ]
                        },
                    ]
                },
                {
                    lines: [
                        { type: 'cmd', text: 'kubectl rollout status deployment/web-app' },
                        { type: 'output-plain', text: 'Waiting for deployment "web-app" rollout to finish...' },
                        { type: 'output-plain', text: '3 of 3 updated replicas are available...' },
                        { type: 'success', text: 'deployment "web-app" successfully rolled out' },
                    ]
                },
                {
                    lines: [
                        { type: 'cmd', text: 'helm upgrade --install monitoring prometheus-stack' },
                        { type: 'output-plain', text: 'Release "monitoring" has been upgraded.' },
                        { type: 'header', text: 'NAME: monitoring' },
                        { type: 'output-plain', text: 'NAMESPACE: observability' },
                        { type: 'output-plain', text: 'STATUS: deployed' },
                        { type: 'success', text: 'REVISION: 3' },
                    ]
                },
            ];

            let currentScene = 0;

            function renderLine(lineData) {
                const div = document.createElement('div');
                div.className = 'terminal-line';

                if (lineData.type === 'blank') {
                    div.innerHTML = '&nbsp;';
                    return div;
                }

                if (lineData.type === 'cmd') {
                    div.innerHTML = `<span class="terminal-prompt">$</span><span class="terminal-cmd">${lineData.text}</span>`;
                    return div;
                }

                if (lineData.type === 'header') {
                    div.innerHTML = `<span class="terminal-table-header">${lineData.text}</span>`;
                    return div;
                }

                if (lineData.type === 'success') {
                    div.innerHTML = `<span class="terminal-success">${lineData.text}</span>`;
                    return div;
                }

                if (lineData.type === 'output-plain') {
                    div.innerHTML = `<span class="terminal-output">${lineData.text}</span>`;
                    return div;
                }

                if (lineData.type === 'output' && lineData.parts) {
                    div.innerHTML = lineData.parts.map(p =>
                        `<span class="${p.cls}">${p.text}</span>`
                    ).join('');
                    return div;
                }

                return div;
            }

            async function typeCommand(element, text) {
                const cmdSpan = element.querySelector('.terminal-cmd');
                if (!cmdSpan) return;
                cmdSpan.textContent = '';
                const cursor = document.createElement('span');
                cursor.className = 'terminal-cursor';
                cmdSpan.after(cursor);

                for (let i = 0; i < text.length; i++) {
                    cmdSpan.textContent += text[i];
                    await sleep(30 + Math.random() * 40);
                }
                cursor.remove();
            }

            function sleep(ms) {
                return new Promise(r => setTimeout(r, ms));
            }

            async function playScene(scene) {
                terminal.innerHTML = '';

                for (let i = 0; i < scene.lines.length; i++) {
                    const lineData = scene.lines[i];
                    const el = renderLine(lineData);
                    terminal.appendChild(el);

                    if (lineData.type === 'cmd') {
                        // Show prompt immediately, then type the command
                        el.style.opacity = '1';
                        el.style.transform = 'none';
                        await typeCommand(el, lineData.text);
                        await sleep(400);
                    } else if (lineData.type === 'blank') {
                        el.style.animationDelay = '0s';
                        await sleep(50);
                    } else {
                        el.style.animationDelay = '0s';
                        await sleep(120);
                    }
                }

                // Add blinking cursor at end
                const cursorLine = document.createElement('div');
                cursorLine.className = 'terminal-line';
                cursorLine.style.opacity = '1';
                cursorLine.style.transform = 'none';
                cursorLine.innerHTML = '<span class="terminal-prompt">$</span><span class="terminal-cursor"></span>';
                terminal.appendChild(cursorLine);
            }

            async function loop() {
                while (true) {
                    await playScene(scenes[currentScene]);
                    await sleep(3000);
                    currentScene = (currentScene + 1) % scenes.length;
                }
            }

            // Start after a short delay
            setTimeout(loop, 600);
        })();

        // ===== SCROLL-TRIGGERED BENEFIT CARDS =====
        (function () {
            const cards = document.querySelectorAll('.benefit-card');
            if (!cards.length) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const delay = parseInt(entry.target.dataset.delay) || 0;
                        setTimeout(() => {
                            entry.target.classList.add('visible');
                        }, delay);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.15,
                rootMargin: '0px 0px -50px 0px'
            });

            cards.forEach(card => observer.observe(card));
        })();
    </script>
</body>

</html>