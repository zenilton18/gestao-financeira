<?php

namespace App\Services\Shopee;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ShopeeAdsService
{
    public function __construct(
        protected ShopeeApiService $api
    ) {
    }


    /**
     * Retorna todas as métricas do Shopee Ads
     * dentro do período informado.
     *
     * O resultado é armazenado em cache para evitar
     * excesso de chamadas à API da Shopee.
     */
    public function getInvestimento(
        Carbon $dataInicio,
        Carbon $dataFim
    ): array {

        $inicio = $dataInicio->copy()->startOfDay();

        $fim = $dataFim->copy()->startOfDay();


        /*
        |--------------------------------------------------------------------------
        | CHAVE DO CACHE
        |--------------------------------------------------------------------------
        */

        $cacheKey =
            'shopee_ads_' .
            $inicio->format('Y-m-d') .
            '_' .
            $fim->format('Y-m-d');


        /*
        |--------------------------------------------------------------------------
        | CACHE
        |--------------------------------------------------------------------------
        */

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(5),

            function () use ($inicio, $fim) {

                /*
                |--------------------------------------------------------------------------
                | APENAS UM DIA
                |--------------------------------------------------------------------------
                */

                if ($inicio->isSameDay($fim)) {

                    return $this->getInvestimentoDia(
                        $inicio
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | PERÍODO
                |--------------------------------------------------------------------------
                |
                | Para vários dias usamos a API diária.
                |
                | Dividimos em blocos de até 30 dias.
                |
                */

                $total =
                    $this->metricasVazias();


                $cursor =
                    $inicio->copy();


                while ($cursor->lte($fim)) {

                    $blocoFim =
                        $cursor
                            ->copy()
                            ->addDays(29);


                    if ($blocoFim->gt($fim)) {

                        $blocoFim =
                            $fim->copy();
                    }


                    $metricas =
                        $this->getInvestimentoPeriodo(
                            $cursor,
                            $blocoFim
                        );


                    $total =
                        $this->somarMetricas(
                            $total,
                            $metricas
                        );


                    $cursor =
                        $blocoFim
                            ->copy()
                            ->addDay();
                }


                return $this->finalizarMetricas(
                    $total
                );
            }
        );
    }


    /**
     * Consulta um único dia usando a API horária.
     */
    private function getInvestimentoDia(
        Carbon $data
    ): array {

        Log::info(
            '[ShopeeAdsService] Consultando Ads por hora',
            [
                'data' =>
                    $data->format('Y-m-d')
            ]
        );


        $response =
            $this->api->get(

                '/api/v2/ads/get_all_cpc_ads_hourly_performance',

                [

                    /*
                    |--------------------------------------------------------------------------
                    | A Shopee exige DD-MM-YYYY
                    |--------------------------------------------------------------------------
                    */

                    'performance_date' =>
                        $data->format('d-m-Y'),

                ]

            );


        if (
            isset($response['error']) &&
            $response['error']
        ) {

            Log::error(
                '[ShopeeAdsService] Erro na API de Ads',
                [
                    'response' =>
                        $response
                ]
            );


            throw new \Exception(
                $response['message']
                ??
                'Erro ao consultar Shopee Ads.'
            );
        }


        return $this->extrairMetricas(
            $response
        );
    }


    /**
     * Consulta período de múltiplos dias.
     */
    private function getInvestimentoPeriodo(
        Carbon $inicio,
        Carbon $fim
    ): array {

        Log::info(
            '[ShopeeAdsService] Consultando Ads por período',
            [
                'data_inicio' =>
                    $inicio->format('Y-m-d'),

                'data_fim' =>
                    $fim->format('Y-m-d'),
            ]
        );


        $response =
            $this->api->get(

                '/api/v2/ads/get_all_cpc_ads_daily_performance',

                [

                    /*
                    |--------------------------------------------------------------------------
                    | A Shopee exige DD-MM-YYYY
                    |--------------------------------------------------------------------------
                    */

                    'start_date' =>
                        $inicio->format('d-m-Y'),

                    'end_date' =>
                        $fim->format('d-m-Y'),

                ]

            );


        if (
            isset($response['error']) &&
            $response['error']
        ) {

            Log::error(
                '[ShopeeAdsService] Erro na API de Ads',
                [
                    'response' =>
                        $response
                ]
            );


            throw new \Exception(
                $response['message']
                ??
                'Erro ao consultar Shopee Ads.'
            );
        }


        return $this->extrairMetricas(
            $response
        );
    }


    /**
     * Extrai todas as métricas da resposta da Shopee.
     *
     * IMPORTANTE:
     *
     * Os nomes aqui são padronizados para serem
     * exatamente os mesmos utilizados pelo Controller.
     */
    private function extrairMetricas(
        array $response
    ): array {

        $metricas =
            $this->metricasVazias();


        $dados =
            $response['response']
            ?? [];


        foreach ($dados as $item) {

            /*
            |--------------------------------------------------------------------------
            | INVESTIMENTO
            |--------------------------------------------------------------------------
            */

            $metricas['investimento'] +=
                (float) (
                    $item['expense']
                    ?? 0
                );


            /*
            |--------------------------------------------------------------------------
            | IMPRESSÕES
            |--------------------------------------------------------------------------
            */

            $metricas['impressions'] +=
                (int) (
                    $item['impression']
                    ?? 0
                );


            /*
            |--------------------------------------------------------------------------
            | CLIQUES
            |--------------------------------------------------------------------------
            */

            $metricas['clicks'] +=
                (int) (
                    $item['clicks']
                    ?? 0
                );


            /*
            |--------------------------------------------------------------------------
            | PEDIDOS ADS
            |--------------------------------------------------------------------------
            |
            | Consideramos os pedidos diretos.
            |
            */

            $metricas['pedidos'] +=
                (float) (
                    $item['direct_order']
                    ?? 0
                );


            /*
            |--------------------------------------------------------------------------
            | GMV ADS
            |--------------------------------------------------------------------------
            |
            | direct_gmv representa o GMV diretamente
            | atribuído aos anúncios.
            |
            */

            $metricas['gmv'] +=
                (float) (
                    $item['direct_gmv']
                    ?? 0
                );


            /*
            |--------------------------------------------------------------------------
            | CONVERSÕES
            |--------------------------------------------------------------------------
            */

            $metricas['conversoes'] +=
                (float) (
                    $item['direct_conversions']
                    ?? 0
                );
        }


        return $this->finalizarMetricas(
            $metricas
        );
    }


    /**
     * Estrutura inicial das métricas.
     *
     * Os nomes precisam ser exatamente iguais
     * aos utilizados no ShopeeDashboardController.
     */
    private function metricasVazias(): array
    {

        return [

            'investimento' => 0,

            'gmv' => 0,

            'impressions' => 0,

            'clicks' => 0,

            'pedidos' => 0,

            'conversoes' => 0,

            'roas' => 0,

            'ctr' => 0,

        ];
    }


    /**
     * Soma métricas de diferentes períodos.
     */
    private function somarMetricas(
        array $total,
        array $metricas
    ): array {

        $total['investimento'] +=
            $metricas['investimento'];


        $total['gmv'] +=
            $metricas['gmv'];


        $total['impressions'] +=
            $metricas['impressions'];


        $total['clicks'] +=
            $metricas['clicks'];


        $total['pedidos'] +=
            $metricas['pedidos'];


        $total['conversoes'] +=
            $metricas['conversoes'];


        return $total;
    }


    /**
     * Calcula métricas derivadas.
     */
    private function finalizarMetricas(
        array $metricas
    ): array {

        /*
        |--------------------------------------------------------------------------
        | ROAS
        |--------------------------------------------------------------------------
        |
        | ROAS = GMV Ads / Investimento Ads
        |
        */

        if (
            $metricas['investimento'] > 0
        ) {

            $metricas['roas'] =

                $metricas['gmv']
                /
                $metricas['investimento'];

        } else {

            $metricas['roas'] =
                0;
        }


        /*
        |--------------------------------------------------------------------------
        | CTR
        |--------------------------------------------------------------------------
        |
        | CTR = Cliques / Impressões × 100
        |
        */

        if (
            $metricas['impressions'] > 0
        ) {

            $metricas['ctr'] =

                (
                    $metricas['clicks']
                    /
                    $metricas['impressions']
                )
                *
                100;

        } else {

            $metricas['ctr'] =
                0;
        }


        /*
        |--------------------------------------------------------------------------
        | ARREDONDAMENTO
        |--------------------------------------------------------------------------
        */

        $metricas['investimento'] =
            round(
                $metricas['investimento'],
                2
            );


        $metricas['gmv'] =
            round(
                $metricas['gmv'],
                2
            );


        $metricas['pedidos'] =
            round(
                $metricas['pedidos'],
                2
            );


        $metricas['conversoes'] =
            round(
                $metricas['conversoes'],
                4
            );


        $metricas['roas'] =
            round(
                $metricas['roas'],
                2
            );


        $metricas['ctr'] =
            round(
                $metricas['ctr'],
                2
            );


        return $metricas;
    }
}