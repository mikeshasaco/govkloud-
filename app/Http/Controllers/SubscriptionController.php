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

        $subscriptionName = config('stripe-plans.subscription_name');

        // If user already has an active subscription, swap to the new plan
        if ($user->subscribed($subscriptionName)) {
            $user->subscription($subscriptionName)->swap($priceId);

            return redirect()->route('courses.index')
                ->with('success', 'Your plan has been updated!');
        }

        // New subscription — include trial days
        $trialDays = config('stripe-plans.trial_days');

        return $user->newSubscription($subscriptionName, $priceId)
            ->trialDays($trialDays)
            ->checkout([
                'success_url' => route('subscription.success'),
                'cancel_url' => route('pricing'),
            ]);
    }

    /**
     * Handle successful subscription — redirect straight to courses
     */
    public function success()
    {
        $message = ' Welcome to GovKloud! Your subscription is active. Start exploring courses below.';

        if (auth()->check() && auth()->user()->onTrial()) {
            $trialDays = config('stripe-plans.trial_days');
            $message .= " Your {$trialDays}-day free trial has started!";
        }

        return redirect()->route('courses.index')->with('success', $message);
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

        return $user->redirectToBillingPortal(route('account.settings'));
    }
}
