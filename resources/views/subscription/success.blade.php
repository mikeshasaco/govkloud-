@extends('layouts.govkloud')

@section('title', 'Welcome to GovKloud!')

@section('content')
    <style>
        .success-container {
            max-width: 600px;
            margin: 4rem auto;
            padding: 3rem;
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2.5rem;
        }

        .success-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gk-navy);
            margin-bottom: 1rem;
        }

        .success-message {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .success-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .success-btn {
            padding: 0.875rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .success-btn.primary {
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            color: var(--gk-navy);
        }

        .success-btn.secondary {
            background: #f1f5f9;
            color: var(--gk-navy);
        }

        .success-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .trial-info {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #047857;
        }
    </style>

    <div class="success-container">
        <div class="success-icon">✓</div>

        <h1 class="success-title">Welcome to GovKloud!</h1>

        <p class="success-message">
            Your subscription is now active. You have full access to all courses,
            lessons, and hands-on Kubernetes lab environments.
        </p>

        <div class="success-actions">
            <a href="{{ route('courses.index') }}" class="success-btn primary">
                🚀 Start Learning
            </a>
            <a href="{{ route('dashboard') }}" class="success-btn secondary">
                📊 Go to Dashboard
            </a>
        </div>

        @if(auth()->user()->onTrial())
            <div class="trial-info">
                🎉 Your {{ config('stripe-plans.trial_days') }}-day free trial has started!
                You won't be charged until {{ auth()->user()->trialEndsAt()->format('F j, Y') }}.
            </div>
        @endif
    </div>
@endsection