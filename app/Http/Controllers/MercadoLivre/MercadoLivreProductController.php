<?php

namespace App\Http\Controllers\MercadoLivre;

use App\Http\Controllers\Controller;
use App\Services\MercadoLivre\MercadoLivreProductService;
use Illuminate\Http\Request;

class MercadoLivreProductController extends Controller
{
    /**
     * Consulta um produto/anúncio no Mercado Livre.
     */
    public function show(
        Request $request,
        MercadoLivreProductService $service
    ) {
         $connection = $service->getConnection(
                $request->user()->id
            );
        // $itemId = $request->get('item_id');
        echo('<pre>');
        print_r($service->getProdutos($connection));
        echo('</pre>'); die();

       

        try {
           

            $produto = $service->getProduto(
                $connection,
                (string) 'search'
            );
            echo('<pre>');
            print_r($produtos);
            echo('</pre>'); die();

            return response()->json([
                'success' => true,
                'produto' => $produto,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Localiza a conexão ativa do Mercado Livre.
     */
    protected function getConnection(
        int $userId
    ) {
        return \App\Models\MercadoLivreConnection::where(
            'user_id',
            $userId
        )
            ->where('active', true)
            ->latest()
            ->firstOrFail();
    }
}