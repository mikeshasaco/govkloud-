<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GovKloud Labs')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --gk-navy: #0a0f1a;
            --gk-dark: #0f172a;
            --gk-slate: #1e293b;
            --gk-cyan: #D2B48C;
            --gk-teal: #C4A77D;
            --gk-gold: #fbbf24;
            --gk-purple: #8b5cf6;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            --radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--gk-navy);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Navigation */
        .nav {
            position: sticky;
            top: 0.75rem;
            z-index: 100;
            padding: 0.75rem 1.5rem;
            margin: 0.75rem 2rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: transparent;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(210, 180, 140, 0.12);
            border-radius: 16px;
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
            gap: 2rem;
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

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--gk-navy);
        }

        /* Main content */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Cards */
        .card {
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-primary {
            background: rgba(210, 180, 140, 0.2);
            color: var(--gk-cyan);
        }

        .badge-secondary {
            background: rgba(196, 167, 125, 0.2);
            color: var(--gk-teal);
        }

        /* Typography */
        h1,
        h2,
        h3 {
            font-weight: 700;
            line-height: 1.3;
        }

        h1 {
            font-size: 2.5rem;
        }

        h2 {
            font-size: 1.75rem;
        }

        h3 {
            font-size: 1.25rem;
        }

        .text-muted {
            color: var(--text-muted);
        }

        /* Utilities */
        .mt-1 {
            margin-top: 0.5rem;
        }

        .mt-2 {
            margin-top: 1rem;
        }

        .mt-3 {
            margin-top: 1.5rem;
        }

        .mt-4 {
            margin-top: 2rem;
        }

        .mb-2 {
            margin-bottom: 1rem;
        }

        .mb-3 {
            margin-bottom: 1.5rem;
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .gap-2 {
            gap: 1rem;
        }
    </style>
    @stack('styles')
</head>

<body>
    @include('components.navbar')

    <main class="container">
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>