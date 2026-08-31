<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\ShopeeAuthController;
use App\Http\Controllers\Shopee\ShopeeProductController;
use App\Http\Controllers\ShopeeDashboardController;
use App\Http\Controllers\ShopeeOrderController;
use App\Services\ShopeeService;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\ShopeeWebhookController;
use App\Http\Controllers\MercadoLivre\MercadoLivreAuthController;
use App\Http\Controllers\MercadoLivre\MercadoLivreOrderController;
use App\Http\Controllers\MercadoLivre\MercadoLivreProductController;


/*
|--------------------------------------------------------------------------
| Contas / Dashboard
|--------------------------------------------------------------------------
*/

Route::post('/contas', [ContaController::class, 'store'])
    ->name('contas.store');

Route::get('/contas', [ContaController::class, 'create'])
    ->name('contas.create');

Route::patch('/contas/{id}/dar-baixa', [ContaController::class, 'darBaixa'])
    ->name('contas.darBaixa');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
    Route::get('/dashboard/grafico', [
    DashboardController::class,
    'grafico'
])->name('dashboard.grafico');

Route::get('/dashboard/lancamentos-data', [
    DashboardController::class,
    'lancamentosPorData'
])->name('dashboard.lancamentos.data');

/*
|--------------------------------------------------------------------------
| Rotas Autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Connect & OAuth Shopee
    |--------------------------------------------------------------------------
    */

    Route::get('/shopee/connect', function (ShopeeService $service) {
        return redirect()->away($service->getAuthorizationUrl());
    })->name('shopee.connect');

    Route::get('/shopee/callback', [ShopeeAuthController::class, 'callback'])
        ->name('shopee.callback');

    /*
    |--------------------------------------------------------------------------
    | Dashboard Shopee
    |--------------------------------------------------------------------------
    */

    Route::get('/', function () {
        return redirect()->route('shopee.dashboard');
    });

    Route::get('/shopee/dashboard', [ShopeeDashboardController::class, 'index'])
        ->name('shopee.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Produtos Shopee
    |--------------------------------------------------------------------------
    */

    Route::prefix('shopee/produtos')->group(function () {

        Route::get('/sincronizar', [ShopeeProductController::class, 'sincronizarProdutos'])
            ->name('shopee.produtos.sincronizar');

        Route::get('/lista', [ShopeeProductController::class, 'listarProdutosComDetalhes'])
            ->name('shopee.produtos.lista');

        Route::get('/{id}/editar', [ShopeeProductController::class, 'editar'])
            ->name('shopee.produtos.editar');

        Route::put('/{id}', [ShopeeProductController::class, 'atualizar'])
            ->name('shopee.produtos.atualizar');
    });

    /*
    |--------------------------------------------------------------------------
    | Pedidos Shopee
    |--------------------------------------------------------------------------
    */

    Route::prefix('shopee/orders')->group(function () {

        Route::get('/lista', [ShopeeOrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/sync', [ShopeeOrderController::class, 'sync'])
            ->name('shopee.orders.sync');

        Route::post('/etiquetas', [ShopeeOrderController::class, 'imprimirEtiquetas'])
            ->name('shopee.orders.etiquetas');

        Route::get('/{order}', [ShopeeOrderController::class, 'show'])
            ->name('shopee.orders.show');

        Route::post('/{order}/sync', [ShopeeOrderController::class, 'syncOne'])
            ->name('shopee.orders.syncOne');
    });

    /*
    |--------------------------------------------------------------------------
    | Financeiro
    |--------------------------------------------------------------------------
    */

    Route::get('/lancamento/cadastro', function () {
        return view('cadastroLancamento');
    })->name('financeiro.lancamento');

    Route::post('/financeiro/salvar', [FinanceiroController::class, 'store'])
        ->name('financeiro.salvar');

    /*
    |--------------------------------------------------------------------------
    | Mercado Livre
    |--------------------------------------------------------------------------
    */

    Route::prefix('mercadolivre')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Autenticação
        |--------------------------------------------------------------------------
        */

        Route::get('/auth', [MercadoLivreAuthController::class, 'redirect'])
            ->name('mercadolivre.auth');

        Route::get('/callback', [MercadoLivreAuthController::class, 'callback'])
            ->name('mercadolivre.callback');

        /*
        |--------------------------------------------------------------------------
        | Pedidos
        |--------------------------------------------------------------------------
        */

        Route::get('/orders', [MercadoLivreOrderController::class, 'index'])
            ->name('mercadolivre.orders');

        Route::get('/pedidos/importar', [MercadoLivreOrderController::class, 'importar'])
            ->name('mercadolivre.pedidos.importar');
            Route::get(
            '/produtos',
            [MercadoLivreProductController::class, 'show']
        )->name('mercadolivre.produtos.show');
    });
});

/*
|--------------------------------------------------------------------------
| Autenticação Breeze
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';