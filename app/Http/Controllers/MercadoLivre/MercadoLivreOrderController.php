<?php

namespace App\Http\Controllers\MercadoLivre;

use App\Http\Controllers\Controller;
use App\Services\MercadoLivre\MercadoLivreOrderService;
use Illuminate\Http\Request;

class MercadoLivreOrderController extends Controller
{
    /**
     * Consulta um pedido do Mercado Livre
     * e retorna todas as informações financeiras.
     */
    public function index(
        Request $request,
        MercadoLivreOrderService $service
    ) {
        $orderId = $request->get('order_id');

        if (!$orderId) {
            return response()->json([
                'success' => false,
                'message' => 'Informe o order_id.',
            ], 422);
        }

        try {
            $connection = $service->getConnection(
                $request->user()->id
            );

            $resultado = $service->getOrderFinanceiro(
                $connection,
                (string) $orderId
            );

            return response()->json([
                'success' => true,
                'order_id' => $orderId,
                'resultado' => $resultado,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Importa um pedido do Mercado Livre
     * e salva no banco de dados.
     */
    public function importar(
        Request $request,
        MercadoLivreOrderService $service
    ) {
        $orderId = $request->get('order_id');

        if (!$orderId) {
            return response()->json([
                'success' => false,
                'message' => 'Informe o order_id.',
            ], 422);
        }

        try {
            $connection = $service->getConnection(
                $request->user()->id
            );

            $pedido = $service->salvarPedido(
                $connection,
                (string) $orderId
            );

            return response()->json([
                'success' => true,
                'message' => 'Pedido importado com sucesso.',
                'pedido' => $pedido,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}