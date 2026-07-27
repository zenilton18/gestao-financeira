<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\ShopeeAuthController;
use App\Http\Controllers\Shopee\ShopeeProductController;
use App\Http\Controllers\ShopeeDashboardController;
use App\Http\Controllers\ShopeeOrderController;

use App\Services\ShopeeService;

Route::middleware('auth')->group(function () {

    Route::get('/shopee/connect', function (ShopeeService $service) {

        return redirect()->away(
            $service->getAuthorizationUrl()
        );

    })->name('shopee.connect');


    Route::get('/shopee/callback', [
        ShopeeAuthController::class,
        'callback'
    ])->name('shopee.callback');


    /*
    |--------------------------------------------------------------------------
    | Dashboard Shopee
    |--------------------------------------------------------------------------
    */

    Route::get('/', function () {
        return redirect()->route('shopee.dashboard');
    });

    Route::get(
        '/shopee/dashboard',
        [ShopeeDashboardController::class, 'index']
    )->name('shopee.dashboard');



    /*
    |--------------------------------------------------------------------------
    | Produtos Shopee
    |--------------------------------------------------------------------------
    */

    Route::prefix('shopee/produtos')
        ->group(function () {

            Route::get(
                '/sincronizar',
                [ShopeeProductController::class, 'sincronizarProdutos']
            )->name('shopee.produtos.sincronizar');

            Route::get(
                '/lista',
                [ShopeeProductController::class, 'listarProdutosComDetalhes']
            )->name('shopee.produtos.lista');



            Route::get(
                '/{id}/editar',
                [ShopeeProductController::class, 'editar']
            )->name('shopee.produtos.editar');


            Route::put(
                '/{id}',
                [ShopeeProductController::class, 'atualizar']
            )->name('shopee.produtos.atualizar');


        });



    /*
    |--------------------------------------------------------------------------
    | Pedidos Shopee
    |--------------------------------------------------------------------------
    */

    Route::prefix('shopee/orders')
        ->group(function () {


            Route::get(
                '/lista',
                [ShopeeOrderController::class, 'index']
            )->name('orders.index');


            Route::get(
                '/sync',
                [ShopeeOrderController::class, 'sync']
            )->name('shopee.orders.sync');
            Route::get(
                '/{order}',
                [ShopeeOrderController::class, 'show']
            )->name('shopee.orders.show');
            Route::post(
            '/{order}/sync',
            [ShopeeOrderController::class,'syncOne']
        )
        ->name('shopee.orders.syncOne');


        });



    /*
    |--------------------------------------------------------------------------
    | Financeiro
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/lancamento/cadastro',
        function () {
            return view('cadastroLancamento');
        }
    )->name('financeiro.lancamento');
    Route::post(
        '/financeiro/salvar',
        [FinanceiroController::class, 'store']
    )->name('financeiro.salvar');


});


/*
|--------------------------------------------------------------------------
| Autenticação Breeze
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';