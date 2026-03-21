@extends('layouts.govkloud')

@section('title', 'Career Paths - GovKloud Labs')

@push('styles')
<style>
    :root {
        --gk-navy: #0a0f1a;
        --gk-dark: #0f172a;
        --gk-slate: #1e293b;
        --gk-cyan: #D2B48C;
        --gk-teal: #C4A77D;
        --gk-gold: #fbbf24;
        --gk-purple: #8b5cf6;
    }

    .career-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    /* ========================================
       HERO
       ======================================== */
    .career-hero {
        text-align: center;
        margin-bottom: 2rem;
    }

    .career-hero h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ffffff, var(--gk-cyan));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.75rem;
    }

    .career-hero p {
        font-size: 1.1rem;
        color: #94a3b8;
        max-width: 600px;
        margin: 0 auto;
    }

    /* ========================================
       PIE CHART
       ======================================== */
    .pie-container {
        position: relative;
        width: 500px;
        height: 500px;
        margin: 0 auto 3rem;
    }

    .pie-svg {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0 4px 30px rgba(0,0,0,0.4));
    }

    .pie-slice {
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        transform-origin: 250px 250px;
    }

    .pie-slice:hover,
    .pie-slice.active {
        transform: scale(1.06);
        filter: brightness(1.3);
    }

    .pie-slice:hover .pie-slice-fill,
    .pie-slice.active .pie-slice-fill {
        stroke: rgba(255,255,255,0.5);
        stroke-width: 2;
    }

    .pie-label {
        pointer-events: none;
        font-size: 11px;
        font-weight: 700;
        fill: #ffffff;
        text-anchor: middle;
        dominant-baseline: central;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pie-label-icon {
        font-size: 22px;
        pointer-events: none;
    }

    .pie-label-count {
        font-size: 9px;
        font-weight: 600;
        fill: rgba(255,255,255,0.7);
        pointer-events: none;
        text-anchor: middle;
        dominant-baseline: central;
    }

    /* Center circle */
    .pie-center-bg {
        fill: var(--gk-dark);
        stroke: rgba(210, 180, 140, 0.3);
        stroke-width: 2;
    }

    .pie-center-label {
        font-size: 12px;
        font-weight: 800;
        fill: var(--gk-cyan);
        text-anchor: middle;
        dominant-baseline: central;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        pointer-events: none;
    }

    .pie-center-icon {
        font-size: 28px;
        text-anchor: middle;
        dominant-baseline: central;
        pointer-events: none;
    }

    /* ========================================
       MOBILE GRID
       ======================================== */
    .mobile-categories {
        display: none;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .mobile-cat-card {
        background: var(--gk-slate);
        border: 2px solid rgba(210, 180, 140, 0.15);
        border-radius: 16px;
        padding: 1.25rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .mobile-cat-card:hover,
    .mobile-cat-card.active {
        border-color: var(--gk-cyan);
        background: rgba(210, 180, 140, 0.08);
    }

    .mobile-cat-card .cat-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .mobile-cat-card .cat-name { font-size: 0.8rem; font-weight: 700; color: #e2e8f0; }
    .mobile-cat-card .cat-count { font-size: 0.7rem; color: var(--gk-cyan); }

    @media (max-width: 600px) {
        .pie-container { display: none; }
        .mobile-categories { display: grid; }
        .career-hero h1 { font-size: 1.75rem; }
    }

    /* ========================================
       SELECTED CATEGORY SECTION
       ======================================== */
    .category-detail {
        display: none;
        animation: fadeSlideUp 0.5s ease;
    }

    .category-detail.visible { display: block; }

    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .category-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(210, 180, 140, 0.15);
    }

    .category-header-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, var(--gk-cyan), var(--gk-purple));
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        box-shadow: 0 4px 20px rgba(210, 180, 140, 0.3);
    }

    .category-header h2 { font-size: 1.5rem; font-weight: 700; }
    .category-header p { font-size: 0.85rem; color: #94a3b8; }

    /* Level Section */
    .level-section { margin-bottom: 2rem; }

    .level-label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
    }

    .level-label.beginner {
        background: rgba(34, 197, 94, 0.15); color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .level-label.intermediate {
        background: rgba(251, 191, 36, 0.15); color: #fbbf24;
        border: 1px solid rgba(251, 191, 36, 0.3);
    }
    .level-label.advanced {
        background: rgba(239, 68, 68, 0.15); color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .level-dots { display: inline-flex; gap: 4px; margin-left: 0.25rem; }
    .level-dot { width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,0.2); }
    .level-dot.filled { background: currentColor; }

    /* Module Cards */
    .module-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .mod-card {
        background: var(--gk-slate);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
    }

    .mod-card:hover {
        border-color: rgba(210, 180, 140, 0.3);
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    }

    .mod-card-banner {
        height: 140px;
        background: linear-gradient(135deg, rgba(210, 180, 140, 0.1), rgba(139, 92, 246, 0.08));
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .mod-card-banner img { width: 100%; height: 100%; object-fit: cover; }
    .mod-card-banner .fallback-icon { font-size: 3rem; opacity: 0.6; }

    .mod-card-badges {
        position: absolute; top: 0.75rem; left: 0.75rem;
        display: flex; gap: 0.4rem; flex-wrap: wrap;
    }

    .mod-badge {
        padding: 0.2rem 0.5rem; border-radius: 4px;
        font-size: 0.6rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.3px;
    }
    .mod-badge-lessons { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    .mod-badge-labs { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .mod-badge-videos { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
    .mod-badge-sub { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

    .mod-card-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }

    .mod-card-techs { display: flex; flex-wrap: wrap; gap: 0.3rem; margin-bottom: 0.75rem; }

    .tech-pill {
        padding: 0.15rem 0.5rem;
        background: rgba(210, 180, 140, 0.1);
        border: 1px solid rgba(210, 180, 140, 0.2);
        border-radius: 50px;
        font-size: 0.6rem; font-weight: 600; color: var(--gk-cyan);
        text-transform: uppercase; letter-spacing: 0.3px;
    }

    .mod-card-title { font-size: 1.05rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.3; }
    .mod-card-desc { font-size: 0.8rem; color: #94a3b8; line-height: 1.5; flex: 1; }

    .mod-card-meta {
        display: flex; gap: 1rem; margin-top: 0.75rem; padding-top: 0.75rem;
        border-top: 1px solid rgba(255,255,255,0.06);
        font-size: 0.75rem; color: #64748b;
    }

    .mod-card-cta {
        display: block; text-align: center; padding: 0.75rem;
        background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
        color: var(--gk-dark); font-weight: 700; font-size: 0.8rem;
        text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px;
        transition: all 0.2s;
    }
    .mod-card-cta:hover { opacity: 0.9; }

    .empty-level {
        padding: 1.5rem; text-align: center;
        background: rgba(210, 180, 140, 0.03);
        border: 1px dashed rgba(210, 180, 140, 0.15);
        border-radius: 12px; color: #64748b; font-size: 0.85rem;
    }

    .select-prompt {
        text-align: center; color: #64748b; padding: 3rem 1rem; font-size: 1rem;
    }
    .select-prompt .prompt-icon { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
</style>
@endpush

@section('content')
<div class="career-page">
    <!-- Hero -->
    <div class="career-hero">
        <h1>Choose Your Career Path</h1>
        <p>Click a slice of the pie to explore modules organized by skill level — from beginner to advanced.</p>
    </div>

    @php
        $categoryMeta = [
            'DevSecOps'         => ['icon' => '🛡️', 'color' => '#6366f1', 'desc' => 'Integrate security into every stage of the DevOps lifecycle'],
            'Cloud Engineer'    => ['icon' => '☁️', 'color' => '#06b6d4', 'desc' => 'Design, deploy, and manage cloud infrastructure at scale'],
            'SRE'               => ['icon' => '📈', 'color' => '#10b981', 'desc' => 'Build reliable, scalable systems with site reliability engineering'],
            'DevOps'            => ['icon' => '🔄', 'color' => '#f59e0b', 'desc' => 'Automate and streamline software delivery pipelines'],
            'Platform Engineer' => ['icon' => '🏗️', 'color' => '#ef4444', 'desc' => 'Build internal developer platforms and golden paths'],
            'Security Engineer' => ['icon' => '🔒', 'color' => '#ec4899', 'desc' => 'Protect infrastructure, data, and applications from threats'],
            'Data Engineer'     => ['icon' => '📊', 'color' => '#8b5cf6', 'desc' => 'Build and maintain data pipelines and analytics platforms'],
        ];
        $catKeys = $categories->keys()->values();
        $sliceCount = $catKeys->count();

        // All 7 categories for the pie (even if they don't have modules yet)
        $allCategories = collect(array_keys($categoryMeta));
        // Merge: show categories that have modules + ones that don't
        $pieCategories = $allCategories;
        $pieCount = $pieCategories->count();
    @endphp

    <!-- SVG Pie Chart (desktop) -->
    <div class="pie-container">
        <svg class="pie-svg" viewBox="0 0 500 500">
            <!-- Pie slices -->
            @foreach($pieCategories as $i => $catName)
                @php
                    $meta = $categoryMeta[$catName] ?? ['icon' => '📁', 'color' => '#64748b', 'desc' => ''];
                    $startAngle = ($i / $pieCount) * 360 - 90;
                    $endAngle = (($i + 1) / $pieCount) * 360 - 90;
                    $midAngle = ($startAngle + $endAngle) / 2;

                    $outerR = 220;
                    $innerR = 80;
                    $labelR = 155;

                    // Convert to radians
                    $startRad = deg2rad($startAngle);
                    $endRad = deg2rad($endAngle);
                    $midRad = deg2rad($midAngle);

                    // Outer arc points
                    $x1 = 250 + $outerR * cos($startRad);
                    $y1 = 250 + $outerR * sin($startRad);
                    $x2 = 250 + $outerR * cos($endRad);
                    $y2 = 250 + $outerR * sin($endRad);

                    // Inner arc points
                    $x3 = 250 + $innerR * cos($endRad);
                    $y3 = 250 + $innerR * sin($endRad);
                    $x4 = 250 + $innerR * cos($startRad);
                    $y4 = 250 + $innerR * sin($startRad);

                    // Large arc flag
                    $largeArc = ($endAngle - $startAngle) > 180 ? 1 : 0;

                    // Label position
                    $lx = 250 + $labelR * cos($midRad);
                    $ly = 250 + $labelR * sin($midRad);

                    // Module count for this category
                    $catModuleCount = isset($categories[$catName]) ? $categories[$catName]->count() : 0;

                    // Darken color slightly for gradient
                    $baseColor = $meta['color'];
                @endphp
                <g class="pie-slice" data-category="{{ $catName }}" onclick="selectCategory('{{ addslashes($catName) }}')">
                    <defs>
                        <linearGradient id="grad-{{ $i }}" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:{{ $baseColor }};stop-opacity:0.85" />
                            <stop offset="100%" style="stop-color:{{ $baseColor }};stop-opacity:0.55" />
                        </linearGradient>
                    </defs>
                    <path class="pie-slice-fill"
                          d="M {{ $x1 }} {{ $y1 }}
                             A {{ $outerR }} {{ $outerR }} 0 {{ $largeArc }} 1 {{ $x2 }} {{ $y2 }}
                             L {{ $x3 }} {{ $y3 }}
                             A {{ $innerR }} {{ $innerR }} 0 {{ $largeArc }} 0 {{ $x4 }} {{ $y4 }}
                             Z"
                          fill="url(#grad-{{ $i }})"
                          stroke="rgba(0,0,0,0.3)"
                          stroke-width="1.5" />
                    <!-- Icon -->
                    <text class="pie-label-icon" x="{{ $lx }}" y="{{ $ly - 14 }}">{{ $meta['icon'] }}</text>
                    <!-- Name -->
                    <text class="pie-label" x="{{ $lx }}" y="{{ $ly + 8 }}">{{ Str::upper(Str::limit($catName, 14, '')) }}</text>
                    <!-- Count -->
                    <text class="pie-label-count" x="{{ $lx }}" y="{{ $ly + 22 }}">{{ $catModuleCount }} {{ Str::plural('module', $catModuleCount) }}</text>
                </g>
            @endforeach

            <!-- Center circle -->
            <circle class="pie-center-bg" cx="250" cy="250" r="75" />
            <text class="pie-center-icon" x="250" y="240">🎯</text>
            <text class="pie-center-label" x="250" y="264" id="pieCenterText">Pick a Career</text>
        </svg>
    </div>

    <!-- Mobile category grid -->
    <div class="mobile-categories">
        @foreach($pieCategories as $catName)
            @php $meta = $categoryMeta[$catName] ?? ['icon' => '📁', 'color' => '#64748b', 'desc' => '']; @endphp
            <div class="mobile-cat-card" data-category="{{ $catName }}"
                 onclick="selectCategory('{{ addslashes($catName) }}')">
                <div class="cat-icon">{{ $meta['icon'] }}</div>
                <div class="cat-name">{{ $catName }}</div>
                <div class="cat-count">{{ isset($categories[$catName]) ? $categories[$catName]->count() : 0 }} modules</div>
            </div>
        @endforeach
    </div>

    <!-- Prompt -->
    <div class="select-prompt" id="selectPrompt">
        <div class="prompt-icon">👆</div>
        <p>Select a career path above to see available modules</p>
    </div>

    <!-- Category Detail Sections -->
    @foreach($categories as $catName => $catModules)
        @php
            $meta = $categoryMeta[$catName] ?? ['icon' => '📁', 'color' => '#64748b', 'desc' => ''];
            $levels = ['Beginner', 'Intermediate', 'Advanced'];
        @endphp
        <div class="category-detail" id="cat-{{ Str::slug($catName) }}">
            <div class="category-header">
                <div class="category-header-icon">{{ $meta['icon'] }}</div>
                <div>
                    <h2>{{ $catName }}</h2>
                    <p>{{ $meta['desc'] }}</p>
                </div>
            </div>

            @foreach($levels as $level)
                @php $levelModules = $catModules->where('level', $level); @endphp
                <div class="level-section">
                    <div class="level-label {{ strtolower($level) }}">
                        {{ $level }}
                        <span class="level-dots">
                            <span class="level-dot filled"></span>
                            <span class="level-dot {{ in_array($level, ['Intermediate', 'Advanced']) ? 'filled' : '' }}"></span>
                            <span class="level-dot {{ $level === 'Advanced' ? 'filled' : '' }}"></span>
                        </span>
                    </div>

                    @if($levelModules->count() > 0)
                        <div class="module-cards">
                            @foreach($levelModules as $module)
                                @php
                                    $techs = $module->lessons->pluck('subcategory')->filter()->unique()->values();
                                    $videoCount = $module->lessons->whereNotNull('video_url')->count();
                                @endphp
                                <a href="{{ route('courses.show', $module->slug) }}" class="mod-card">
                                    <div class="mod-card-banner" @if($module->banner_image) style="background: none;" @endif>
                                        @if($module->banner_image)
                                            <img src="{{ Storage::disk('azure')->url($module->banner_image) }}" alt="{{ $module->title }}">
                                        @else
                                            <span class="fallback-icon">{{ $meta['icon'] }}</span>
                                        @endif
                                        <div class="mod-card-badges">
                                            <span class="mod-badge mod-badge-lessons">{{ $module->lessons->count() }} Lessons</span>
                                            @if($module->labs->count() > 0)
                                                <span class="mod-badge mod-badge-labs">{{ $module->labs->count() }} Labs</span>
                                            @endif
                                            @if($videoCount > 0)
                                                <span class="mod-badge mod-badge-videos">{{ $videoCount }} Videos</span>
                                            @endif
                                            @if($module->requires_subscription)
                                                <span class="mod-badge mod-badge-sub">🔒 PRO</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mod-card-body">
                                        @if($techs->count() > 0)
                                            <div class="mod-card-techs">
                                                @foreach($techs->take(4) as $tech)
                                                    <span class="tech-pill">{{ $tech }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <h3 class="mod-card-title">{{ $module->title }}</h3>
                                        <p class="mod-card-desc">{{ Str::limit($module->description, 100) }}</p>
                                        <div class="mod-card-meta">
                                            <span>📚 {{ $module->lessons->count() }} lessons</span>
                                            <span>🧪 {{ $module->labs->count() }} labs</span>
                                            <span>⏱️ ~{{ round(($module->lessons->count() * 15 + $module->labs->sum('estimated_minutes')) / 60, 1) }}h</span>
                                        </div>
                                    </div>
                                    <div class="mod-card-cta">View Course →</div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-level">No {{ strtolower($level) }} modules yet for {{ $catName }} — stay tuned!</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    <!-- Empty category detail for categories with 0 modules -->
    @foreach($pieCategories as $catName)
        @if(!isset($categories[$catName]))
            @php $meta = $categoryMeta[$catName] ?? ['icon' => '📁', 'color' => '#64748b', 'desc' => '']; @endphp
            <div class="category-detail" id="cat-{{ Str::slug($catName) }}">
                <div class="category-header">
                    <div class="category-header-icon">{{ $meta['icon'] }}</div>
                    <div>
                        <h2>{{ $catName }}</h2>
                        <p>{{ $meta['desc'] }}</p>
                    </div>
                </div>
                <div class="empty-level" style="margin-top: 1rem;">
                    No modules available yet for {{ $catName }} — stay tuned! 🚀
                </div>
            </div>
        @endif
    @endforeach
</div>
@endsection

@push('scripts')
<script>
    function selectCategory(name) {
        // Remove active from all slices and mobile cards
        document.querySelectorAll('.pie-slice, .mobile-cat-card').forEach(el => el.classList.remove('active'));

        // Add active to matching elements
        document.querySelectorAll(`[data-category="${name}"]`).forEach(el => el.classList.add('active'));

        // Hide prompt
        document.getElementById('selectPrompt').style.display = 'none';

        // Hide all details, show selected
        document.querySelectorAll('.category-detail').forEach(el => el.classList.remove('visible'));

        const slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        const detail = document.getElementById('cat-' + slug);
        if (detail) {
            detail.classList.add('visible');
            detail.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Update center text
        const center = document.getElementById('pieCenterText');
        if (center) center.textContent = name;
    }
</script>
@endpush