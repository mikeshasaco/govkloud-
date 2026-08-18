@extends('layouts.govkloud')

@section('title', 'Problems - GovKloud')

@push('styles')
    <style>
        /* Page Header */
        .problems-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-top: 1rem;
        }

        .problems-header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, var(--gk-cyan) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .problems-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        /* Stats Banner */
        .stats-banner {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin: 1.5rem 0 2rem;
            flex-wrap: wrap;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid var(--border);
            border-radius: 12px;
            min-width: 140px;
        }

        .stat-icon {
            font-size: 1.25rem;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Category Tabs */
        .category-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .category-tab {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.15rem;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .category-tab:hover {
            border-color: var(--gk-cyan);
            color: var(--text);
        }

        .category-tab.active {
            background: rgba(210, 180, 140, 0.15);
            border-color: var(--gk-cyan);
            color: var(--gk-cyan);
        }

        .category-tab .tab-icon {
            font-size: 1rem;
        }

        /* Controls Row: Search + Difficulty + Solved Counter */
        .controls-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            width: 220px;
        }

        .search-box input {
            width: 100%;
            padding: 0.5rem 0.75rem 0.5rem 2.25rem;
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 0.8rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .search-box input::placeholder {
            color: var(--text-muted);
        }

        .search-box input:focus {
            border-color: var(--gk-cyan);
        }

        .search-icon {
            position: absolute;
            left: 0.65rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            width: 14px;
            height: 14px;
        }

        /* Difficulty Filter Pills */
        .difficulty-pills {
            display: flex;
            gap: 0.35rem;
        }

        .difficulty-pill {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            border: 1.5px solid transparent;
            transition: all 0.2s ease;
        }

        .difficulty-pill .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        .difficulty-pill.beginner { background: rgba(34, 197, 94, 0.1); color: #22c55e; border-color: rgba(34, 197, 94, 0.2); }
        .difficulty-pill.beginner .dot { background: #22c55e; }
        .difficulty-pill.beginner:hover, .difficulty-pill.beginner.active { background: #22c55e; color: #fff; border-color: #22c55e; }
        .difficulty-pill.beginner.active .dot, .difficulty-pill.beginner:hover .dot { background: #fff; }

        .difficulty-pill.medium { background: rgba(249, 115, 22, 0.1); color: #f97316; border-color: rgba(249, 115, 22, 0.2); }
        .difficulty-pill.medium .dot { background: #f97316; }
        .difficulty-pill.medium:hover, .difficulty-pill.medium.active { background: #f97316; color: #fff; border-color: #f97316; }
        .difficulty-pill.medium.active .dot, .difficulty-pill.medium:hover .dot { background: #fff; }

        .difficulty-pill.hard { background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: rgba(239, 68, 68, 0.2); }
        .difficulty-pill.hard .dot { background: #ef4444; }
        .difficulty-pill.hard:hover, .difficulty-pill.hard.active { background: #ef4444; color: #fff; border-color: #ef4444; }
        .difficulty-pill.hard.active .dot, .difficulty-pill.hard:hover .dot { background: #fff; }

        .solved-counter {
            margin-left: auto;
            font-size: 0.8rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .solved-counter .solved-ring {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid var(--border);
            display: inline-block;
        }

        .solved-counter .solved-ring.has-progress {
            border-color: #22c55e;
            border-right-color: var(--border);
        }

        /* ═══════════════════════════════════════════════ */
        /* PROBLEM LIST TABLE (LeetCode-style rows)       */
        /* ═══════════════════════════════════════════════ */
        .problem-list {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 4px;
        }

        .problem-row {
            cursor: pointer;
            transition: background 0.15s ease;
            text-decoration: none;
            display: table-row;
        }

        .problem-row td {
            padding: 0.85rem 1rem;
            background: var(--gk-slate);
            border: none;
            vertical-align: middle;
        }

        .problem-row td:first-child {
            border-radius: 10px 0 0 10px;
        }

        .problem-row td:last-child {
            border-radius: 0 10px 10px 0;
        }

        .problem-row:hover td {
            background: rgba(30, 41, 59, 0.9);
            box-shadow: 0 0 0 1px rgba(210, 180, 140, 0.15);
        }

        /* Status column */
        .problem-status {
            width: 36px;
            text-align: center;
        }

        .status-icon {
            font-size: 0.9rem;
        }

        .status-icon.completed {
            color: #22c55e;
        }

        .status-icon.started {
            color: #f97316;
        }

        /* Number + Title */
        .problem-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .problem-number {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
            min-width: 24px;
        }

        .problem-title {
            color: var(--text);
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.15s;
        }

        .problem-row:hover .problem-title {
            color: var(--gk-cyan);
        }

        .problem-video-icon {
            font-size: 0.75rem;
            opacity: 0.5;
            margin-left: 0.3rem;
        }

        /* Category column */
        .problem-category {
            width: 120px;
        }

        .category-badge {
            padding: 0.2rem 0.55rem;
            border-radius: 5px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .category-badge.kubernetes { background: rgba(6, 182, 212, 0.12); color: #06b6d4; }
        .category-badge.terraform { background: rgba(139, 92, 246, 0.12); color: #a78bfa; }
        .category-badge.docker { background: rgba(59, 130, 246, 0.12); color: #60a5fa; }
        .category-badge.linux { background: rgba(251, 191, 36, 0.12); color: #fbbf24; }

        /* Time column */
        .problem-time {
            width: 80px;
            text-align: right;
            color: var(--text-muted);
            font-size: 0.8rem;
            white-space: nowrap;
        }

        /* Difficulty column */
        .problem-difficulty {
            width: 80px;
            text-align: center;
        }

        .diff-label {
            font-size: 0.8rem;
            font-weight: 700;
        }

        .diff-label.beginner { color: #22c55e; }
        .diff-label.medium { color: #f97316; }
        .diff-label.hard { color: #ef4444; }

        /* Tags column */
        .problem-tags {
            width: 160px;
        }

        .tag-pills {
            display: flex;
            gap: 0.3rem;
            flex-wrap: wrap;
        }

        .tag-pill {
            padding: 0.1rem 0.45rem;
            background: rgba(148, 163, 184, 0.08);
            border-radius: 4px;
            font-size: 0.6rem;
            color: var(--text-muted);
            font-weight: 500;
            white-space: nowrap;
        }

        /* Lock icon */
        .problem-lock {
            width: 36px;
            text-align: center;
            font-size: 0.75rem;
            opacity: 0.35;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-muted);
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .problems-header h1 { font-size: 2rem; }
            .controls-row { flex-direction: column; align-items: flex-start; }
            .search-box { width: 100%; }
            .solved-counter { margin-left: 0; }
            .problem-tags, .problem-category, .problem-time { display: none; }
            .stats-banner { flex-direction: column; align-items: center; }
        }
    </style>
@endpush

@section('content')
    <!-- Header -->
    <div class="problems-header">
        <h1>Problems</h1>
        <p>Sharpen your DevOps skills with hands-on coding challenges</p>

        @auth
            @if($stats)
                <div class="stats-banner">
                    <div class="stat-card">
                        <span class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gk-cyan)" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></span>
                        <div>
                            <div class="stat-number">{{ $categoryCounts['all'] }}</div>
                            <div class="stat-label">Challenges</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                        <div>
                            <div class="stat-number">{{ $stats['total_completed'] }}</div>
                            <div class="stat-label">Completed</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></span>
                        <div>
                            <div class="stat-number">{{ $stats['total_started'] }}</div>
                            <div class="stat-label">Attempted</div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </div>

    <!-- Category Tabs -->
    <div class="category-tabs">
        <a href="{{ route('problems.index') }}"
            class="category-tab {{ !request('category') ? 'active' : '' }}">
            <svg class="tab-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg> All Topics
        </a>
        <a href="{{ route('problems.index', ['category' => 'kubernetes', 'difficulty' => request('difficulty')]) }}"
            class="category-tab {{ request('category') === 'kubernetes' ? 'active' : '' }}">
            <svg class="tab-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v12M6 12h12"/></svg> Kubernetes
        </a>
        <a href="{{ route('problems.index', ['category' => 'terraform', 'difficulty' => request('difficulty')]) }}"
            class="category-tab {{ request('category') === 'terraform' ? 'active' : '' }}">
            <svg class="tab-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg> Terraform
        </a>
        <a href="{{ route('problems.index', ['category' => 'docker', 'difficulty' => request('difficulty')]) }}"
            class="category-tab {{ request('category') === 'docker' ? 'active' : '' }}">
            <svg class="tab-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg> Docker
        </a>
        <a href="{{ route('problems.index', ['category' => 'linux', 'difficulty' => request('difficulty')]) }}"
            class="category-tab {{ request('category') === 'linux' ? 'active' : '' }}">
            <svg class="tab-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16M4 20V10l8-6 8 6v10"/><rect x="9" y="14" width="6" height="6"/></svg> Linux
        </a>
    </div>

    <!-- Controls Row: Search + Difficulty + Solved -->
    <div class="controls-row">
        <div class="search-box">
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" id="searchInput" placeholder="Search questions"
                value="{{ request('search') }}"
                onkeydown="if(event.key==='Enter'){window.location.href='{{ route('problems.index') }}?search='+this.value+'{{ request('category') ? '&category='.request('category') : '' }}{{ request('difficulty') ? '&difficulty='.request('difficulty') : '' }}'}">
        </div>

        <div class="difficulty-pills">
            <a href="{{ route('problems.index', array_merge(request()->except('difficulty'), request('difficulty') === 'beginner' ? [] : ['difficulty' => 'beginner'])) }}"
                class="difficulty-pill beginner {{ request('difficulty') === 'beginner' ? 'active' : '' }}">
                <span class="dot"></span> Beginner
            </a>
            <a href="{{ route('problems.index', array_merge(request()->except('difficulty'), request('difficulty') === 'medium' ? [] : ['difficulty' => 'medium'])) }}"
                class="difficulty-pill medium {{ request('difficulty') === 'medium' ? 'active' : '' }}">
                <span class="dot"></span> Medium
            </a>
            <a href="{{ route('problems.index', array_merge(request()->except('difficulty'), request('difficulty') === 'hard' ? [] : ['difficulty' => 'hard'])) }}"
                class="difficulty-pill hard {{ request('difficulty') === 'hard' ? 'active' : '' }}">
                <span class="dot"></span> Hard
            </a>
        </div>

        <div class="solved-counter">
            <span class="solved-ring {{ ($stats['total_completed'] ?? 0) > 0 ? 'has-progress' : '' }}"></span>
            {{ $stats['total_completed'] ?? 0 }}/{{ $categoryCounts['all'] }} Solved
        </div>
    </div>

    <!-- Problem List (table rows) -->
    @if($challenges->count() > 0)
        <table class="problem-list">
            <tbody>
                @foreach($challenges as $i => $challenge)
                    <tr class="problem-row"
                        id="problem-{{ $challenge->slug }}"
                        onclick="window.location.href='{{ auth()->check() ? route('problems.show', $challenge->slug) : route('login') }}'">

                        {{-- Status --}}
                        <td class="problem-status">
                            @if(in_array($challenge->id, $completedIds))
                                <span class="status-icon completed" title="Completed">✓</span>
                            @endif
                        </td>

                        {{-- Number + Title --}}
                        <td>
                            <div class="problem-info">
                                <span class="problem-number">{{ $i + 1 }}.</span>
                                <span class="problem-title">{{ $challenge->title }}</span>
                                @if($challenge->hasVideo())
                                    <span class="problem-video-icon" title="Tutorial video available"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h16v16H4V4zm2 2v12h12V6H6zm3 2l6 4-6 4V8z"/></svg></span>
                                @endif
                            </div>
                        </td>

                        {{-- Category --}}
                        <td class="problem-category">
                            <span class="category-badge {{ $challenge->category }}">
                                {{ ucfirst($challenge->category) }}
                            </span>
                            @if($challenge->problem_type)
                                <span class="category-badge" style="background:rgba(210,180,140,0.1);color:var(--gk-tan);margin-left:4px;">
                                    {{ $challenge->getProblemTypeLabel() }}
                                </span>
                            @endif
                        </td>

                        {{-- Estimated Time --}}
                        <td class="problem-time">
                            ~{{ $challenge->estimated_minutes }} min
                            @if($challenge->points)
                                <span style="color:var(--gk-cyan);font-size:0.7rem;margin-left:4px;">{{ $challenge->points }}pts</span>
                            @endif
                        </td>

                        {{-- Difficulty --}}
                        <td class="problem-difficulty">
                            <span class="diff-label {{ $challenge->difficulty }}">
                                {{ $challenge->difficulty === 'beginner' ? 'Easy' : ($challenge->difficulty === 'medium' ? 'Med.' : 'Hard') }}
                            </span>
                        </td>

                        {{-- Tags --}}
                        <td class="problem-tags">
                            @if($challenge->tags)
                                <div class="tag-pills">
                                    @foreach(array_slice($challenge->tags, 0, 2) as $tag)
                                        <span class="tag-pill">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>

                        {{-- Lock --}}
                        <td class="problem-lock">
                            @if($challenge->requiresSubscription() && auth()->check() && !auth()->user()->isSubscribed() && !auth()->user()->onTrial())
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3>No challenges found</h3>
            <p>Try adjusting your filters or check back soon for new problems.</p>
        </div>
    @endif
@endsection
