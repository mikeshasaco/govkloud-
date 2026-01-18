<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - GovKloud</title>
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

        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        .checkbox-label input {
            width: 18px;
            height: 18px;
            accent-color: var(--gk-cyan);
        }

        .forgot-link {
            color: var(--gk-cyan);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
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

        .success-message {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
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
            <h1>Welcome Back</h1>
            <p>Sign in to continue your learning journey</p>
        </div>

        <div class="auth-card">
            @if (session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" 
                        placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" 
                        placeholder="••••••••" required>
                </div>

                <div class="form-row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-submit">
                    Sign In
                </button>
            </form>

            <div class="divider">
                <span>or continue with</span>
            </div>

            <div class="social-buttons">
                <button type="button" class="btn-social">
                    <span>🔷</span> Google
                </button>
                <button type="button" class="btn-social">
                    <span>🐙</span> GitHub
                </button>
            </div>
        </div>

        <div class="auth-footer">
            Don't have an account? <a href="{{ route('register') }}">Sign up free</a>
        </div>
    </div>
</body>
</html>