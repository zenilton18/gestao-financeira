<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopeeWebhookController;

Route::post('/shopee/webhook', [
    ShopeeWebhookController::class,
    'handle'
]);