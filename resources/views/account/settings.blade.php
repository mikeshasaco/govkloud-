<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Account Settings - GovKloud</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --gk-navy: #0a0f1a;
            --gk-dark: #0f172a;
            --gk-slate: #1e293b;
            --gk-cyan: #D2B48C;
            --gk-teal: #C4A77D;
            --gk-purple: #8b5cf6;
            --gk-gold: #fbbf24;
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
            min-height: 100vh;
        }

        /* Navigation */
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: var(--gk-dark);
            border-bottom: 1px solid var(--border);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text);
            font-weight: 800;
            font-size: 1.25rem;
        }

        .nav-logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--gk-cyan);
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--gk-purple), var(--gk-cyan));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* Main Content */
        .main-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: var(--text-muted);
        }

        /* Settings Cards */
        .settings-section {
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            background: rgba(210, 180, 140, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .section-title h2 {
            font-size: 1rem;
            font-weight: 700;
        }

        .section-title p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .section-body {
            padding: 1.5rem;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text);
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: var(--gk-dark);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--gk-cyan);
            box-shadow: 0 0 0 3px rgba(210, 180, 140, 0.2);
        }

        .form-input:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .btn {
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            color: var(--gk-navy);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(210, 180, 140, 0.3);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            border-color: var(--gk-cyan);
            color: var(--gk-cyan);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        /* Plan Card */
        .plan-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            background: var(--gk-dark);
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        .plan-info h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .plan-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            color: var(--gk-navy);
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-left: 0.5rem;
        }

        .plan-badge.pro {
            background: linear-gradient(135deg, var(--gk-gold), #f59e0b);
        }

        .plan-features {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .plan-feature {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .plan-feature span {
            color: var(--gk-cyan);
            margin-right: 0.25rem;
        }

        /* Payment Method Card */
        .payment-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--gk-dark);
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        .card-icon {
            width: 50px;
            height: 32px;
            background: linear-gradient(135deg, #1a1f71, #00579f);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .card-info {
            flex: 1;
        }

        .card-info h4 {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .card-info p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
        }

        .empty-state-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Success Message */
        .success-message {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: none;
        }

        .success-message.show {
            display: block;
        }

        /* Logout Button */
        .logout-section {
            text-align: center;
            padding: 2rem;
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
            <a href="{{ route('courses.index') }}">Courses</a>
            <a href="{{ route('account.settings') }}" class="active">Account</a>
        </div>
        <div class="nav-user">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-container">
        <div class="page-header">
            <h1>Account Settings</h1>
            <p>Manage your account information and preferences</p>
        </div>

        <div id="successMessage" class="success-message">
            Changes saved successfully!
        </div>

        <!-- Email Section -->
        <section class="settings-section">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-icon">📧</div>
                    <div>
                        <h2>Email Address</h2>
                        <p>Your primary email for notifications</p>
                    </div>
                </div>
            </div>
            <div class="section-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" value="{{ Auth::user()->email }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Email</button>
                </form>
            </div>
        </section>

        <!-- Password Section -->
        <section class="settings-section">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-icon">🔒</div>
                    <div>
                        <h2>Password</h2>
                        <p>Keep your account secure</p>
                    </div>
                </div>
            </div>
            <div class="section-body">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-input"
                            placeholder="Enter current password" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-input" placeholder="New password"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-input"
                                placeholder="Confirm password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </section>

        <!-- Subscription Plan Section -->
        <section class="settings-section">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-icon">⭐</div>
                    <div>
                        <h2>Subscription Plan</h2>
                        <p>Manage your subscription</p>
                    </div>
                </div>
            </div>
            <div class="section-body">
                <div class="plan-card">
                    <div class="plan-info">
                        <h3>
                            @if(Auth::user()->subscribed())
                                {{ Auth::user()->getPlanName() }} Plan
                                <span class="plan-badge {{ Auth::user()->isPro() ? 'pro' : '' }}">Current</span>
                            @else
                                Free Plan
                                <span class="plan-badge">Current</span>
                            @endif
                        </h3>
                        <div class="plan-features">
                            @if(Auth::user()->subscribed())
                                <span class="plan-feature"><span>✓</span> Full Access</span>
                                <span class="plan-feature"><span>✓</span> All Labs</span>
                                @if(Auth::user()->onTrial())
                                    <span class="plan-feature" style="color: var(--gk-gold);">
                                        <span>⏱️</span> Trial until {{ Auth::user()->trialEndsAt()->format('M j, Y') }}
                                    </span>
                                @endif
                            @else
                                <span class="plan-feature"><span>✓</span> Basic courses</span>
                                <span class="plan-feature"><span>✓</span> Community support</span>
                            @endif
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        @if(Auth::user()->subscribed())
                            <a href="{{ route('billing') }}" class="btn btn-outline">Manage Billing</a>
                        @else
                            <a href="{{ route('pricing') }}" class="btn btn-primary">View Plans</a>
                        @endif
                    </div>
                </div>

                @unless(Auth::user()->subscribed())
                <div style="margin-top: 1rem; padding: 1rem; background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.3); border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.25rem;">🚀</span>
                        <div>
                            <div style="font-weight: 600; color: var(--gk-gold);">Unlock Full Access</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Unlimited labs, all courses, priority support, certificates</div>
                        </div>
                    </div>
                </div>
                @endunless
            </div>
        </section>

        <!-- Danger Zone -->
        <section class="settings-section" style="border-color: rgba(239, 68, 68, 0.3);">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-icon" style="background: rgba(239, 68, 68, 0.1);">⚠️</div>
                    <div>
                        <h2 style="color: #ef4444;">Danger Zone</h2>
                        <p>Irreversible actions</p>
                    </div>
                </div>
            </div>
            <div class="section-body">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>Delete Account</strong>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Permanently delete your account and all
                            data</p>
                    </div>
                    <button class="btn btn-danger">Delete Account</button>
                </div>
            </div>
        </section>

        <!-- Logout -->
        <div class="logout-section">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline">
                    Sign Out
                </button>
            </form>
        </div>
    </main>
</body>

</html>