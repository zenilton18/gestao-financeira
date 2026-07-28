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


    public function getAuthorizationUrl(): string
    {

        $url = 'https://open.shopee.com.br/auth';


        $redirectUrl = 'https://meulucropro.com.br/shopee/callback';


        return $url
            . '?partner_id=' . $this->partnerId
            . '&redirect=' . urlencode($redirectUrl);

    }

}