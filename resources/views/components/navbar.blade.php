@php
    $navModules = \App\Models\Module::published()->ordered()->withCount('lessons')->get();
    $navSubcategories = \App\Models\Lesson::selectRaw('subcategory, COUNT(*) as count')
        ->whereNotNull('subcategory')
        ->whereHas('module', fn($q) => $q->published())
        ->groupBy('subcategory')
        ->orderBy('subcategory')
        ->get();
@endphp

<nav class="nav">
    <a href="/" class="nav-logo">
        <div class="nav-logo-icon">☁️</div>
        <span>GovKloud</span>
    </a>
    <div class="nav-links">
        <!-- Modules Dropdown -->
        <div class="nav-dropdown">
            <button class="nav-dropdown-trigger">
                Modules
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <div class="nav-dropdown-menu">
                <div class="dropdown-header">Available Courses</div>
                @forelse($navModules as $module)
                    <a href="{{ route('courses.show', $module->slug) }}" class="dropdown-item">
                        <div class="dropdown-item-info">
                            <span class="dropdown-item-title">{{ $module->title }}</span>
                            @if($module->category)
                                <span class="dropdown-item-desc">{{ $module->category }}</span>
                            @endif
                        </div>
                        <span class="dropdown-item-count">{{ $module->lessons_count ?? $module->lessons()->count() }}
                            lessons</span>
                    </a>
                @empty
                    <span class="dropdown-item">No modules yet</span>
                @endforelse
                <div class="dropdown-divider"></div>
                <a href="{{ route('courses.index') }}" class="dropdown-item">
                    <span class="dropdown-item-title">View All Courses →</span>
                </a>
            </div>
        </div>

        <!-- Courses by Tech Dropdown -->
        <div class="nav-dropdown">
            <button class="nav-dropdown-trigger">
                Browse by Tech
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <div class="nav-dropdown-menu">
                <a href="{{ route('courses.index') }}" class="dropdown-item"
                    style="background: rgba(210, 180, 140, 0.1); border-bottom: 1px solid var(--border); margin-bottom: 0.5rem;">
                    <span class="dropdown-item-title">🔍 View All Courses</span>
                </a>
                <div class="dropdown-header">Filter by Technology</div>
                @forelse($navSubcategories as $sub)
                    <a href="{{ route('courses.index') }}?tech={{ urlencode($sub->subcategory) }}" class="dropdown-item">
                        <span class="dropdown-item-title">{{ $sub->subcategory }}</span>
                        <span class="dropdown-item-count">{{ $sub->count }} lessons</span>
                    </a>
                @empty
                    <span class="dropdown-item">No technologies yet</span>
                @endforelse
            </div>
        </div>

        @auth
            <a href="{{ route('pricing') }}">Upgrade</a>
        @else
            <a href="{{ route('pricing') }}">Pricing</a>
        @endauth
    </div>

    @auth
        <!-- Logged In User Menu -->
        <div class="nav-dropdown">
            <button class="nav-dropdown-trigger" style="display: flex; align-items: center; gap: 0.5rem;">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <span>{{ Auth::user()->name }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <div class="nav-dropdown-menu" style="right: 0; left: auto; min-width: 200px;">
                @if(Auth::user()->is_admin)
                    <a href="/admin" class="dropdown-item"
                        style="background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.3); margin-bottom: 0.5rem;">
                        <span class="dropdown-item-title" style="color: #a78bfa;">🛡️ Admin Panel</span>
                    </a>
                @endif
                <a href="{{ route('account.settings') }}" class="dropdown-item">
                    <span class="dropdown-item-title">⚙️ Account Settings</span>
                </a>
                <a href="{{ route('my-courses') }}" class="dropdown-item">
                    <span class="dropdown-item-title">📚 My Courses</span>
                </a>
                <a href="{{ route('billing') }}" class="dropdown-item">
                    <span class="dropdown-item-title">💳 Billing</span>
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item"
                        style="width: 100%; background: none; border: none; cursor: pointer; text-align: left;">
                        <span class="dropdown-item-title" style="color: #ef4444;">🚪 Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    @else
        <!-- Guest CTA Buttons -->
        <div class="nav-cta">
            <a href="{{ route('login') }}" class="btn btn-ghost">Sign In</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Sign Up</a>
        </div>
    @endauth
</nav>