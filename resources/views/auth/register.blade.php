<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - GovKloud</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --gk-navy: #0a0f1a;
            --gk-dark: #0f172a;
            --gk-slate: #1e293b;
            --gk-cyan: #D2B48C;
            --gk-teal: #C4A77D;
            --gk-purple: #8b5cf6;
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 30%, rgba(210, 180, 140, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 70%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text);
            font-weight: 800;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
        }

        .auth-logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .auth-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .auth-card {
            background: var(--gk-slate);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--gk-cyan), var(--gk-purple));
        }

        .form-group {
            margin-bottom: 1.25rem;
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
            padding: 0.875rem 1rem;
            background: var(--gk-dark);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--gk-cyan);
            box-shadow: 0 0 0 3px rgba(210, 180, 140, 0.2);
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            color: var(--gk-navy);
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(210, 180, 140, 0.4);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .divider span {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .social-buttons {
            display: flex;
            gap: 0.75rem;
        }

        .btn-social {
            flex: 1;
            padding: 0.75rem;
            background: var(--gk-dark);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-social:hover {
            border-color: var(--gk-cyan);
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .auth-footer a {
            color: var(--gk-cyan);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .terms-text {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-align: center;
            margin-top: 1rem;
        }

        .terms-text a {
            color: var(--gk-cyan);
            text-decoration: none;
        }

        .terms-text a:hover {
            text-decoration: underline;
        }

        .features-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .feature-icon {
            color: var(--gk-cyan);
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-header">
            <a href="/" class="auth-logo">
                <div class="auth-logo-icon">☁️</div>
                <span>GovKloud</span>
            </a>
            <h1>Create Your Account</h1>
            <p>Start learning cloud & DevOps for free</p>
        </div>

        <div class="auth-card">
            @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="John Doe"
                        value="{{ old('name') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="johndoe"
                        value="{{ old('username') }}" required pattern="[a-z0-9][a-z0-9-]*[a-z0-9]" minlength="3"
                        maxlength="20" style="text-transform: lowercase;">
                    <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">
                        3-20 characters, lowercase letters, numbers & hyphens. This will be your lab workspace ID.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="you@example.com"
                        value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input"
                        placeholder="Create a strong password" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input"
                        placeholder="Confirm your password" required>
                </div>

                <button type="submit" class="btn-submit">
                    🚀 Create Free Account
                </button>

                <p class="terms-text">
                    By signing up, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>
                </p>
            </form>

            <div class="divider">
                <span>or sign up with</span>
            </div>

            <div class="social-buttons">
                <a href="{{ route('auth.google') }}" class="btn-social" style="flex: 1;">
                    <svg width="18" height="18" viewBox="0 0 24 24">
                        <path fill="#4285F4"
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" />
                        <path fill="#34A853"
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05"
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                        <path fill="#EA4335"
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                    </svg>
                    Sign up with Google
                </a>
            </div>

            <div class="features-list">
                <div class="feature-item">
                    <span class="feature-icon">✓</span>
                    <span>50+ hands-on labs</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">✓</span>
                    <span>Real Kubernetes environments</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">✓</span>
                    <span>Certificate on completion</span>
                </div>
            </div>
        </div>

        <div class="auth-footer">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>
</body>

</html>