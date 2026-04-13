<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Plans Configuration
    |--------------------------------------------------------------------------
    |
    | These are the Stripe Price IDs for each subscription tier.
    | Create these products/prices in your Stripe dashboard and add the
    | price IDs to your .env file.
    |
    */

    'plans' => [
        'standard' => [
            'name' => 'Standard',
            'monthly' => [
                'price_id' => env('STRIPE_STANDARD_MONTHLY_PRICE'),
                'amount' => 2900, // $29 in cents
            ],
            'yearly' => [
                'price_id' => env('STRIPE_STANDARD_YEARLY_PRICE'),
                'amount' => 19900, // $199 in cents (save 43%)
            ],
            'features' => [
                'All courses & lessons',
                'Lab environments',
                '1 hour session time',
                '1 concurrent lab',
                'Progress tracking',
            ],
        ],
        'pro' => [
            'name' => 'Pro',
            'monthly' => [
                'price_id' => env('STRIPE_PRO_MONTHLY_PRICE'),
                'amount' => 4900, // $49 in cents
            ],
            'yearly' => [
                'price_id' => env('STRIPE_PRO_YEARLY_PRICE'),
                'amount' => 29900, // $299 in cents (save 49%)
            ],
            'features' => [
                'Everything in Standard',
                '3 hour session time',
                '2 concurrent labs',
                'Priority support',
                'Certificate downloads',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Trial Period
    |--------------------------------------------------------------------------
    */

    'trial_days' => 3,

    /*
    |--------------------------------------------------------------------------
    | Subscription Names
    |--------------------------------------------------------------------------
    |
    | The name used to identify subscriptions in the database.
    |
    */

    'subscription_name' => 'default',
];
