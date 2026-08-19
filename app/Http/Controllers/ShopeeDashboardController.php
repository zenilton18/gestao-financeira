<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\Shopee\ShopeeAdsService;


class ShopeeDashboardController extends Controller
{

    public function __construct(
        protected ShopeeAdsService $shopeeAdsService
    ) {
    }


    public function index(Request $request)
    {

        /*
        |--------------------------------------------------------------------------
        | FILTRO
        |--------------------------------------------------------------------------
        */

        $periodo = $request->get(
            'periodo',
            'today'
        );


        /*
        |--------------------------------------------------------------------------
        | DATAS PADRÃO
        |--------------------------------------------------------------------------
        */

        $dataInicio = now()->startOfDay();

        $dataFim = now()->endOfDay();


        /*
        |--------------------------------------------------------------------------
        | STATUS VÁLIDOS
        |--------------------------------------------------------------------------
        */

        $statusValidos = [

            'READY_TO_SHIP',
            'PROCESSED',
            'SHIPPED',
            'TO_CONFIRM_RECEIVE',
            'COMPLETED',

        ];


        /*
        |--------------------------------------------------------------------------
        | CONSULTA BASE
        |--------------------------------------------------------------------------
        */

        $pedidoQuery = Pedido::query()

            ->whereIn(
                'status_marketplace',
                $statusValidos
            );


        /*
        |--------------------------------------------------------------------------
        | FILTRO DE PERÍODO
        |--------------------------------------------------------------------------
        */

        switch ($periodo) {


            /*
            |--------------------------------------------------------------------------
            | HOJE
            |--------------------------------------------------------------------------
            */

            case 'today':

                $dataInicio =
                    now()->startOfDay();

                $dataFim =
                    now()->endOfDay();


                $pedidoQuery->whereDate(
                    'data_pedido',
                    today()
                );

                break;


            /*
            |--------------------------------------------------------------------------
            | ONTEM
            |--------------------------------------------------------------------------
            */

            case 'yesterday':

                $dataInicio =
                    now()
                        ->subDay()
                        ->startOfDay();

                $dataFim =
                    now()
                        ->subDay()
                        ->endOfDay();


                $pedidoQuery->whereDate(
                    'data_pedido',
                    today()->subDay()
                );

                break;


            /*
            |--------------------------------------------------------------------------
            | ESTA SEMANA
            |--------------------------------------------------------------------------
            */

            case 'week':

                $dataInicio =
                    now()->startOfWeek();

                $dataFim =
                    now()->endOfWeek();


                $pedidoQuery->whereBetween(
                    'data_pedido',
                    [
                        $dataInicio,
                        $dataFim
                    ]
                );

                break;


            /*
            |--------------------------------------------------------------------------
            | ÚLTIMOS 30 DIAS
            |--------------------------------------------------------------------------
            */

            case '30':

                $dataInicio =
                    now()
                        ->subDays(30)
                        ->startOfDay();

                $dataFim =
                    now()->endOfDay();


                $pedidoQuery->whereBetween(
                    'data_pedido',
                    [
                        $dataInicio,
                        $dataFim
                    ]
                );

                break;


            /*
            |--------------------------------------------------------------------------
            | ESTE MÊS
            |--------------------------------------------------------------------------
            */

            case 'month':

                $dataInicio =
                    now()->startOfMonth();

                $dataFim =
                    now()->endOfMonth();


                $pedidoQuery->whereBetween(
                    'data_pedido',
                    [
                        $dataInicio,
                        $dataFim
                    ]
                );

                break;


            /*
            |--------------------------------------------------------------------------
            | MÊS ANTERIOR
            |--------------------------------------------------------------------------
            */

            case 'last_month':

                $dataInicio =
                    now()
                        ->subMonth()
                        ->startOfMonth();

                $dataFim =
                    now()
                        ->subMonth()
                        ->endOfMonth();


                $pedidoQuery->whereBetween(
                    'data_pedido',
                    [
                        $dataInicio,
                        $dataFim
                    ]
                );

                break;


            /*
            |--------------------------------------------------------------------------
            | PERSONALIZADO
            |--------------------------------------------------------------------------
            */

            case 'custom':

                if (
                    $request->filled('data_inicio') &&
                    $request->filled('data_fim')
                ) {

                    $dataInicio =
                        Carbon::parse(
                            $request->data_inicio
                        )->startOfDay();


                    $dataFim =
                        Carbon::parse(
                            $request->data_fim
                        )->endOfDay();


                    $pedidoQuery->whereBetween(
                        'data_pedido',
                        [
                            $dataInicio,
                            $dataFim
                        ]
                    );

                }

                break;

        }


        /*
        |--------------------------------------------------------------------------
        | DADOS FINANCEIROS DOS PEDIDOS
        |--------------------------------------------------------------------------
        */

        $financeiro = (clone $pedidoQuery)

            ->selectRaw('

                COUNT(*) as pedidos,

                SUM(valor_produtos) as faturamento,

                SUM(valor_repasse) as valor_repasse,

                SUM(taxas_marketplace) as taxas_marketplace,

                SUM(custo_total) as custo_produtos,

                SUM(lucro_bruto) as lucro_bruto

            ')

            ->first();


        /*
        |--------------------------------------------------------------------------
        | VALORES
        |--------------------------------------------------------------------------
        */

        $totalPedidos =
            $financeiro->pedidos ?? 0;


        $faturamento =
            (float) (
                $financeiro->faturamento
                ?? 0
            );


        $valorRepasse =
            (float) (
                $financeiro->valor_repasse
                ?? 0
            );


        $taxasMarketplace =
            (float) (
                $financeiro->taxas_marketplace
                ?? 0
            );


        $custoProdutos =
            (float) (
                $financeiro->custo_produtos
                ?? 0
            );


        $lucroBruto =
            (float) (
                $financeiro->lucro_bruto
                ?? 0
            );


        /*
        |--------------------------------------------------------------------------
        | SHOPEE ADS
        |--------------------------------------------------------------------------
        |
        | O serviço recebe exatamente o mesmo período
        | utilizado no filtro dos pedidos.
        |
        */

        $adsMetricas =
            $this->shopeeAdsService->getInvestimento(
                $dataInicio,
                $dataFim
            );


        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS ADS
        |--------------------------------------------------------------------------
        */

        $ads =
            (float) (
                $adsMetricas['investimento']
                ?? 0
            );


        $adsGmv =
            (float) (
                $adsMetricas['gmv']
                ?? 0
            );


        $adsRoas =
            (float) (
                $adsMetricas['roas']
                ?? 0
            );


        $adsImpressions =
            (int) (
                $adsMetricas['impressions']
                ?? 0
            );


        $adsClicks =
            (int) (
                $adsMetricas['clicks']
                ?? 0
            );


        $adsPedidos =
            (int) (
                $adsMetricas['pedidos']
                ?? 0
            );


        /*
        |--------------------------------------------------------------------------
        | ADS / FATURAMENTO
        |--------------------------------------------------------------------------
        */

        $percentualAds =

            $faturamento > 0

                ?

                round(
                    (
                        $ads
                        /
                        $faturamento
                    ) * 100,
                    2
                )

                :

                0;


        /*
        |--------------------------------------------------------------------------
        | CUSTOS OPERACIONAIS
        |--------------------------------------------------------------------------
        */

        $custoOperacional = 0;


        /*
        |--------------------------------------------------------------------------
        | LUCRO LÍQUIDO
        |--------------------------------------------------------------------------
        */

        $lucroLiquido =

            $lucroBruto

            -

            $ads

            -

            $custoOperacional;


        /*
        |--------------------------------------------------------------------------
        | MARGEM
        |--------------------------------------------------------------------------
        */

        $margem =

            $faturamento > 0

                ?

                round(
                    (
                        $lucroLiquido
                        /
                        $faturamento
                    ) * 100,
                    2
                )

                :

                0;


        /*
        |--------------------------------------------------------------------------
        | TICKET MÉDIO
        |--------------------------------------------------------------------------
        */

        $ticketMedio =

            $totalPedidos > 0

                ?

                round(
                    $faturamento
                    /
                    $totalPedidos,
                    2
                )

                :

                0;


        /*
        |--------------------------------------------------------------------------
        | CARDS DASHBOARD
        |--------------------------------------------------------------------------
        */

        $cards = [

            /*
            |--------------------------------------------------------------------------
            | Vendas
            |--------------------------------------------------------------------------
            */

            'pedidos' =>
                $totalPedidos,


            'faturamento' =>
                round(
                    $faturamento,
                    2
                ),


            'taxas_marketplace' =>
                round(
                    $taxasMarketplace,
                    2
                ),


            'valor_liquido' =>
                round(
                    $valorRepasse,
                    2
                ),


            'custo_produtos' =>
                round(
                    $custoProdutos,
                    2
                ),


            /*
            |--------------------------------------------------------------------------
            | Custos
            |--------------------------------------------------------------------------
            */

            'custo_operacional' =>
                round(
                    $custoOperacional,
                    2
                ),


            'ads' =>
                round(
                    $ads,
                    2
                ),


            /*
            |--------------------------------------------------------------------------
            | Lucros
            |--------------------------------------------------------------------------
            */

            'lucro_bruto' =>
                round(
                    $lucroBruto,
                    2
                ),


            'lucro_liquido' =>
                round(
                    $lucroLiquido,
                    2
                ),


            'margem' =>
                $margem,


            /*
            |--------------------------------------------------------------------------
            | Indicadores
            |--------------------------------------------------------------------------
            */

            'ticket_medio' =>
                $ticketMedio,


            /*
            |--------------------------------------------------------------------------
            | SHOPEE ADS
            |--------------------------------------------------------------------------
            */

            'ads_investimento' =>
                round(
                    $ads,
                    2
                ),


            'ads_gmv' =>
                round(
                    $adsGmv,
                    2
                ),


            'ads_roas' =>
                round(
                    $adsRoas,
                    2
                ),


            'ads_impressions' =>
                $adsImpressions,


            'ads_clicks' =>
                $adsClicks,


            'ads_pedidos' =>
                $adsPedidos,


            'percentual_ads' =>
                $percentualAds,

        ];


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(

            'shopee.dashboard.dashboard',

            compact(
                'cards',
                'periodo'
            )

        );

    }

}