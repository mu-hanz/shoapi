<?php

/**
 * ShoAPI Configuration
 */
return [

    /**
     * API method sandbox or production
     *
     * Default false
     * @boolean
     */
    'production' => env('SHOPEE_PRODUCTION', false),

    /**
     * API host URL [DO NOT REPLACE OR MODIFY]
     *
     * Default 'https://partner.shopeemobile.com'
     * @string url
     */
    'host_url' => env('SHOPEE_HOST_URL', 'https://partner.shopeemobile.com'),

    /**
     * API sandbox host URL  [DO NOT REPLACE OR MODIFY]
     *
     * Default 'https://partner.test-stable.shopeemobile.com'
     * @string url
     */
    'sandbox_host_url' => env('SHOPEE_SANDBOX_HOST_URL', 'https://partner.test-stable.shopeemobile.com'),

    /**
     * API version [DO NOT REPLACE OR MODIFY]
     *
     * Default '/api/v2/'
     * @string
     */
    'api_version' => env('SHOPEE_API_VERSION', '/api/v2/'),

    /**
     * API callback url for auth partner and cancel auth partner
     *
     * @string url
     */
    'callback_url' => env('SHOPEE_CALLBACK_URL'),

    /**
     * Your Partner ID
     *
     * @int
     */
    'partner_id' => env('SHOPEE_PARTNER_ID'),

    /**
     * Your Partner Key
     *
     * @string
     */
    'partner_key' => env('SHOPEE_PARTNER_KEY'),

];
