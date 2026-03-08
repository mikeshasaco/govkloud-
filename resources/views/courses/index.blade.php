@extends('layouts.govkloud')

@section('title', 'Courses - GovKloud')

@push('styles')
    <style>
        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, var(--gk-cyan) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        /* Active Filter Badge */
        .active-filter {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.75rem;
            padding: 0.5rem 1rem;
            background: rgba(210, 180, 140, 0.15);
            border: 1px solid var(--gk-cyan);
            border-radius: 8px;
            color: var(--gk-cyan);
            font-size: 0.9rem;
        }

        .active-filter a {
            color: inherit;
            text-decoration: none;
            opacity: 0.7;
        }

        .active-filter a:hover {
            opacity: 1;
        }

        /* Filter Bar */
        .filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 280px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-size: 1rem;
            transition: all 0.2s;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--gk-cyan);
            box-shadow: 0 0 0 3px rgba(210, 180, 140, 0.1);
        }

        .search-box input::placeholder {
            color: var(--text-muted);
        }

        .search-box svg {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: var(--text-muted);
        }

        .filter-select {
            padding: 0.875rem 2.5rem 0.875rem 1rem;
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-size: 0.95rem;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%2394a3b8' viewBox='0 0 20 20'%3E%3Cpath d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px;
            min-width: 150px;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--gk-cyan);
        }

        .sort-by {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: auto;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Courses Cards Grid */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.5rem;
        }

        /* Course Card */
        .course-card {
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .course-card:hover {
            border-color: var(--gk-cyan);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
        }

        .course-card-image {
            height: 160px;
            background: linear-gradient(135deg, rgba(210, 180, 140, 0.2) 0%, rgba(139, 92, 246, 0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .course-card-image::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(210, 180, 140, 0.3) 0%, transparent 70%);
        }

        .course-card-icon {
            font-size: 4rem;
            z-index: 1;
        }

        .course-card-badges {
            position: absolute;
            top: 1rem;
            left: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-lessons {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .badge-labs {
            background: rgba(139, 92, 246, 0.2);
            color: #a78bfa;
        }

        .badge-tech {
            background: rgba(210, 180, 140, 0.2);
            color: var(--gk-cyan);
        }

        .course-card-body {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .course-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .course-description {
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1rem;
            flex: 1;
        }

        .course-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        .course-meta span {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .course-card-footer {
            padding: 1rem 1.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid var(--border);
        }

        .course-card-footer a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            color: var(--gk-navy);
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .course-card-footer a:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(210, 180, 140, 0.3);
        }

        /* Results Count */
        .results-count {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--gk-slate);
            border-radius: 16px;
            border: 1px dashed var(--border);
            grid-column: 1 / -1;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .filter-bar {
                flex-direction: column;
            }

            .search-box {
                width: 100%;
            }

            .sort-by {
                margin-left: 0;
                width: 100%;
                justify-content: flex-end;
            }

            .courses-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Courses</h1>
        <p>Master cloud infrastructure with hands-on labs and guided lessons</p>

        @if(request('tech'))
            <div class="active-filter">
                <span>🏷️ Filtered by tech: <strong>{{ request('tech') }}</strong></span>
                <a href="{{ route('courses.index') }}{{ request('category') ? '?category=' . urlencode(request('category')) : '' }}"
                    title="Clear filter">✕</a>
            </div>
        @endif

        @if(request('category'))
            <div class="active-filter">
                <span>💼 Filtered by career track: <strong>{{ request('category') }}</strong></span>
                <a href="{{ route('courses.index') }}{{ request('tech') ? '?tech=' . urlencode(request('tech')) : '' }}"
                    title="Clear filter">✕</a>
            </div>
        @endif
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <input type="text" id="searchInput" placeholder="Search courses..." onkeyup="filterCourses()">
        </div>

        <select class="filter-select" id="techFilter" onchange="applyFilters()">
            <option value="">All Technologies</option>
            @foreach($technologies ?? [] as $tech)
                <option value="{{ $tech->subcategory }}" {{ request('tech') == $tech->subcategory ? 'selected' : '' }}>
                    {{ $tech->subcategory }}
                </option>
            @endforeach
        </select>

        <select class="filter-select" id="categoryFilter" onchange="applyFilters()">
            <option value="">All Career Tracks</option>
            @foreach($categories ?? [] as $cat)
                <option value="{{ $cat->category }}" {{ request('category') == $cat->category ? 'selected' : '' }}>
                    {{ $cat->category }}
                </option>
            @endforeach
        </select>

        <select class="filter-select" id="levelFilter" onchange="filterCourses()">
            <option value="">All Levels</option>
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="advanced">Advanced</option>
        </select>

        <div class="sort-by">
            <span>Sort by:</span>
            <select class="filter-select" id="sortFilter" onchange="filterCourses()">
                <option value="title">Alphabetical</option>
                <option value="lessons">Most Lessons</option>
                <option value="labs">Most Labs</option>
            </select>
        </div>
    </div>

    <!-- Results Count -->
    <div class="results-count" id="resultsCount">
        Showing {{ $modules->count() }} courses
    </div>

    <!-- Courses Grid -->
    <div class="courses-grid" id="coursesGrid">
        @forelse($modules as $module)
            <div class="course-card" data-title="{{ strtolower($module->title) }}"
                data-lessons="{{ $module->lessons->count() }}" data-labs="{{ $module->labs->count() }}">

                <div class="course-card-image" @if($module->banner_image)
                    style="background: url('{{ Storage::disk('azure')->url($module->banner_image) }}') center/cover no-repeat;"
                @endif>
                    <div class="course-card-badges">
                        <span class="badge badge-lessons">{{ $module->lessons->count() }} Lessons</span>
                        @if($module->labs->count() > 0)
                            <span class="badge badge-labs">{{ $module->labs->count() }} Labs</span>
                        @endif
                    </div>
                    @unless($module->banner_image)
                        <span class="course-card-icon">
                            @if($module->category == 'Cloud Engineer')
                                ☁️
                            @elseif($module->category == 'DevOps Engineer')
                                🔄
                            @elseif($module->category == 'Security Analyst')
                                🔒
                            @elseif($module->category == 'Platform Engineer')
                                🏗️
                            @else
                                📚
                            @endif
                        </span>
                    @endunless
                </div>

                <div class="course-card-body">
                    {{-- Subcategory tags (technologies from lessons) --}}
                    @php
                        $techs = $module->lessons->pluck('subcategory')->filter()->unique()->values();
                    @endphp
                    @if($techs->count() > 0)
                        <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 0.75rem;">
                            @foreach($techs as $tech)
                                <span class="badge badge-tech">{{ $tech }}</span>
                            @endforeach
                        </div>
                    @endif

                    <h3 class="course-title">{{ $module->title }}</h3>

                    {{-- Level and Category row --}}
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                        @if($module->level)
                            <span class="badge"
                                style="background: {{ $module->level === 'Beginner' ? 'rgba(34,197,94,0.2)' : ($module->level === 'Intermediate' ? 'rgba(251,191,36,0.2)' : 'rgba(239,68,68,0.2)') }}; color: {{ $module->level === 'Beginner' ? '#22c55e' : ($module->level === 'Intermediate' ? '#fbbf24' : '#ef4444') }};">{{ strtoupper($module->level) }}</span>
                        @endif
                        @if($module->category)
                            <span class="badge badge-tech">{{ $module->category }}</span>
                        @endif
                    </div>

                    <p class="course-description">{{ Str::limit($module->description, 120) }}</p>

                    <div class="course-meta">
                        <span>📚 {{ $module->lessons->count() }} lessons</span>
                        <span>🧪 {{ $module->labs->count() }} labs</span>
                        <span>⏱️
                            ~{{ round(($module->lessons->count() * 15 + $module->labs->sum('estimated_minutes')) / 60, 1) }}h</span>
                    </div>
                </div>

                <div class="course-card-footer">
                    <a href="{{ route('courses.show', $module->slug) }}">
                        View Course →
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">📚</div>
                <h3>No courses found</h3>
                <p>Try adjusting your filters or check back later for new content</p>
            </div>
        @endforelse
    </div>

    <script>
        function filterCourses() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const sortValue = document.getElementById('sortFilter').value;
            const cards = Array.from(document.querySelectorAll('.course-card'));
            const grid = document.getElementById('coursesGrid');

            // Filter
            let visibleCount = 0;
            cards.forEach(card => {
                const title = card.dataset.title;
                const matches = title.includes(searchValue);
                card.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            });

            // Sort visible cards
            const visibleCards = cards.filter(c => c.style.display !== 'none');
            visibleCards.sort((a, b) => {
                if (sortValue === 'title') {
                    return a.dataset.title.localeCompare(b.dataset.title);
                } else if (sortValue === 'lessons') {
                    return parseInt(b.dataset.lessons) - parseInt(a.dataset.lessons);
                } else if (sortValue === 'labs') {
                    return parseInt(b.dataset.labs) - parseInt(a.dataset.labs);
                }
                return 0;
            });

            // Reorder in DOM
            visibleCards.forEach(card => grid.appendChild(card));

            // Update count
            document.getElementById('resultsCount').textContent =
                `Showing ${visibleCount} course${visibleCount === 1 ? '' : 's'}`;
        }

        function applyFilters() {
            const tech = document.getElementById('techFilter').value;
            const category = document.getElementById('categoryFilter').value;
            const params = new URLSearchParams();
            if (tech) params.set('tech', tech);
            if (category) params.set('category', category);
            const qs = params.toString();
            window.location.href = '{{ route("courses.index") }}' + (qs ? '?' + qs : '');
        }
    </script>
@endsection