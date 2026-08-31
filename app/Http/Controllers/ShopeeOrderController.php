<?php

namespace App\Http\Controllers;


use App\Services\Shopee\ShopeeOrderService;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Services\ShopeeService; // Seu serviço de integração com a Shopee
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;




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
    
public function imprimirEtiquetas(
    Request $request,
    \App\Services\Shopee\ShopeeApiService $shopeeApi
) {
    /*
     * =========================================================
     * 1. VALIDAÇÃO
     * =========================================================
     */
    $request->validate([
        'order_ids'   => 'required|array|min:1',
        'order_ids.*' => 'exists:pedidos,id',
    ], [
        'order_ids.required' =>
            'Selecione pelo menos um pedido para gerar as etiquetas.',
    ]);

    /*
     * =========================================================
     * 2. BUSCA OS PEDIDOS
     * =========================================================
     */
    $pedidos = Pedido::whereIn(
        'id',
        $request->input('order_ids')
    )->get();

    $orderList = [];
    $pedidosSemRastreio = [];

    foreach ($pedidos as $pedido) {

        /*
         * Pedido precisa ter pedido_externo
         */
        if (empty($pedido->pedido_externo)) {

            Log::warning(
                '[Shopee Etiquetas] Pedido sem pedido_externo',
                [
                    'pedido_id' => $pedido->id,
                ]
            );

            continue;
        }

        /*
         * Pedido precisa ter código de rastreio
         */
        if (empty($pedido->codigo_rastreio)) {

            $pedidosSemRastreio[] =
                $pedido->pedido_externo;

            Log::warning(
                '[Shopee Etiquetas] Pedido sem código de rastreio',
                [
                    'pedido_id' =>
                        $pedido->id,

                    'order_sn' =>
                        $pedido->pedido_externo,
                ]
            );

            continue;
        }

        /*
         * Adiciona pedido à lista da Shopee
         */
        $orderList[] = [

            'order_sn' =>
                $pedido->pedido_externo,

            'document_type' =>
                'THERMAL_AIR_WAYBILL',

            'tracking_number' =>
                $pedido->codigo_rastreio,
        ];
    }

    /*
     * =========================================================
     * 3. NENHUM PEDIDO VÁLIDO
     * =========================================================
     */
    if (empty($orderList)) {

        return back()->with(
            'error',
            'Nenhum dos pedidos selecionados possui código de rastreio.'
        );
    }

    try {

        /*
         * =====================================================
         * 4. LOG INICIAL
         * =====================================================
         */
        Log::info(
            '[Shopee Etiquetas] Iniciando geração das etiquetas',
            [
                'quantidade' =>
                    count($orderList),

                'pedidos' =>
                    $orderList,
            ]
        );

        /*
         * =====================================================
         * 5. CRIA AS ETIQUETAS NA SHOPEE
         * =====================================================
         */
        $responseCreate = $shopeeApi->post(
            '/api/v2/logistics/create_shipping_document',
            [
                'order_list' =>
                    $orderList,
            ]
        );

        Log::info(
            '[Shopee Etiquetas] Resposta create_shipping_document',
            [
                'response' =>
                    $responseCreate,
            ]
        );

        /*
         * =====================================================
         * 6. VERIFICA ERRO DA SHOPEE
         * =====================================================
         */
        if (!empty($responseCreate['error'])) {

            $failDetail = '';

            foreach (
                $responseCreate['response']['result_list']
                ?? []
                as $result
            ) {

                if (
                    !empty($result['fail_error']) ||
                    !empty($result['fail_message'])
                ) {

                    $failDetail .= sprintf(
                        ' [Order %s: %s - %s]',
                        $result['order_sn'] ?? '',
                        $result['fail_error'] ?? '',
                        $result['fail_message'] ?? ''
                    );
                }
            }

            $msg =
                ($responseCreate['message']
                    ?? $responseCreate['error']
                    ?? 'Erro ao criar etiquetas.')
                . $failDetail;

            throw new \Exception($msg);
        }

        /*
         * =====================================================
         * 7. AGUARDA A SHOPEE PREPARAR O DOCUMENTO
         * =====================================================
         */
        sleep(2);

        /*
         * =====================================================
         * 8. BAIXA O PDF DA SHOPEE
         * =====================================================
         */
        $pdfContent = $shopeeApi->postRaw(
            '/api/v2/logistics/download_shipping_document',
            [
                'order_list' =>
                    $orderList,
            ]
        );

        /*
         * =====================================================
         * 9. VERIFICA PDF VAZIO
         * =====================================================
         */
        if (empty($pdfContent)) {

            return back()->with(
                'error',
                'A Shopee retornou um arquivo de etiqueta vazio.'
            );
        }

        /*
         * =====================================================
         * 10. VERIFICA SE É PDF
         * =====================================================
         */
        if (
            !str_starts_with(
                ltrim($pdfContent),
                '%PDF'
            )
        ) {

            Log::error(
                '[Shopee Etiquetas] Resposta não parece ser um PDF',
                [
                    'tamanho' =>
                        strlen($pdfContent),

                    'inicio' =>
                        substr(
                            $pdfContent,
                            0,
                            200
                        ),
                ]
            );

            return back()->with(
                'error',
                'A Shopee não retornou um PDF válido.'
            );
        }

        /*
         * =====================================================
         * 11. SALVA PDF ORIGINAL TEMPORARIAMENTE
         * =====================================================
         */
        $tempPdf = tempnam(
            sys_get_temp_dir(),
            'shopee_etiquetas_'
        );

        file_put_contents(
            $tempPdf,
            $pdfContent
        );

        /*
         * =====================================================
         * 12. TENTA PROCESSAR COM FPDI
         * =====================================================
         *
         * Se qualquer coisa der errado nessa etapa,
         * vamos retornar automaticamente o PDF original
         * da Shopee.
         */
        try {

            Log::info(
                '[Shopee Etiquetas] Iniciando processamento FPDI'
            );

            /*
             * Cria PDF final
             */
            $pdfFinal = new Fpdi();

            /*
             * Carrega PDF original
             */
            $totalPaginas =
                $pdfFinal->setSourceFile(
                    $tempPdf
                );

            Log::info(
                '[Shopee Etiquetas] PDF carregado pelo FPDI',
                [
                    'total_paginas' =>
                        $totalPaginas,
                ]
            );

            /*
             * =================================================
             * PROCESSA CADA PÁGINA ORIGINAL
             * =================================================
             */
            for (
                $pagina = 1;
                $pagina <= $totalPaginas;
                $pagina++
            ) {

                Log::info(
                    '[Shopee Etiquetas] Processando página',
                    [
                        'pagina' =>
                            $pagina,

                        'total' =>
                            $totalPaginas,
                    ]
                );

                /*
                 * Importa página original
                 */
                $template =
                    $pdfFinal->importPage(
                        $pagina
                    );

                /*
                 * Obtém tamanho original
                 */
                $tamanho =
                    $pdfFinal->getTemplateSize(
                        $template
                    );

                $larguraOriginal =
                    (float) $tamanho['width'];

                $alturaOriginal =
                    (float) $tamanho['height'];

                Log::info(
                    '[Shopee Etiquetas] Dimensões da página original',
                    [
                        'pagina' =>
                            $pagina,

                        'largura_mm' =>
                            $larguraOriginal,

                        'altura_mm' =>
                            $alturaOriginal,

                        'orientacao' =>
                            $tamanho['orientation']
                                ?? null,
                    ]
                );

                /*
                 * =================================================
                 * TAMANHO DA ETIQUETA FINAL
                 * =================================================
                 *
                 * 10 x 15 cm
                 *
                 * 100 x 150 mm
                 */
                $larguraFinal = 100;
                $alturaFinal  = 150;

                /*
                 * =================================================
                 * DIVIDE A PÁGINA ORIGINAL EM 4
                 * =================================================
                 */
                $larguraQuadrante =
                    $larguraOriginal / 2;

                $alturaQuadrante =
                    $alturaOriginal / 2;

                Log::info(
                    '[Shopee Etiquetas] Dimensão estimada de cada etiqueta',
                    [
                        'largura_mm' =>
                            $larguraQuadrante,

                        'altura_mm' =>
                            $alturaQuadrante,
                    ]
                );

                /*
                 * =================================================
                 * ESCALA
                 * =================================================
                 *
                 * Queremos colocar cada quadrante dentro
                 * de uma página 100 x 150 mm.
                 */
                $escalaX =
                    $larguraFinal /
                    $larguraQuadrante;

                $escalaY =
                    $alturaFinal /
                    $alturaQuadrante;

                /*
                 * Mantém proporção
                 */
                $escala =
                    min(
                        $escalaX,
                        $escalaY
                    );

                $larguraRender =
                    $larguraQuadrante *
                    $escala;

                $alturaRender =
                    $alturaQuadrante *
                    $escala;

                /*
                 * Centraliza
                 */
                $offsetX =
                    ($larguraFinal -
                        $larguraRender) / 2;

                $offsetY =
                    ($alturaFinal -
                        $alturaRender) / 2;

                /*
                 * =================================================
                 * QUATRO ETIQUETAS
                 * =================================================
                 */
                $quadrantes = [

                    /*
                     * 1 - Superior esquerda
                     */
                    [
                        'nome' => 'superior_esquerda',

                        'origemX' => 0,

                        'origemY' => 0,
                    ],

                    /*
                     * 2 - Superior direita
                     */
                    [
                        'nome' => 'superior_direita',

                        'origemX' =>
                            $larguraQuadrante,

                        'origemY' => 0,
                    ],

                    /*
                     * 3 - Inferior esquerda
                     */
                    [
                        'nome' => 'inferior_esquerda',

                        'origemX' => 0,

                        'origemY' =>
                            $alturaQuadrante,
                    ],

                    /*
                     * 4 - Inferior direita
                     */
                    [
                        'nome' => 'inferior_direita',

                        'origemX' =>
                            $larguraQuadrante,

                        'origemY' =>
                            $alturaQuadrante,
                    ],
                ];

                /*
                 * =================================================
                 * CRIA UMA PÁGINA PARA CADA ETIQUETA
                 * =================================================
                 */
                foreach (
                    $quadrantes as $indice => $quadrante
                ) {

                    Log::info(
                        '[Shopee Etiquetas] Criando etiqueta',
                        [
                            'pagina_original' =>
                                $pagina,

                            'etiqueta' =>
                                $indice + 1,

                            'posicao' =>
                                $quadrante['nome'],
                        ]
                    );

                    /*
                     * Cria página 10x15
                     */
                    $pdfFinal->AddPage(
                        'P',
                        [
                            $larguraFinal,
                            $alturaFinal,
                        ]
                    );

                    /*
                     * =================================================
                     * DESLOCAMENTO
                     * =================================================
                     *
                     * A ideia é posicionar a página original
                     * de forma que somente o quadrante desejado
                     * fique dentro da página final.
                     */

                    $posicaoX =
                        $offsetX -
                        ($quadrante['origemX'] *
                            $escala);

                    $posicaoY =
                        $offsetY -
                        ($quadrante['origemY'] *
                            $escala);

                    /*
                     * Importa o template.
                     */
                    $pdfFinal->useTemplate(
                        $template,
                        $posicaoX,
                        $posicaoY,
                        $larguraOriginal *
                            $escala,
                        $alturaOriginal *
                            $escala
                    );
                }
            }

            /*
             * =====================================================
             * 13. GERA PDF FINAL
             * =====================================================
             */
            $pdfFinalContent =
                $pdfFinal->Output(
                    'S'
                );

            /*
             * =====================================================
             * 14. VERIFICA PDF FINAL
             * =====================================================
             */
            if (
                empty($pdfFinalContent) ||
                !str_starts_with(
                    ltrim($pdfFinalContent),
                    '%PDF'
                )
            ) {

                throw new \Exception(
                    'FPDI gerou um conteúdo inválido.'
                );
            }

            Log::info(
                '[Shopee Etiquetas] PDF final criado com sucesso',
                [
                    'paginas_originais' =>
                        $totalPaginas,

                    /*
                     * Cada página original gera
                     * 4 etiquetas.
                     */
                    'paginas_finais' =>
                        $totalPaginas * 4,

                    'tamanho_original' =>
                        strlen($pdfContent),

                    'tamanho_final' =>
                        strlen($pdfFinalContent),
                ]
            );

            /*
             * Remove temporário
             */
            if (
                file_exists($tempPdf)
            ) {

                unlink($tempPdf);
            }

            /*
             * Nome do arquivo
             */
            $nomeArquivo =
                'etiquetas-shopee-'
                . now()->format('Ymd-His')
                . '.pdf';

            /*
             * =================================================
             * RETORNA PDF FINAL
             * =================================================
             */
            return response(
                $pdfFinalContent,
                200,
                [
                    'Content-Type' =>
                        'application/pdf',

                    'Content-Disposition' =>
                        'inline; filename="' .
                        $nomeArquivo .
                        '"',

                    'Content-Length' =>
                        strlen(
                            $pdfFinalContent
                        ),

                    'Cache-Control' =>
                        'private, max-age=0, must-revalidate',

                    'Pragma' =>
                        'public',
                ]
            );

        } catch (\Throwable $fpdiException) {

            /*
             * =====================================================
             * ERRO NO FPDI
             * =====================================================
             *
             * NÃO deixa o usuário sem etiqueta.
             *
             * Registra tudo no log e retorna o PDF original
             * da Shopee.
             */
            Log::error(
                '[Shopee Etiquetas] ERRO NO PROCESSAMENTO FPDI',
                [
                    'mensagem' =>
                        $fpdiException->getMessage(),

                    'linha' =>
                        $fpdiException->getLine(),

                    'arquivo' =>
                        $fpdiException->getFile(),

                    'trace' =>
                        $fpdiException->getTraceAsString(),
                ]
            );

            /*
             * Remove arquivo temporário
             */
            if (
                file_exists($tempPdf)
            ) {

                unlink($tempPdf);
            }

            /*
             * Log informando fallback
             */
            Log::warning(
                '[Shopee Etiquetas] Retornando PDF ORIGINAL da Shopee devido a erro no FPDI'
            );

            /*
             * Nome do arquivo original
             */
            $nomeArquivo =
                'etiquetas-shopee-original-'
                . now()->format('Ymd-His')
                . '.pdf';

            /*
             * Retorna PDF original
             */
            return response(
                $pdfContent,
                200,
                [
                    'Content-Type' =>
                        'application/pdf',

                    'Content-Disposition' =>
                        'inline; filename="' .
                        $nomeArquivo .
                        '"',

                    'Content-Length' =>
                        strlen(
                            $pdfContent
                        ),

                    'Cache-Control' =>
                        'private, max-age=0, must-revalidate',

                    'Pragma' =>
                        'public',
                ]
            );
        }

    } catch (\Throwable $e) {

        /*
         * =========================================================
         * ERRO GERAL
         * =========================================================
         */
        Log::error(
            '[Shopee Etiquetas] Erro ao gerar PDF',
            [
                'mensagem' =>
                    $e->getMessage(),

                'linha' =>
                    $e->getLine(),

                'arquivo' =>
                    $e->getFile(),

                'pedidos' =>
                    $orderList,
            ]
        );

        /*
         * Remove temporário se existir
         */
        if (
            isset($tempPdf) &&
            file_exists($tempPdf)
        ) {

            unlink($tempPdf);
        }

        return back()->with(
            'error',
            'Falha ao gerar etiquetas: '
            . $e->getMessage()
        );
    }
}
  






}