<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GovKloud Labs')</title>
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #10b981;
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
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
            background: var(--bg-dark);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Navigation */
        .navbar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--text);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-name {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        /* Main content */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Cards */
        .card {
            background: var(--bg-card);
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

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
        }

        .btn-secondary {
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--secondary), #059669);
            color: white;
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
            background: rgba(99, 102, 241, 0.2);
            color: var(--primary);
        }

        .badge-secondary {
            background: rgba(16, 185, 129, 0.2);
            color: var(--secondary);
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

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar">
        <div class="navbar-content">
            <a href="/" class="logo">GovKloud</a>
            <ul class="nav-links">
                <li><a href="{{ route('modules.index') }}">Modules</a></li>
                @auth
                    <li class="user-menu">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit"
                                style="background: none; border: none; color: var(--text-muted); cursor: pointer;">
                                Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}" class="btn btn-primary" style="padding: 0.5rem 1rem;">Sign Up</a>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>