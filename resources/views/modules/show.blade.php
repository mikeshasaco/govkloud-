@extends('layouts.govkloud')

@section('title', $module->title . ' - GovKloud Labs')

@push('styles')
<style>
    /* ========================================
       GOVKLOUD UNIQUE DESIGN SYSTEM
       Professional • Modern • Distinctive
       ======================================== */
    
    :root {
        --gk-navy: #0f172a;
        --gk-slate: #1e293b;
        --gk-cyan: #D2B48C;
        --gk-teal: #C4A77D;
        --gk-gold: #fbbf24;
        --gk-purple: #8b5cf6;
        --gk-glow: rgba(210, 180, 140, 0.4);
    }

    /* Page Layout - Full Width */
    .module-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem 0;
    }

    /* Info Cards Row - Below Hero */
    .info-cards-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin: 1.5rem 0;
    }

    @media (max-width: 900px) {
        .info-cards-row {
            grid-template-columns: 1fr;
        }
    }

    /* ========================================
       HERO SECTION - Unique Mesh Gradient
       ======================================== */
    .module-hero {
        position: relative;
        border-radius: 20px;
        padding: 2.5rem;
        overflow: hidden;
        background: var(--gk-slate);
        border: 1px solid rgba(210, 180, 140, 0.2);
    }

    .module-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: 
            radial-gradient(ellipse at 20% 20%, rgba(210, 180, 140, 0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 80%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
            radial-gradient(ellipse at 50% 50%, rgba(196, 167, 125, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    .module-hero::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 500px;
        height: 500px;
        background: conic-gradient(from 180deg, transparent, rgba(210, 180, 140, 0.1), transparent 60%);
        animation: rotate 20s linear infinite;
        pointer-events: none;
    }

    @keyframes rotate {
        to { transform: rotate(360deg); }
    }

    .hero-content {
        position: relative;
        z-index: 2;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 2rem;
        align-items: center;
    }

    @media (max-width: 768px) {
        .hero-content {
            grid-template-columns: 1fr;
        }
    }

    /* Category Pills */
    .category-pills {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }

    .pill {
        background: rgba(210, 180, 140, 0.12);
        border: 1px solid rgba(210, 180, 140, 0.3);
        color: var(--gk-cyan);
        padding: 0.35rem 0.9rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .module-hero h1 {
        font-size: 2.25rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ffffff 0%, #94a3b8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .level-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .level-badge {
        background: linear-gradient(135deg, var(--gk-teal), #059669);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .level-dots {
        display: flex;
        gap: 4px;
    }

    .level-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
    }

    .level-dot.active {
        background: var(--gk-cyan);
        box-shadow: 0 0 8px var(--gk-glow);
    }

    .module-description {
        color: #94a3b8;
        font-size: 1rem;
        line-height: 1.7;
        margin-bottom: 1.5rem;
        max-width: 480px;
    }

    .hero-stats {
        display: flex;
        gap: 2rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--gk-cyan);
    }

    .stat-label {
        font-size: 0.7rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Hero Visual */
    .hero-visual {
        width: 280px;
        height: 200px;
        position: relative;
    }

    .hero-visual-orb {
        position: absolute;
        width: 180px;
        height: 180px;
        background: linear-gradient(135deg, rgba(210, 180, 140, 0.3), rgba(139, 92, 246, 0.2));
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        box-shadow: 
            0 0 60px rgba(210, 180, 140, 0.3),
            inset 0 0 60px rgba(210, 180, 140, 0.1);
        animation: pulse 4s ease-in-out infinite;
    }

    .hero-visual-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 4rem;
        filter: drop-shadow(0 0 20px var(--gk-glow));
    }

    @keyframes pulse {
        0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
        50% { transform: translate(-50%, -50%) scale(1.05); opacity: 0.8; }
    }

    @media (max-width: 768px) {
        .hero-visual { display: none; }
    }

    /* ========================================
       SIDEBAR - Glassmorphism Cards
       ======================================== */
    .module-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .glass-card {
        background: rgba(30, 41, 59, 0.8);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(210, 180, 140, 0.15);
        border-radius: 16px;
        padding: 1.75rem;
        position: relative;
        overflow: hidden;
    }

    .glass-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(210, 180, 140, 0.5), transparent);
    }

    .glass-card h3 {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
        margin-bottom: 1.25rem;
        font-weight: 600;
    }

    /* Progress Section */
    .progress-ring-container {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .progress-ring {
        width: 80px;
        height: 80px;
        position: relative;
    }

    .progress-ring svg {
        transform: rotate(-90deg);
    }

    .progress-ring-bg {
        fill: none;
        stroke: rgba(210, 180, 140, 0.15);
        stroke-width: 6;
    }

    .progress-ring-fill {
        fill: none;
        stroke: url(#progressGradient);
        stroke-width: 6;
        stroke-linecap: round;
        stroke-dasharray: 226;
        stroke-dashoffset: 226;
        transition: stroke-dashoffset 0.5s ease;
    }

    .progress-ring-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--gk-cyan);
    }

    .progress-info h4 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .progress-info p {
        font-size: 0.8rem;
        color: #64748b;
    }

    .btn-start {
        display: block;
        width: 100%;
        background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
        color: #0f172a;
        border: none;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(210, 180, 140, 0.3);
    }

    .btn-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(210, 180, 140, 0.4);
    }

    .btn-secondary {
        display: block;
        width: 100%;
        background: transparent;
        color: #94a3b8;
        border: 1px solid rgba(148, 163, 184, 0.3);
        padding: 0.875rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        margin-top: 0.75rem;
        transition: all 0.2s ease;
        text-align: center;
    }

    .btn-secondary:hover {
        border-color: var(--gk-cyan);
        color: var(--gk-cyan);
    }

    /* Course Includes - Unique Grid */
    .includes-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .include-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem;
        background: rgba(210, 180, 140, 0.05);
        border: 1px solid rgba(210, 180, 140, 0.1);
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .include-item:hover {
        border-color: rgba(210, 180, 140, 0.3);
        background: rgba(210, 180, 140, 0.1);
    }

    .include-icon {
        font-size: 1.1rem;
    }

    .include-text {
        font-size: 0.8rem;
        color: #cbd5e1;
    }

    /* ========================================
       CURRICULUM - Unique Timeline Design
       ======================================== */
    .curriculum-section {
        margin-top: 0.5rem;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .section-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, var(--gk-cyan), var(--gk-purple));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: 0 4px 20px rgba(210, 180, 140, 0.3);
    }

    .section-header h2 {
        font-size: 1.4rem;
        font-weight: 700;
    }

    .section-header span {
        font-size: 0.85rem;
        color: #64748b;
    }

    /* Timeline */
    .curriculum-timeline {
        position: relative;
        padding-left: 2rem;
    }

    .curriculum-timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, var(--gk-cyan), var(--gk-purple), rgba(139, 92, 246, 0.2));
        border-radius: 2px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 0.75rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -2rem;
        top: 50%;
        transform: translateY(-50%);
        width: 22px;
        height: 22px;
        background: var(--gk-slate);
        border: 2px solid var(--gk-cyan);
        border-radius: 50%;
        z-index: 1;
        transition: all 0.2s ease;
    }

    .timeline-item:hover::before {
        background: var(--gk-cyan);
        box-shadow: 0 0 12px var(--gk-glow);
    }

    .timeline-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        background: rgba(30, 41, 59, 0.6);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
        text-decoration: none;
        color: inherit;
        transition: all 0.25s ease;
    }

    .timeline-card:hover {
        background: rgba(210, 180, 140, 0.08);
        border-color: rgba(210, 180, 140, 0.3);
        transform: translateX(8px);
    }

    .timeline-number {
        width: 32px;
        height: 32px;
        background: rgba(210, 180, 140, 0.15);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--gk-cyan);
        flex-shrink: 0;
    }

    .timeline-content {
        flex: 1;
        min-width: 0;
    }

    .timeline-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
        color: #e2e8f0;
    }

    .timeline-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.75rem;
        color: #64748b;
    }

    .timeline-badges {
        display: flex;
        gap: 0.4rem;
        flex-shrink: 0;
    }

    .badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-lesson {
        background: rgba(210, 180, 140, 0.15);
        color: var(--gk-cyan);
    }

    .badge-lab {
        background: rgba(196, 167, 125, 0.15);
        color: var(--gk-teal);
    }

    .badge-video {
        background: rgba(251, 191, 36, 0.15);
        color: var(--gk-gold);
    }

    /* Instructor */
    .instructor-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: rgba(210, 180, 140, 0.05);
        border: 1px solid rgba(210, 180, 140, 0.1);
        border-radius: 12px;
        margin-top: 1.5rem;
    }

    .instructor-avatar {
        width: 52px;
        height: 52px;
        background: linear-gradient(135deg, var(--gk-cyan), var(--gk-purple));
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .instructor-info h4 {
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.2rem;
    }

    .instructor-info p {
        font-size: 0.8rem;
        color: #64748b;
    }
</style>
@endpush

@section('content')
    <!-- SVG Gradient Definition -->
    <svg width="0" height="0">
        <defs>
            <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" style="stop-color: var(--gk-cyan)" />
                <stop offset="100%" style="stop-color: var(--gk-teal)" />
            </linearGradient>
        </defs>
    </svg>

    <div class="module-page">
        <!-- Main Content -->
        <div class="module-content">
            <!-- Hero Section -->
            <div class="module-hero">
                <div class="hero-content">
                    <div>
                        <div class="category-pills">
                            <span class="pill">Kubernetes</span>
                            <span class="pill">DevOps</span>
                            <span class="pill">Cloud Native</span>
                        </div>
                        
                        <h1>{{ $module->title }}</h1>
                        
                        <div class="level-indicator">
                            <span class="level-badge">Beginner</span>
                            <div class="level-dots">
                                <span class="level-dot active"></span>
                                <span class="level-dot"></span>
                                <span class="level-dot"></span>
                            </div>
                        </div>
                        
                        @if($module->description)
                            <p class="module-description">{{ $module->description }}</p>
                        @endif
                        
                        <div class="hero-stats">
                            <div class="stat-item">
                                <div class="stat-value">{{ $module->lessons->count() }}</div>
                                <div class="stat-label">Lessons</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ $module->labs->count() }}</div>
                                <div class="stat-label">Labs</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ round(($module->lessons->count() * 15 + $module->labs->sum('estimated_minutes')) / 60, 1) }}h</div>
                                <div class="stat-label">Duration</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="hero-visual">
                        <div class="hero-visual-orb"></div>
                        <div class="hero-visual-icon">☸️</div>
                    </div>
                </div>
            </div>

            <!-- Info Cards Row - Below Hero -->
            <div class="info-cards-row">
                <!-- Progress Card -->
                <div class="glass-card">
                    <h3>Your Progress</h3>
                    @auth
                        @php 
                            $progress = Auth::user()->getModuleProgress($module);
                            $strokeDashoffset = 226 - (226 * $progress['percentage'] / 100);
                        @endphp
                        <div class="progress-ring-container">
                            <div class="progress-ring">
                                <svg width="80" height="80">
                                    <circle class="progress-ring-bg" cx="40" cy="40" r="36"/>
                                    <circle class="progress-ring-fill" cx="40" cy="40" r="36" style="stroke-dashoffset: {{ $strokeDashoffset }};"/>
                                </svg>
                                <span class="progress-ring-text">{{ $progress['percentage'] }}%</span>
                            </div>
                            <div class="progress-info">
                                <h4>{{ $progress['percentage'] == 100 ? 'Complete!' : ($progress['percentage'] > 0 ? 'In Progress' : 'Get Started') }}</h4>
                                <p>{{ $progress['completed'] }} of {{ $progress['total'] }} completed</p>
                            </div>
                        </div>
                    @else
                        <div class="progress-ring-container">
                            <div class="progress-ring">
                                <svg width="80" height="80">
                                    <circle class="progress-ring-bg" cx="40" cy="40" r="36"/>
                                    <circle class="progress-ring-fill" cx="40" cy="40" r="36"/>
                                </svg>
                                <span class="progress-ring-text">0%</span>
                            </div>
                            <div class="progress-info">
                                <h4>Sign in to track</h4>
                                <p>0 of {{ $module->lessons->count() }} completed</p>
                            </div>
                        </div>
                    @endauth
                    
                    @if($module->lessons->count() > 0)
                        <form action="{{ route('modules.start-lab', $module->slug) }}" method="POST" style="margin-top: 1rem;">
                            @csrf
                            <button type="submit" class="btn-start">⚡ Begin Course</button>
                        </form>
                    @endif
                </div>

                <!-- What's Included Card -->
                <div class="glass-card">
                    <h3>What's Included</h3>
                    <div class="includes-grid">
                        <div class="include-item">
                            <span class="include-icon">🧪</span>
                            <span class="include-text">{{ $module->labs->count() }} Labs</span>
                        </div>
                        <div class="include-item">
                            <span class="include-icon">📚</span>
                            <span class="include-text">{{ $module->lessons->count() }} Lessons</span>
                        </div>
                        <div class="include-item">
                            <span class="include-icon">📹</span>
                            <span class="include-text">{{ $module->lessons->whereNotNull('video_url')->count() }} Videos</span>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="glass-card">
                    <h3>Quick Actions</h3>
                    @auth
                        @php $isSaved = Auth::user()->hasSavedModule($module); @endphp
                        <button id="saveBtn" onclick="toggleSave()" class="btn-secondary" style="width: 100%; {{ $isSaved ? 'background: rgba(210, 180, 140, 0.2); color: var(--gk-cyan); border-color: var(--gk-cyan);' : '' }}">
                            <span id="saveIcon">{{ $isSaved ? '✓' : '🔖' }}</span>
                            <span id="saveText">{{ $isSaved ? 'Saved' : 'Save for Later' }}</span>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary" style="display: block; text-align: center; text-decoration: none; width: 100%;">
                            🔖 Save for Later
                        </a>
                    @endauth
                    

                </div>
            </div>

            <!-- Instructor -->
            <div class="instructor-row">
                <div class="instructor-avatar">👨‍💻</div>
                <div class="instructor-info">
                    <h4>GovKloud Training Team</h4>
                    <p>Federal Cloud & DevOps Specialists</p>
                </div>
            </div>

            <!-- Curriculum Section -->
            <section class="curriculum-section">
                <div class="section-header">
                    <div class="section-icon">📚</div>
                    <div>
                        <h2>Course Curriculum</h2>
                        <span>{{ $module->lessons->count() + $module->labs->count() }} chapters • {{ round(($module->lessons->count() * 15 + $module->labs->sum('estimated_minutes')) / 60, 1) }} hours</span>
                    </div>
                </div>
                
                <div class="curriculum-timeline">
                    @foreach($module->lessons as $lesson)
                        @php $lessonCompleted = Auth::check() && Auth::user()->hasCompletedLesson($lesson); @endphp
                        <div class="timeline-item {{ $lessonCompleted ? 'completed' : '' }}">
                            <form action="{{ route('modules.start-lab', $module->slug) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="timeline-card" style="width: 100%; border: none; cursor: pointer; font-family: inherit; font-size: inherit; color: inherit; text-align: left; -webkit-appearance: none; appearance: none;">
                                    <div class="timeline-number" style="{{ $lessonCompleted ? 'background: rgba(16, 185, 129, 0.2); color: #10b981;' : '' }}">
                                        @if($lessonCompleted)
                                            ✓
                                        @else
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        @endif
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-title" style="{{ $lessonCompleted ? 'text-decoration: line-through; opacity: 0.7;' : '' }}">{{ $lesson->title }}</div>
                                        <div class="timeline-meta">
                                            <span>📖 Lesson</span>
                                            @if($lesson->video_url)<span>📹 Video</span>@endif
                                            @if($lesson->hasLab())<span>🧪 Lab Included</span>@endif
                                            @if($lessonCompleted)<span style="color: #10b981;">✅ Done</span>@endif
                                        </div>
                                    </div>
                                    <div class="timeline-badges">
                                        @if($lessonCompleted)
                                            <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">Complete</span>
                                        @else
                                            <span class="badge badge-lesson">Lesson</span>
                                            @if($lesson->video_url)<span class="badge badge-video">Video</span>@endif
                                            @if($lesson->hasLab())<span class="badge badge-lab">Lab</span>@endif
                                        @endif
                                    </div>
                                </button>
                            </form>
                        </div>
                    @endforeach

                    @foreach($module->labs as $lab)
                        <div class="timeline-item">
                            <form action="{{ route('modules.start-lab', $module->slug) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="timeline-card" style="width: 100%; border: none; cursor: pointer; font-family: inherit; font-size: inherit; color: inherit; text-align: left; -webkit-appearance: none; appearance: none;">
                                    <div class="timeline-number" style="background: rgba(139, 92, 246, 0.2); color: #8b5cf6;">
                                        {{ str_pad($module->lessons->count() + $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">{{ $lab->title }}</div>
                                        <div class="timeline-meta">
                                            <span>🧪 Hands-on Lab</span>
                                            @if($lab->estimated_minutes)<span>⏱️ {{ $lab->estimated_minutes }} min</span>@endif
                                        </div>
                                    </div>
                                    <div class="timeline-badges">
                                        <span class="badge badge-lab">Lab</span>
                                        @if($lab->estimated_minutes)<span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">{{ $lab->estimated_minutes }} min</span>@endif
                                    </div>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    @auth
    <script>
        async function toggleSave() {
            const btn = document.getElementById('saveBtn');
            const icon = document.getElementById('saveIcon');
            const text = document.getElementById('saveText');
            
            btn.disabled = true;
            btn.style.opacity = '0.6';
            
            try {
                const response = await fetch('{{ route('modules.save', $module->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.saved) {
                    icon.textContent = '✓';
                    text.textContent = 'Saved';
                    btn.style.background = 'rgba(210, 180, 140, 0.2)';
                    btn.style.color = 'var(--gk-cyan)';
                    btn.style.borderColor = 'var(--gk-cyan)';
                } else {
                    icon.textContent = '🔖';
                    text.textContent = 'Save for Later';
                    btn.style.background = '';
                    btn.style.color = '';
                    btn.style.borderColor = '';
                }
            } catch (error) {
                console.error('Error:', error);
            }
            
            btn.disabled = false;
            btn.style.opacity = '1';
        }
    </script>
    @endauth
@endsection