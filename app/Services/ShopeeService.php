<?php

namespace App\Services;


class ShopeeService
{

    private int $partnerId;
    private string $partnerKey;


    public function __construct()
    {
        $this->partnerId = (int) trim(env('SHOPEE_PARTNER_ID'));

        $this->partnerKey = trim(env('SHOPEE_PARTNER_KEY'));
    }


    public function getAuthorizationUrl()
    {

        $path = "/api/v2/shop/auth_partner";


        $redirectUrl = "https://gestao-financeira.test/shopee/callback";


        $timestamp = time();


        $baseString = sprintf(
            "%s%s%s",
            $this->partnerId,
            $path,
            $timestamp
        );


        $sign = hash_hmac(
            'sha256',
            $baseString,
            $this->partnerKey
        );


        return config('shopee.base_url')
            .$path
            .'?partner_id='.$this->partnerId
            .'&timestamp='.$timestamp
            .'&sign='.$sign
            .'&redirect='.$redirectUrl;

    }

}