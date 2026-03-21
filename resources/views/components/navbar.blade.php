@php
    $navModules = \App\Models\Module::published()->ordered()->withCount('lessons')->get();
    $navSubcategories = \App\Models\Lesson::selectRaw('subcategory, COUNT(*) as count')
        ->whereNotNull('subcategory')
        ->whereHas('module', fn($q) => $q->published())
        ->groupBy('subcategory')
        ->orderBy('subcategory')
        ->get();

    // Group modules by category with count
    $navCategories = \App\Models\Module::published()
        ->selectRaw('category, COUNT(*) as count')
        ->whereNotNull('category')
        ->groupBy('category')
        ->orderBy('category')
        ->get();
@endphp

<nav class="nav">
    <a href="/" class="nav-logo">
        <img src="/images/govkloud-logo.png" alt="GovKloud" class="nav-logo-icon">
        <span>GovKloud</span>
    </a>
    <div class="nav-links">
        <!-- Courses Mega-Menu Dropdown -->
        <div class="nav-dropdown nav-dropdown--mega">
            <button class="nav-dropdown-trigger">
                Courses
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <div class="nav-dropdown-menu mega-menu">
                <!-- Left Column: By Career Track -->
                <div class="mega-menu-column">
                    <div class="mega-menu-header">By Career Track</div>
                    @forelse($navCategories as $cat)
                        <a href="{{ route('courses.index') }}?category={{ urlencode($cat->category) }}"
                            class="dropdown-item">
                            <div class="dropdown-item-info">
                                <span class="dropdown-item-title">{{ $cat->category }}</span>
                            </div>
                            <span class="dropdown-item-count">{{ $cat->count }}
                                {{ Str::plural('Course', $cat->count) }}</span>
                        </a>
                    @empty
                        <span class="dropdown-item"><span class="dropdown-item-title" style="color: var(--text-muted);">No
                                career tracks yet</span></span>
                    @endforelse
                </div>

                <!-- Right Column: By Technology -->
                <div class="mega-menu-column">
                    <div class="mega-menu-header">By Technology</div>
                    @forelse($navSubcategories as $sub)
                        <a href="{{ route('courses.index') }}?tech={{ urlencode($sub->subcategory) }}"
                            class="dropdown-item">
                            <div class="dropdown-item-info">
                                <span class="dropdown-item-title">{{ $sub->subcategory }}</span>
                            </div>
                            <span class="dropdown-item-count">{{ $sub->count }}
                                {{ Str::plural('Lesson', $sub->count) }}</span>
                        </a>
                    @empty
                        <span class="dropdown-item"><span class="dropdown-item-title" style="color: var(--text-muted);">No
                                technologies yet</span></span>
                    @endforelse
                </div>

                <!-- Footer -->
                <div class="mega-menu-footer">
                    <a href="{{ route('courses.index') }}" class="mega-menu-footer-link">
                        Explore All Courses →
                    </a>
                </div>
            </div>
        </div>

        <a href="{{ route('career') }}">Career Paths</a>

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