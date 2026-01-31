<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Show the pricing page
     */
    public function index()
    {
        $plans = config('stripe-plans.plans');
        $trialDays = config('stripe-plans.trial_days');

        return view('pricing', compact('plans', 'trialDays'));
    }

    /**
     * Create a checkout session and redirect to Stripe
     */
    public function checkout(Request $request, string $plan, string $interval)
    {
        $user = $request->user();

        // Validate plan and interval
        $plans = config('stripe-plans.plans');
        if (!isset($plans[$plan]) || !in_array($interval, ['monthly', 'yearly'])) {
            abort(400, 'Invalid plan or interval');
        }

        $priceId = $plans[$plan][$interval]['price_id'];

        if (!$priceId) {
            abort(500, 'Stripe price not configured. Please set up your Stripe prices.');
        }

        $trialDays = config('stripe-plans.trial_days');

        return $user->newSubscription(config('stripe-plans.subscription_name'), $priceId)
            ->trialDays($trialDays)
            ->checkout([
                'success_url' => route('subscription.success'),
                'cancel_url' => route('pricing'),
            ]);
    }

    /**
     * Handle successful subscription
     */
    public function success()
    {
        return view('subscription.success');
    }

    /**
     * Redirect to Stripe Customer Portal
     */
    public function portal(Request $request)
    {
        $user = $request->user();

        // If user doesn't have a Stripe customer ID (never subscribed), redirect to pricing
        if (!$user->stripe_id) {
            return redirect()->route('pricing')->with('message', 'Please subscribe to access the billing portal.');
        }

        return $user->redirectToBillingPortal(route('profile.edit'));
    }
}
