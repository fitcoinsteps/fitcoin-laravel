<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Steps to Fitcoin Conversion Rate
    |--------------------------------------------------------------------------
    |
    | This value determines how many steps are required to earn 1 Fitcoin.
    | For example, 1000 steps = 1 Fitcoin.
    |
    */
    'conversion_rate' => env('FITCOIN_CONVERSION_RATE', 10),

    /*
    |--------------------------------------------------------------------------
    | FIT Coin to USD Conversion Rate
    |--------------------------------------------------------------------------
    |
    | How many FIT coins equal 1 USD (for gift cards)
    | Example: 100 FIT = $1 USD
    |
    */
    'fit_to_usd_rate' => env('FIT_TO_USD_RATE', 100),

    /*
    |--------------------------------------------------------------------------
    | Gift Card Providers
    |--------------------------------------------------------------------------
    |
    | Available gift card providers with their configurations
    |
    */
    'gift_cards' => [
        'providers' => [
            'amazon' => [
                'name' => 'Amazon',
                'icon' => 'shopping_bag_outlined',
                'color' => '#FF9900',
                'enabled' => true,
                'available_values' => [5, 10, 25, 50, 100],
                'min_fitcoins' => 500,
                'max_fitcoins' => 10000,
            ],
            'google_play' => [
                'name' => 'Google Play',
                'icon' => 'android',
                'color' => '#34A853',
                'enabled' => true,
                'available_values' => [5, 10, 25, 50],
                'min_fitcoins' => 500,
                'max_fitcoins' => 5000,
            ],
            'steam' => [
                'name' => 'Steam',
                'icon' => 'gamepad',
                'color' => '#171A21',
                'enabled' => true,
                'available_values' => [5, 10, 25, 50],
                'min_fitcoins' => 500,
                'max_fitcoins' => 5000,
            ],
            'apple' => [
                'name' => 'Apple',
                'icon' => 'apple',
                'color' => '#555555',
                'enabled' => true,
                'available_values' => [5, 10, 25, 50],
                'min_fitcoins' => 500,
                'max_fitcoins' => 5000,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Crypto Withdrawal Settings
    |--------------------------------------------------------------------------
    |
    | Available cryptocurrencies for withdrawal
    |
    */
    'crypto' => [
        'enabled' => env('CRYPTO_WITHDRAWAL_ENABLED', true),
        'min_withdrawal_amount' => env('CRYPTO_MIN_WITHDRAWAL', 5), // Minimum USDT
        'max_withdrawal_amount' => env('CRYPTO_MAX_WITHDRAWAL', 1000), // Maximum USDT
        'admin_fee_percentage' => env('CRYPTO_ADMIN_FEE', 2), // 2% fee

        'currencies' => [
            'USDT' => [
                'name' => 'Tether USD',
                'symbol' => 'USDT',
                'icon' => 'attach_money',
                'color' => '#26A17B',
                'enabled' => true,
                'networks' => ['ERC-20', 'BEP-20'],
                'fitcoins_per_unit' => env('USDT_FIT_RATE', 1000), // 1000 FIT = 1 USDT
                'min_amount' => env('USDT_MIN_AMOUNT', 10),
                'max_amount' => env('USDT_MAX_AMOUNT', 1000),
                'min_fitcoins' => 100,
                'max_fitcoins' => 100000,
            ],
            'USDC' => [
                'name' => 'USD Coin',
                'symbol' => 'USDC',
                'icon' => 'currency_bitcoin',
                'color' => '#2775CA',
                'enabled' => true,
                'networks' => ['ERC-20', 'BEP-20'],
                'fitcoins_per_unit' => env('USDC_FIT_RATE', 1000), // 1000 FIT = 1 USDC
                'min_amount' => env('USDC_MIN_AMOUNT', 10),
                'max_amount' => env('USDC_MAX_AMOUNT', 1000),
                'min_fitcoins' => 100,
                'max_fitcoins' => 100000,
            ],
            'BTC' => [
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
                'icon' => 'currency_bitcoin',
                'color' => '#F7931A',
                'enabled' => env('BTC_ENABLED', true),
                'networks' => ['Lightning Network'],
                'fitcoins_per_unit' => env('BTC_FIT_RATE', 50000), // 50000 FIT = 1 BTC
                'min_amount' => env('BTC_MIN_AMOUNT', 0.0001),
                'max_amount' => env('BTC_MAX_AMOUNT', 0.01),
                'min_fitcoins' => 1000,
                'max_fitcoins' => 1000000,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gift Card Redemption Settings
    |--------------------------------------------------------------------------
    |
    | General settings for gift card redemptions
    |
    */
    'redemption' => [
        // Auto-complete redemption (skip pending status)
        'auto_complete' => env('GIFT_CARD_AUTO_COMPLETE', true),
        
        // Allow redemption of used gift cards (should be false)
        'allow_used' => env('GIFT_CARD_ALLOW_USED', false),
        
        // Maximum redemptions per user per day
        'max_per_day' => env('GIFT_CARD_MAX_PER_DAY', 5),
        
        // Minimum FIT coins required to redeem
        'min_fitcoins' => env('GIFT_CARD_MIN_FITCOINS', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limits for gift card redemptions and crypto withdrawals
    |
    */
    'rate_limits' => [
        'redemptions' => [
            'max_attempts' => env('REDEMPTION_MAX_ATTEMPTS', 10),
            'decay_minutes' => env('REDEMPTION_DECAY_MINUTES', 60),
        ],
        'withdrawals' => [
            'max_attempts' => env('WITHDRAWAL_MAX_ATTEMPTS', 5),
            'decay_minutes' => env('WITHDRAWAL_DECAY_MINUTES', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Notifications
    |--------------------------------------------------------------------------
    |
    | Email addresses to notify for new redemptions/withdrawals
    |
    */
    'notifications' => [
        'admin_emails' => explode(',', env('ADMIN_NOTIFICATION_EMAILS', 'admin@fitcoin.com')),
        'slack_webhook' => env('SLACK_WEBHOOK_URL'),
        'send_on_redemption' => env('NOTIFY_ON_REDEMPTION', true),
        'send_on_withdrawal' => env('NOTIFY_ON_WITHDRAWAL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Cache duration for rates and gift card availability
    |
    */
    'cache' => [
        'rates_duration' => env('CACHE_RATES_DURATION', 3600), // 1 hour
        'gift_cards_duration' => env('CACHE_GIFT_CARDS_DURATION', 300), // 5 minutes
    ],
];