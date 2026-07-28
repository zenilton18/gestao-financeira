<?php

return [

    'partner_id'=>env('SHOPEE_PARTNER_ID'),

    'partner_key'=>env('SHOPEE_PARTNER_KEY'),


  'base_url' => env('SHOPEE_ENV') === 'production'
        ? 'https://openplatform.shopee.com'
        : 'https://openplatform.sandbox.test-stable.shopee.sg',

];