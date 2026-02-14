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
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" value="{{ Auth::user()->email }}" disabled
                        style="opacity: 0.6; cursor: not-allowed;">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">Contact support to
                        change your email address.</p>
                </div>
            </div>
        </section>

        @if(!Auth::user()->google_id)
            <!-- Password Section (only for email/password users) -->
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
        @else
            <!-- Google Sign-In Info -->
            <section class="settings-section">
                <div class="section-header">
                    <div class="section-title">
                        <div class="section-icon">🔒</div>
                        <div>
                            <h2>Sign-In Method</h2>
                            <p>How you access your account</p>
                        </div>
                    </div>
                </div>
                <div class="section-body">
                    <div
                        style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: var(--gk-dark); border-radius: 8px; border: 1px solid var(--border);">
                        <svg width="20" height="20" viewBox="0 0 24 24">
                            <path fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" />
                            <path fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        <span style="color: var(--text);">Signed in with Google</span>
                    </div>
                </div>
            </section>
        @endif

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
                            @if(Auth::user()->onTrial())
                                {{ Auth::user()->getPlanName() }} Plan
                                <span class="plan-badge"
                                    style="background: rgba(251, 191, 36, 0.2); color: #fbbf24;">Trial</span>
                            @elseif(Auth::user()->subscribed())
                                {{ Auth::user()->getPlanName() }} Plan
                                <span class="plan-badge {{ Auth::user()->isPro() ? 'pro' : '' }}">Active</span>
                            @else
                                Free Plan
                                <span class="plan-badge">Current</span>
                            @endif
                        </h3>
                        <div class="plan-features">
                            @if(Auth::user()->onTrial())
                                @php
                                    $daysLeft = (int) now()->diffInDays(Auth::user()->trialEndsAt(), false);
                                    $totalTrialDays = 3;
                                    $pct = max(0, min(100, ($daysLeft / $totalTrialDays) * 100));
                                @endphp
                                <div style="width: 100%; margin-bottom: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                                        <span style="font-size: 0.85rem; color: #fbbf24; font-weight: 600;">
                                            ⏱️ {{ $daysLeft }} {{ Str::plural('day', $daysLeft) }} remaining
                                        </span>
                                        <span style="font-size: 0.8rem; color: var(--text-muted);">
                                            Ends {{ Auth::user()->trialEndsAt()->format('M j, Y') }}
                                        </span>
                                    </div>
                                    <div
                                        style="width: 100%; height: 6px; background: var(--gk-dark); border-radius: 3px; overflow: hidden;">
                                        <div
                                            style="width: {{ $pct }}%; height: 100%; background: linear-gradient(90deg, #fbbf24, #f59e0b); border-radius: 3px; transition: width 0.3s;">
                                        </div>
                                    </div>
                                </div>
                                <span class="plan-feature"><span>✓</span> Full Access</span>
                                <span class="plan-feature"><span>✓</span> All Labs</span>
                            @elseif(Auth::user()->subscribed())
                                <span class="plan-feature"><span>✓</span> Full Access</span>
                                <span class="plan-feature"><span>✓</span> All Labs</span>
                                <span class="plan-feature" style="color: #10b981;"><span>✓</span> Subscribed</span>
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
                    <div
                        style="margin-top: 1rem; padding: 1rem; background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.3); border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="font-size: 1.25rem;">🚀</span>
                            <div>
                                <div style="font-weight: 600; color: var(--gk-gold);">Unlock Full Access</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">Unlimited labs, all courses,
                                    priority support, certificates</div>
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