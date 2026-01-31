@extends('layouts.govkloud')

@section('title', 'Pricing - GovKloud')

@section('content')
    <style>
        .pricing-hero {
            background: linear-gradient(135deg, var(--gk-navy) 0%, #1e3a5f 100%);
            padding: 4rem 2rem;
            text-align: center;
        }

        .pricing-hero h1 {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .pricing-hero p {
            color: var(--gk-cyan);
            font-size: 1.1rem;
        }

        .pricing-container {
            max-width: 1000px;
            margin: -3rem auto 4rem;
            padding: 0 1.5rem;
        }

        .billing-toggle {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            background: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            display: inline-flex;
        }

        .toggle-wrapper {
            text-align: center;
        }

        .toggle-label {
            font-weight: 600;
            color: var(--gk-navy);
            cursor: pointer;
        }

        .toggle-label.active {
            color: var(--gk-cyan);
        }

        .toggle-switch {
            width: 50px;
            height: 26px;
            background: var(--gk-cyan);
            border-radius: 13px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s;
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            width: 22px;
            height: 22px;
            background: white;
            border-radius: 50%;
            top: 2px;
            left: 2px;
            transition: all 0.3s;
        }

        .toggle-switch.yearly::after {
            left: 26px;
        }

        .save-badge {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .pricing-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .pricing-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .pricing-card.featured {
            border: 2px solid var(--gk-cyan);
        }

        .pricing-card.featured::before {
            content: 'Most Popular';
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            color: var(--gk-navy);
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .plan-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gk-navy);
            margin-bottom: 0.5rem;
        }

        .plan-price {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
            margin-bottom: 0.5rem;
        }

        .price-amount {
            font-size: 3rem;
            font-weight: 800;
            color: var(--gk-navy);
        }

        .price-period {
            color: #64748b;
            font-size: 1rem;
        }

        .price-yearly {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 0 0 2rem;
        }

        .features-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
            color: #475569;
        }

        .features-list li:last-child {
            border-bottom: none;
        }

        .features-list .check {
            color: #10b981;
            font-size: 1.1rem;
        }

        .subscribe-btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .subscribe-btn.primary {
            background: linear-gradient(135deg, var(--gk-cyan), var(--gk-teal));
            color: var(--gk-navy);
        }

        .subscribe-btn.secondary {
            background: var(--gk-navy);
            color: white;
        }

        .subscribe-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .trial-note {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #64748b;
        }

        .guarantee {
            text-align: center;
            padding: 2rem;
            color: #64748b;
            font-size: 0.9rem;
        }

        .guarantee strong {
            color: var(--gk-navy);
        }
    </style>

    <div class="pricing-hero">
        <h1>Simple, Transparent Pricing</h1>
        <p>Start learning Kubernetes hands-on today</p>
    </div>

    <div class="pricing-container">
        <div class="toggle-wrapper">
            <div class="billing-toggle">
                <span class="toggle-label active" id="monthlyLabel">Monthly</span>
                <div class="toggle-switch" id="billingToggle" onclick="toggleBilling()"></div>
                <span class="toggle-label" id="yearlyLabel">Yearly</span>
                <span class="save-badge">Save 29%</span>
            </div>
        </div>

        <div class="pricing-cards">
            <!-- Standard Plan -->
            <div class="pricing-card">
                <div class="plan-name">{{ $plans['standard']['name'] }}</div>
                <div class="plan-price">
                    <span class="price-amount" id="standardPrice">$29</span>
                    <span class="price-period" id="standardPeriod">/month</span>
                </div>
                <div class="price-yearly" id="standardYearly">Billed monthly</div>

                <ul class="features-list">
                    @foreach($plans['standard']['features'] as $feature)
                        <li><span class="check">✓</span> {{ $feature }}</li>
                    @endforeach
                </ul>

                <form action="{{ route('subscribe', ['plan' => 'standard', 'interval' => 'monthly']) }}" method="POST"
                    id="standardForm">
                    @csrf
                    <button type="submit" class="subscribe-btn secondary">
                        Start Free Trial
                    </button>
                </form>
                <div class="trial-note">{{ $trialDays }}-day free trial • Cancel anytime</div>
            </div>

            <!-- Pro Plan -->
            <div class="pricing-card featured">
                <div class="plan-name">{{ $plans['pro']['name'] }}</div>
                <div class="plan-price">
                    <span class="price-amount" id="proPrice">$49</span>
                    <span class="price-period" id="proPeriod">/month</span>
                </div>
                <div class="price-yearly" id="proYearly">Billed monthly</div>

                <ul class="features-list">
                    @foreach($plans['pro']['features'] as $feature)
                        <li><span class="check">✓</span> {{ $feature }}</li>
                    @endforeach
                </ul>

                <form action="{{ route('subscribe', ['plan' => 'pro', 'interval' => 'monthly']) }}" method="POST"
                    id="proForm">
                    @csrf
                    <button type="submit" class="subscribe-btn primary">
                        Start Free Trial
                    </button>
                </form>
                <div class="trial-note">{{ $trialDays }}-day free trial • Cancel anytime</div>
            </div>
        </div>

        <div class="guarantee">
            <strong>💳 Secure checkout powered by Stripe</strong><br>
            Questions? Contact us at support@govkloud.com
        </div>
    </div>

    <script>
        let isYearly = false;

        function toggleBilling() {
            isYearly = !isYearly;

            const toggle = document.getElementById('billingToggle');
            const monthlyLabel = document.getElementById('monthlyLabel');
            const yearlyLabel = document.getElementById('yearlyLabel');

            toggle.classList.toggle('yearly', isYearly);
            monthlyLabel.classList.toggle('active', !isYearly);
            yearlyLabel.classList.toggle('active', isYearly);

            // Update prices
            if (isYearly) {
                document.getElementById('standardPrice').textContent = '$249';
                document.getElementById('standardPeriod').textContent = '/year';
                document.getElementById('standardYearly').textContent = 'Save $99 per year';

                document.getElementById('proPrice').textContent = '$399';
                document.getElementById('proPeriod').textContent = '/year';
                document.getElementById('proYearly').textContent = 'Save $189 per year';

                document.getElementById('standardForm').action = "{{ route('subscribe', ['plan' => 'standard', 'interval' => 'yearly']) }}";
                document.getElementById('proForm').action = "{{ route('subscribe', ['plan' => 'pro', 'interval' => 'yearly']) }}";
            } else {
                document.getElementById('standardPrice').textContent = '$29';
                document.getElementById('standardPeriod').textContent = '/month';
                document.getElementById('standardYearly').textContent = 'Billed monthly';

                document.getElementById('proPrice').textContent = '$49';
                document.getElementById('proPeriod').textContent = '/month';
                document.getElementById('proYearly').textContent = 'Billed monthly';

                document.getElementById('standardForm').action = "{{ route('subscribe', ['plan' => 'standard', 'interval' => 'monthly']) }}";
                document.getElementById('proForm').action = "{{ route('subscribe', ['plan' => 'pro', 'interval' => 'yearly']) }}";
            }
        }
    </script>
@endsection