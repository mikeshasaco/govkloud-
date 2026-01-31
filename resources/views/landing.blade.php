<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="GovKloud - Enterprise Cloud & DevOps Training Platform. Trusted by Cisco, Lockheed Martin, and the US Army.">
    <title>GovKloud - Enterprise Cloud Training Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
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

        /* Navigation */
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
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
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

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8rem 2rem 4rem;
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
                radial-gradient(ellipse at 30% 20%, rgba(210, 180, 140, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 60%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(196, 167, 125, 0.1) 0%, transparent 40%);
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
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
        }

        .hero-badge-dot {
            width: 8px;
            height: 8px;
            background: var(--gk-cyan);
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
                transform: scale(1.2);
            }
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero h1 span {
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            -webkit-background-clip: text;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 2.5rem;
            line-height: 1.8;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 3rem;
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

        /* Social Proof */
        .hero-social-proof {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .avatars {
            display: flex;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid var(--gk-navy);
            margin-left: -12px;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .avatar:first-child {
            margin-left: 0;
        }

        .social-proof-text {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .social-proof-text strong {
            color: var(--text);
        }

        /* Demo Preview */
        .demo-preview {
            position: relative;
            max-width: 1000px;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(210, 180, 140, 0.2),
                0 25px 80px rgba(0, 0, 0, 0.5),
                0 0 100px rgba(210, 180, 140, 0.1);
        }

        .demo-preview img {
            width: 100%;
            display: block;
        }

        .demo-preview-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, transparent 60%, var(--gk-navy) 100%);
            pointer-events: none;
        }

        /* Trusted By Section */
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

        /* Features Section */
        .features {
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

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        @media (max-width: 900px) {
            .features-grid {
                grid-template-columns: 1fr;
            }
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

        /* Stats Section */
        .stats {
            padding: 4rem 2rem;
            background: linear-gradient(135deg, rgba(210, 180, 140, 0.05), rgba(139, 92, 246, 0.05));
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .stats-grid {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .stat-item h4 {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .stat-item p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* CTA Section */
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

        /* Footer */
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

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .hero-cta {
                flex-direction: column;
            }

            .logos-grid {
                gap: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="nav">
        <a href="/" class="nav-logo">
            <div class="nav-logo-icon">☁️</div>
            <span>GovKloud</span>
        </a>
        <div class="nav-links">
            <!-- Courses Dropdown -->
            <div class="nav-dropdown">
                <button class="nav-dropdown-trigger">
                    Courses
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="nav-dropdown-menu">
                    <div class="dropdown-header">Available Courses</div>
                    @forelse($modules ?? [] as $module)
                        <a href="{{ route('courses.show', $module->slug) }}" class="dropdown-item">
                            <div class="dropdown-item-info">
                                <span class="dropdown-item-title">{{ $module->title }}</span>
                                @if($module->category)
                                    <span class="dropdown-item-desc">{{ $module->category }}</span>
                                @endif
                            </div>
                            <span class="dropdown-item-count">{{ $module->lessons()->count() }} lessons</span>
                        </a>
                    @empty
                        <span class="dropdown-item">No courses yet</span>
                    @endforelse
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('courses.index') }}" class="dropdown-item">
                        <span class="dropdown-item-title">View All Courses →</span>
                    </a>
                </div>
            </div>

            <!-- Browse by Tech Dropdown -->
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
                    @forelse($subcategories ?? [] as $sub)
                        <a href="{{ route('courses.index') }}?tech={{ urlencode($sub->subcategory) }}"
                            class="dropdown-item">
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
                    <div
                        style="width: 32px; height: 32px; background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; color: var(--gk-navy);">
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
                <a href="{{ route('register') }}" class="btn btn-primary">Get Started Free</a>
            </div>
        @endauth
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="hero-badge-dot"></span>
                <span>Trusted by Government & Enterprise</span>
            </div>

            <h1>
                Build, Break, Fix,<br>
                <span>Learn DevOps</span>
            </h1>

            <p class="hero-subtitle">
                Hands-on cloud and Kubernetes training for engineers who build mission-critical infrastructure.
                Real environments. Real scenarios. Real skills.
            </p>

            <div class="hero-cta">
                <a href="{{ route('courses.index') }}" class="btn btn-primary btn-large">
                    🚀 Start Learning Free
                </a>
                <a href="#features" class="btn btn-outline btn-large">
                    See How It Works
                </a>
            </div>

            <div class="hero-social-proof">
                <div class="avatars">
                    <div class="avatar">👨‍💻</div>
                    <div class="avatar">👩‍💻</div>
                    <div class="avatar">🧑‍💻</div>
                    <div class="avatar">👨‍🔬</div>
                    <div class="avatar">👩‍🔬</div>
                </div>
                <span class="social-proof-text">
                    <strong>10,000+</strong> engineers trained
                </span>
            </div>
        </div>
    </section>

    <!-- Trusted By -->
    <section class="trusted-by">
        <div class="trusted-by-inner">
            <h3>Experience Working With Industry Leaders</h3>
            <div class="logos-grid">
                <div class="logo-item">
                    <div class="logo-icon">🔷</div>
                    <span class="logo-name">Cisco</span>
                    <span class="logo-role">Network Infrastructure</span>
                </div>
                <div class="logo-item">
                    <div class="logo-icon">✈️</div>
                    <span class="logo-name">Lockheed Martin</span>
                    <span class="logo-role">Defense & Aerospace</span>
                </div>
                <div class="logo-item">
                    <div class="logo-icon">⭐</div>
                    <span class="logo-name">US Army</span>
                    <span class="logo-role">Military Operations</span>
                </div>
                <div class="logo-item">
                    <div class="logo-icon">🏛️</div>
                    <span class="logo-name">Federal Agencies</span>
                    <span class="logo-role">Government Cloud</span>
                </div>
                <div class="logo-item">
                    <div class="logo-icon">🛡️</div>
                    <span class="logo-name">FedRAMP</span>
                    <span class="logo-role">Compliance Ready</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features" id="features">
        <div class="section-header">
            <h2>Why Engineers Choose GovKloud</h2>
            <p>Built for professionals who need to master cloud infrastructure in secure, isolated environments.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">☸️</div>
                <h3>Real Kubernetes Clusters</h3>
                <p>Every lab spins up a real virtual cluster. No simulators—you get actual kubectl access to practice
                    deployments, services, and more.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Isolated Environments</h3>
                <p>Each session runs in a secure, isolated namespace. Perfect for practicing without fear of breaking
                    production systems.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Instant Lab Access</h3>
                <p>No setup required. Click start and you're in a fully configured VS Code environment with terminal
                    access in under a minute.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📚</div>
                <h3>Structured Learning Paths</h3>
                <p>From Kubernetes basics to advanced Terraform, follow curated paths designed by industry veterans.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h3>Hands-On Challenges</h3>
                <p>Learn by doing. Each module includes practical challenges that test your skills in real scenarios.
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Progress Tracking</h3>
                <p>Track your learning journey with completion certificates and skill assessments.</p>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats">
        <div class="stats-grid">
            <div class="stat-item">
                <h4>50+</h4>
                <p>Hands-on Labs</p>
            </div>
            <div class="stat-item">
                <h4>10k+</h4>
                <p>Engineers Trained</p>
            </div>
            <div class="stat-item">
                <h4>100%</h4>
                <p>Cloud-Native</p>
            </div>
            <div class="stat-item">
                <h4>24/7</h4>
                <p>Lab Access</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-box">
            <h2>Ready to Master Cloud Infrastructure?</h2>
            <p>Start your free account today and get instant access to our Kubernetes fundamentals course.</p>
            <a href="{{ route('register') }}" class="btn btn-primary btn-large">
                🚀 Get Started Free
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>© {{ date('Y') }} GovKloud. Enterprise Cloud Training Platform.</p>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Contact</a>
        </div>
    </footer>
</body>

</html>