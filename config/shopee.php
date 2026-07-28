<?php

return [

    'partner_id' => env('SHOPEE_PARTNER_ID'),

    'partner_key' => env('SHOPEE_PARTNER_KEY'),


    // API da Shopee (não é a URL de autorização)
    'base_url' => env('SHOPEE_ENV') === 'production'
        ? 'https://partner.shopeemobile.com'
        : 'https://partner.test-stable.shopeemobile.com',

];