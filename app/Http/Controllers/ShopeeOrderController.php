<?php

namespace App\Http\Controllers;


use App\Services\Shopee\ShopeeOrderService;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Services\ShopeeService; // Seu serviço de integração com a Shopee
use Illuminate\Support\Facades\Log;



class ShopeeOrderController extends Controller
{


    public function __construct(
        protected ShopeeOrderService $service
    )
    {

    }


    public function index()
    {
        // 1. Base da query sem 'with' para métricas rápidas no banco
        $baseQuery = \App\Models\Pedido::query();

        // Busca por termo
        if (request()->filled('search')) {
            $search = trim(request('search'));

            $baseQuery->where(function ($q) use ($search) {
                $q->where('pedido_externo', 'like', "%{$search}%")
                ->orWhere('usuario_cliente', 'like', "%{$search}%")
                ->orWhereHas('itens', function ($itemQuery) use ($search) {
                    $itemQuery->where('nome_produto', 'like', "%{$search}%")
                                ->orWhere('sku_marketplace', 'like', "%{$search}%");
                });
            });
        }

        // Filtro por Status
        if (request()->filled('status')) {
            $baseQuery->where('status_marketplace', request('status'));
        }

        // Filtro por Período
        if (request()->filled('periodo')) {
            match (request('periodo')) {
                'hoje' => $baseQuery->whereDate('data_pedido', today()),

                'mes'  => $baseQuery->whereYear('data_pedido', now()->year)
                                    ->whereMonth('data_pedido', now()->month),

                '30'   => $baseQuery->where('data_pedido', '>=', now()->subDays(30)),

                default => null
            };
        }

        // 2. Cálculo rápido dos Cards (métricas agregadas)
        $stats = [
            'total'        => (clone $baseQuery)->count(),
            'faturamento'  => (clone $baseQuery)->sum('valor_produtos'),
            'lucro'        => (clone $baseQuery)->sum('lucro_bruto'),
            'ticket_medio' => (clone $baseQuery)->avg('valor_produtos') ?? 0,
        ];

        // dd($baseQuery->toRawSql());

        // 3. Busca da listagem com eager loading dos relacionamentos
        $orders = $baseQuery
            ->with(['itens.produto'])
            ->latest('data_pedido')
            ->paginate(20)
            ->withQueryString();

        return view('shopee.orders.lista', compact('orders', 'stats'));
    }




    public function sync()
    {

        $this->service->syncOrders();


        return back()
            ->with(
                'success',
                'Pedidos sincronizados'
            );

    }
    public function show(\App\Models\Order $order)
    {
        $order->load('items.product');

        return view(
            'shopee.orders.show.show',
            compact('order')
        );
    }
    public function syncOne(\App\Models\Order $order)
    {

        $this->service->syncOrder(
            $order->shopee_order_id
        );


        return back()
            ->with(
                'success',
                'Pedido atualizado com sucesso'
            );

    }
    

   public function imprimirEtiquetas(Request $request, \App\Services\Shopee\ShopeeApiService $shopeeApi)
    {
        // 1. Validação dos IDs do formulário
        $request->validate([
            'order_ids'   => 'required|array|min:1',
            'order_ids.*' => 'exists:pedidos,id',
        ], [
            'order_ids.required' => 'Selecione pelo menos um pedido para gerar as etiquetas.',
        ]);

        // 2. Busca os pedidos selecionados
        $pedidos = Pedido::whereIn('id', $request->input('order_ids'))->get();

        $orderList = [];
        foreach ($pedidos as $pedido) {
            if (empty($pedido->pedido_externo)) {
                continue;
            }

            $item = [
                'order_sn'      => $pedido->pedido_externo,
                'document_type' => 'THERMAL_AIR_WAYBILL', // Teste alterar para 'NORMAL_AIR_WAYBILL' se necessário
                'tracking_number' => 'BR260573501908X'
            ];

         

            $orderList[] = $item;
        }

        if (empty($orderList)) {
            return back()->with('error', 'Nenhum pedido válido com código externo foi encontrado.');
        }

        try {
            // 3. Solicita a criação das etiquetas na Shopee
            $responseCreate = $shopeeApi->post(
                '/api/v2/logistics/create_shipping_document',
                ['order_list' => $orderList]
            );

            // Log de depuração do retorno da Shopee
            Log::info('Resposta Shopee create_shipping_document:', $responseCreate);

            // Se houver erro global na API
            if (!empty($responseCreate['error'])) {
                $failDetail = '';

                // Verifica o motivo individual no result_list
                if (isset($responseCreate['response']['result_list'][0])) {
                    $firstFail = $responseCreate['response']['result_list'][0];
                    $failDetail = sprintf(
                        " [Order %s: %s - %s]",
                        $firstFail['order_sn'] ?? '',
                        $firstFail['fail_error'] ?? '',
                        $firstFail['fail_message'] ?? ''
                    );
                }

                $msg = ($responseCreate['message'] ?? $responseCreate['error']) . $failDetail;
                throw new \Exception($msg);
            }

            sleep(1);

            // 4. Baixa o PDF bruto
            $pdfContent = $shopeeApi->postRaw(
                '/api/v2/logistics/download_shipping_document',
                ['order_list' => $orderList]
            );

            if (empty($pdfContent)) {
                return back()->with('error', 'A Shopee retornou um arquivo de etiqueta vazio.');
            }

            return response($pdfContent, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="etiquetas-shopee-' . now()->format('YmdHis') . '.pdf"',
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar etiquetas Shopee: ' . $e->getMessage());
            return back()->with('error', 'Falha ao buscar etiquetas: ' . $e->getMessage());
        }
    }



}