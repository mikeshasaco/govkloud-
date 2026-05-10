@extends('layouts.govkloud')

@section('title', 'Help Center - GovKloud')

@push('styles')
<style>
    .help-page {
        max-width: 860px;
        margin: 0 auto;
        padding: 2rem 1rem 4rem;
    }

    /* ========================================
       HERO
       ======================================== */
    .help-hero {
        text-align: center;
        margin-bottom: 3rem;
    }

    .help-hero-icon {
        width: 72px;
        height: 72px;
        background: linear-gradient(135deg, rgba(210, 180, 140, 0.15), rgba(139, 92, 246, 0.1));
        border: 1px solid rgba(210, 180, 140, 0.2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1.5rem;
        animation: float 4s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }

    .help-hero h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ffffff, var(--gk-cyan));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.75rem;
    }

    .help-hero p {
        font-size: 1.1rem;
        color: #94a3b8;
        max-width: 500px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* ========================================
       FORM CARD
       ======================================== */
    .help-form-card {
        background: var(--gk-slate);
        border: 1px solid rgba(210, 180, 140, 0.12);
        border-radius: 20px;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .help-form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--gk-cyan), var(--gk-purple), var(--gk-cyan));
        background-size: 200% auto;
        animation: shimmer 3s linear infinite;
    }

    @keyframes shimmer {
        0% { background-position: 0% center; }
        100% { background-position: 200% center; }
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 600px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        .help-hero h1 {
            font-size: 1.75rem;
        }
        .help-form-card {
            padding: 1.5rem;
        }
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        margin-bottom: 0.5rem;
    }

    .form-group label .required {
        color: #ef4444;
        margin-left: 2px;
    }

    .form-control {
        width: 100%;
        padding: 0.85rem 1rem;
        background: rgba(10, 15, 26, 0.6);
        border: 1px solid rgba(210, 180, 140, 0.15);
        border-radius: 10px;
        color: var(--text);
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        outline: none;
    }

    .form-control::placeholder {
        color: #475569;
    }

    .form-control:focus {
        border-color: var(--gk-cyan);
        box-shadow: 0 0 0 3px rgba(210, 180, 140, 0.1);
        background: rgba(10, 15, 26, 0.8);
    }

    .form-control:hover:not(:focus) {
        border-color: rgba(210, 180, 140, 0.3);
    }

    select.form-control {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8.825a.5.5 0 01-.354-.146l-4-4a.5.5 0 01.708-.708L6 7.617l3.646-3.646a.5.5 0 01.708.708l-4 4A.5.5 0 016 8.825z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
    }

    textarea.form-control {
        min-height: 160px;
        resize: vertical;
        line-height: 1.6;
    }

    .form-error {
        color: #ef4444;
        font-size: 0.78rem;
        margin-top: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* ========================================
       SUBMIT BUTTON
       ======================================== */
    .help-submit-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
        color: var(--gk-navy);
        font-weight: 700;
        font-size: 1rem;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.5rem;
    }

    .help-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(210, 180, 140, 0.35);
    }

    .help-submit-btn:active {
        transform: translateY(0);
    }

    .help-submit-btn .btn-icon {
        font-size: 1.1rem;
        transition: transform 0.3s ease;
    }

    .help-submit-btn:hover .btn-icon {
        transform: translateX(3px);
    }

    /* ========================================
       INFO CARDS
       ======================================== */
    .help-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        margin-top: 2.5rem;
    }

    @media (max-width: 700px) {
        .help-info-grid {
            grid-template-columns: 1fr;
        }
    }

    .help-info-card {
        background: rgba(30, 41, 59, 0.5);
        border: 1px solid rgba(210, 180, 140, 0.08);
        border-radius: 14px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .help-info-card:hover {
        border-color: rgba(210, 180, 140, 0.2);
        transform: translateY(-3px);
    }

    .help-info-icon {
        font-size: 1.75rem;
        margin-bottom: 0.75rem;
    }

    .help-info-card h3 {
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 0.4rem;
        color: var(--text);
    }

    .help-info-card p {
        font-size: 0.8rem;
        color: #64748b;
        line-height: 1.5;
    }
</style>
@endpush

@section('content')
<div class="help-page">
    <!-- Hero -->
    <div class="help-hero">
        <div class="help-hero-icon">🎫</div>
        <h1>Help Center</h1>
        <p>Need help? Fill out the form below and our team will get back to you as soon as possible.</p>
    </div>

    <!-- Form Card -->
    <div class="help-form-card">
        <form method="POST" action="{{ route('help-center.submit') }}" id="helpForm">
            @csrf

            <div class="form-row">
                <!-- First Name -->
                <div class="form-group">
                    <label for="first_name">First Name <span class="required">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="first_name"
                           name="first_name"
                           placeholder="Your first name"
                           value="{{ old('first_name', auth()->user()?->name ? explode(' ', auth()->user()->name)[0] : '') }}"
                           required>
                    @error('first_name')
                        <div class="form-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email"
                           class="form-control"
                           id="email"
                           name="email"
                           placeholder="you@example.com"
                           value="{{ old('email', auth()->user()?->email ?? '') }}"
                           required>
                    @error('email')
                        <div class="form-error">⚠ {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Issue Type -->
            <div class="form-group">
                <label for="issue">Issue Type <span class="required">*</span></label>
                <select class="form-control" id="issue" name="issue" required>
                    <option value="" disabled {{ old('issue') ? '' : 'selected' }}>Select an issue type...</option>
                    <option value="Account & Login" {{ old('issue') == 'Account & Login' ? 'selected' : '' }}>Account & Login</option>
                    <option value="Billing & Subscription" {{ old('issue') == 'Billing & Subscription' ? 'selected' : '' }}>Billing & Subscription</option>
                    <option value="Course Content" {{ old('issue') == 'Course Content' ? 'selected' : '' }}>Course Content</option>
                    <option value="Lab Environment" {{ old('issue') == 'Lab Environment' ? 'selected' : '' }}>Lab Environment</option>
                    <option value="Bug Report" {{ old('issue') == 'Bug Report' ? 'selected' : '' }}>Bug Report</option>
                    <option value="Feature Request" {{ old('issue') == 'Feature Request' ? 'selected' : '' }}>Feature Request</option>
                    <option value="Other" {{ old('issue') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('issue')
                    <div class="form-error">⚠ {{ $message }}</div>
                @enderror
            </div>

            <!-- Details -->
            <div class="form-group">
                <label for="details">Details <span class="required">*</span></label>
                <textarea class="form-control"
                          id="details"
                          name="details"
                          placeholder="Please describe your issue in as much detail as possible..."
                          required>{{ old('details') }}</textarea>
                @error('details')
                    <div class="form-error">⚠ {{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="help-submit-btn">
                Send Support Request
                <span class="btn-icon">→</span>
            </button>
        </form>
    </div>

    <!-- Info Cards -->
    <div class="help-info-grid">
        <div class="help-info-card">
            <div class="help-info-icon">⚡</div>
            <h3>Fast Response</h3>
            <p>We typically respond within 24 hours during business days.</p>
        </div>
        <div class="help-info-card">
            <div class="help-info-icon">🔒</div>
            <h3>Secure & Private</h3>
            <p>Your information is kept confidential and never shared.</p>
        </div>
        <div class="help-info-card">
            <div class="help-info-icon">💬</div>
            <h3>Expert Support</h3>
            <p>Our team of cloud experts is ready to help you succeed.</p>
        </div>
    </div>
</div>
@endsection
