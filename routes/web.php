<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinanceiroController;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Http\Request;
use App\Services\Shopee\ShopeeAuthService;
use App\Services\ShopeeService;
use App\Http\Controllers\ShopeeAuthController;
use App\Models\ShopeeConnection;
use App\Http\Controllers\Shopee\ShopeeProductController;


Route::get('/', function () {
    return view('dashboard');
});

Route::get('/shopee/connect', function(ShopeeService $service){

    return redirect()->away(
        $service->getAuthorizationUrl()
    );

});
Route::get('/shopee/callback',[ShopeeAuthController::class, 'callback']);

Route::get('/lancamento/cadastro', function () {
    return view('cadastroLancamento');
});


Route::prefix('shopee')->group(function () {

    Route::get(
        '/produtos/lista',
        [ShopeeProductController::class, 'listarProdutosComDetalhes']
    )->name('shopee.produtos.lista');


    Route::get(
        '/produtos/{id}/editar',
        [ShopeeProductController::class, 'editar']
    )->name('shopee.produtos.editar');


    Route::put(
        '/produtos/{id}',
        [ShopeeProductController::class, 'atualizar']
    )->name('shopee.produtos.atualizar');

});