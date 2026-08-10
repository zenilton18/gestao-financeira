<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;


class ShopeeDashboardController extends Controller
{

    public function index(Request $request)
    {

        /*
        |--------------------------------------------------------------------------
        | FILTROS
        |--------------------------------------------------------------------------
        */

        $periodo = $request->get('periodo', 'today');



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
        | FILTRO PERÍODO
        |--------------------------------------------------------------------------
        */

        switch ($periodo) {


            case 'today':

                $pedidoQuery->whereDate(
                    'data_pedido',
                    today()
                );

                break;



            case 'yesterday':

                $pedidoQuery->whereDate(
                    'data_pedido',
                    today()->subDay()
                );

                break;



            case '30':

                $pedidoQuery->where(
                    'data_pedido',
                    '>=',
                    now()->subDays(30)
                );

                break;



            case 'month':

                $pedidoQuery->whereBetween(
                    'data_pedido',
                    [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ]
                );

                break;



            case 'last_month':

                $pedidoQuery->whereBetween(
                    'data_pedido',
                    [
                        now()->subMonth()->startOfMonth(),
                        now()->subMonth()->endOfMonth()
                    ]
                );

                break;


            case 'custom':

                break;

        }



        /*
        |--------------------------------------------------------------------------
        | DADOS FINANCEIROS REAIS
        |--------------------------------------------------------------------------
        */

        $financeiro = (clone $pedidoQuery)

            ->selectRaw('

                COUNT(*) as pedidos,

                SUM(valor_total) as faturamento,

                SUM(valor_repasse) as valor_repasse,

                SUM(taxas_marketplace) as taxas_marketplace,

                SUM(custo_total) as custo_produtos,

                SUM(lucro_bruto) as lucro_bruto

            ')

            ->first();

            



        $totalPedidos =
            $financeiro->pedidos ?? 0;



        $faturamento =
            (float) ($financeiro->faturamento ?? 0);



        $valorRepasse =
            (float) ($financeiro->valor_repasse ?? 0);



        $taxasMarketplace =
            (float) ($financeiro->taxas_marketplace ?? 0);



        $custoProdutos =
            (float) ($financeiro->custo_produtos ?? 0);



        $lucroBruto =
            (float) ($financeiro->lucro_bruto ?? 0);



        /*
        |--------------------------------------------------------------------------
        | DESPESAS EXTRAS
        |--------------------------------------------------------------------------
        */

        $ads = 0;


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
        | CARDS DASHBOARD
        |--------------------------------------------------------------------------
        */

        $cards = [


            'pedidos' => $totalPedidos,



            'faturamento' => round(
                $faturamento,
                2
            ),



            'taxas_marketplace' => round(
                $taxasMarketplace,
                2
            ),



            'valor_liquido' => round(
                $valorRepasse,
                2
            ),



            'custo_produtos' => round(
                $custoProdutos,
                2
            ),



            'custo_operacional' => round(
                $custoOperacional,
                2
            ),



            'ads' => $ads,



            'lucro_bruto' => round(
                $lucroBruto,
                2
            ),



            'lucro_liquido' => round(
                $lucroLiquido,
                2
            ),



            'margem' =>

                $faturamento > 0

                    ?

                    round(
                        ($lucroLiquido / $faturamento) * 100,
                        2
                    )

                    :

                    0,



            'ticket_medio' =>

                $totalPedidos > 0

                    ?

                    round(
                        $faturamento / $totalPedidos,
                        2
                    )

                    :

                    0,


        ];



        return view(

            'shopee.dashboard.dashboard',

            compact(
                'cards',
                'periodo'
            )

        );

    }

}