@extends('layouts.app')

@section('content')

@if(session('success'))

    <div class="alert alert-success shadow-sm">
        <i class="bi bi-check-circle"></i>
        {{ session('success') }}
    </div>

@endif


@if(session('error'))

    <div class="alert alert-danger shadow-sm">

        <i class="bi bi-exclamation-triangle"></i>

        {{ session('error') }}

    </div>

@endif



<div class="container-fluid">


    {{-- ========================================================= --}}
    {{-- CABEÇALHO --}}
    {{-- ========================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Dashboard Shopee
            </h2>

            <span class="text-muted">
                Visão financeira e operacional
            </span>

        </div>


        {{-- ===================================================== --}}
        {{-- FILTRO PERÍODO --}}
        {{-- ===================================================== --}}

        <form action="{{ route('shopee.dashboard') }}"
              method="GET"
              class="d-flex gap-2 align-items-center">

            <select name="periodo"
                    id="periodo"
                    class="form-select">

                <option value="today"
                    {{ request('periodo', 'today') == 'today' ? 'selected' : '' }}>
                    Hoje
                </option>

                <option value="yesterday"
                    {{ request('periodo') == 'yesterday' ? 'selected' : '' }}>
                    Ontem
                </option>

                <option value="week"
                    {{ request('periodo') == 'week' ? 'selected' : '' }}>
                    Esta semana
                </option>

                <option value="30"
                    {{ request('periodo') == '30' ? 'selected' : '' }}>
                    Últimos 30 dias
                </option>

                <option value="month"
                    {{ request('periodo') == 'month' ? 'selected' : '' }}>
                    Este mês
                </option>

                <option value="last_month"
                    {{ request('periodo') == 'last_month' ? 'selected' : '' }}>
                    Mês anterior
                </option>

                <option value="custom"
                    {{ request('periodo') == 'custom' ? 'selected' : '' }}>
                    Personalizado
                </option>

            </select>


            {{-- ================================================= --}}
            {{-- DATAS PERSONALIZADAS --}}
            {{-- ================================================= --}}

            <div id="datas-personalizadas"
                 class="gap-2"
                 style="display:none;">

                <input type="date"
                       id="data_inicio"
                       name="data_inicio"
                       class="form-control"
                       value="{{ request('data_inicio') }}">

                <input type="date"
                       id="data_fim"
                       name="data_fim"
                       class="form-control"
                       value="{{ request('data_fim') }}">

            </div>


            <button type="submit"
                    class="btn btn-primary">

                Filtrar

            </button>

        </form>

    </div>



    {{-- ========================================================= --}}
    {{-- CARDS FINANCEIROS --}}
    {{-- ========================================================= --}}

    <div class="row g-4">


        {{-- ===================================================== --}}
        {{-- FATURAMENTO --}}
        {{-- ===================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <small class="text-muted">
                        FATURAMENTO
                    </small>

                    <h2 class="fw-bold mt-2">

                        R$
                        {{ number_format(
                            $cards['faturamento'] ?? 0,
                            2,
                            ',',
                            '.'
                        ) }}

                    </h2>

                    <span class="text-muted">
                        Vendas realizadas
                    </span>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- CUSTO PRODUTOS --}}
        {{-- ===================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <small class="text-muted">
                        CUSTO PRODUTOS
                    </small>

                    <h2 class="fw-bold mt-2 text-danger">

                        R$
                        {{ number_format(
                            $cards['custo_produtos'] ?? 0,
                            2,
                            ',',
                            '.'
                        ) }}

                    </h2>

                    <span class="text-muted">
                        Mercadoria vendida
                    </span>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- INVESTIMENTO ADS --}}
        {{-- ===================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <small class="text-muted">
                        INVESTIMENTO EM ADS
                    </small>

                    <h2 class="fw-bold mt-2 text-danger">

                        R$
                        {{ number_format(
                            $cards['ads'] ?? 0,
                            2,
                            ',',
                            '.'
                        ) }}

                    </h2>

                    <span class="text-muted">
                        Publicidade Shopee
                    </span>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- TAXAS SHOPEE --}}
        {{-- ===================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <small class="text-muted">
                        TAXAS SHOPEE
                    </small>

                    <h2 class="fw-bold mt-2 text-warning">

                        R$
                        {{ number_format(
                            $cards['taxas_marketplace'] ?? 0,
                            2,
                            ',',
                            '.'
                        ) }}

                    </h2>

                    <span class="text-muted">
                        Comissão e taxas
                    </span>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- VALOR LÍQUIDO --}}
        {{-- ===================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <small class="text-muted">
                        VALOR LÍQUIDO
                    </small>

                    <h2 class="fw-bold mt-2 text-info">

                        R$
                        {{ number_format(
                            $cards['valor_liquido_estimado']
                                ?? $cards['valor_liquido']
                                ?? 0,
                            2,
                            ',',
                            '.'
                        ) }}

                    </h2>

                    <span class="text-muted">
                        Após taxas
                    </span>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- LUCRO BRUTO --}}
        {{-- ===================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <small class="text-muted">
                        LUCRO BRUTO
                    </small>

                    <h2 class="fw-bold mt-2 text-success">

                        R$
                        {{ number_format(
                            $cards['lucro_bruto'] ?? 0,
                            2,
                            ',',
                            '.'
                        ) }}

                    </h2>

                    <span class="text-muted">
                        Antes dos custos operacionais
                    </span>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- LUCRO LÍQUIDO --}}
        {{-- ===================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <small class="text-muted">
                        LUCRO LÍQUIDO
                    </small>

                    <h2 class="fw-bold mt-2 text-primary">

                        R$
                        {{ number_format(
                            $cards['lucro_liquido'] ?? 0,
                            2,
                            ',',
                            '.'
                        ) }}

                    </h2>

                    <span class="text-muted">
                        Resultado final
                    </span>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- MARGEM --}}
        {{-- ===================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <small class="text-muted">
                        MARGEM
                    </small>

                    <h2 class="fw-bold mt-2">

                        {{ number_format(
                            $cards['margem'] ?? 0,
                            2,
                            ',',
                            '.'
                        ) }}%

                    </h2>

                    <span class="text-success">
                        Rentabilidade
                    </span>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- PEDIDOS --}}
        {{-- ===================================================== --}}

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <small class="text-muted">
                        PEDIDOS
                    </small>

                    <h2 class="fw-bold mt-2">

                        {{ number_format(
                            $cards['pedidos'] ?? 0,
                            0,
                            ',',
                            '.'
                        ) }}

                    </h2>

                    <span class="text-muted">
                        Pedidos realizados
                    </span>

                </div>

            </div>

        </div>


    </div>



    {{-- ========================================================= --}}
    {{-- INDICADORES + DESEMPENHO ADS --}}
    {{-- ========================================================= --}}

    <div class="row mt-4 g-4">


        {{-- ===================================================== --}}
        {{-- INDICADORES --}}
        {{-- ===================================================== --}}

        <div class="col-lg-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-white">

                    <strong>
                        Indicadores
                    </strong>

                </div>


                <div class="card-body">

                    <table class="table mb-0">


                        {{-- Ticket médio --}}

                        <tr>

                            <td>
                                Ticket médio
                            </td>

                            <td class="text-end fw-bold">

                                R$
                                {{ number_format(
                                    $cards['ticket_medio'] ?? 0,
                                    2,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                        </tr>


                        {{-- Ads --}}

                        <tr>

                            <td>
                                Investimento Ads
                            </td>

                            <td class="text-end fw-bold text-danger">

                                R$
                                {{ number_format(
                                    $cards['ads'] ?? 0,
                                    2,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                        </tr>


                        {{-- Percentual Ads --}}

                        <tr>

                            <td>
                                Ads / Faturamento
                            </td>

                            <td class="text-end fw-bold">

                                {{ number_format(
                                    $cards['percentual_ads'] ?? 0,
                                    2,
                                    ',',
                                    '.'
                                ) }}%

                            </td>

                        </tr>


                        {{-- Custo operacional --}}

                        <tr>

                            <td>
                                Custo operacional
                            </td>

                            <td class="text-end fw-bold">

                                R$
                                {{ number_format(
                                    $cards['custo_operacional'] ?? 0,
                                    2,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                        </tr>


                        {{-- Margem líquida --}}

                        <tr>

                            <td>
                                Margem líquida
                            </td>

                            <td class="text-end fw-bold text-success">

                                {{ number_format(
                                    $cards['margem'] ?? 0,
                                    2,
                                    ',',
                                    '.'
                                ) }}%

                            </td>

                        </tr>


                    </table>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- DESEMPENHO ADS --}}
        {{-- ===================================================== --}}

        <div class="col-lg-8">

            <div class="card shadow-sm border-0 h-100">


                <div class="card-header bg-white">

                    <strong>
                        Desempenho Shopee Ads
                    </strong>

                </div>


                <div class="card-body">


                    <div class="row g-4">


                        {{-- Investimento --}}

                        <div class="col-md-4">

                            <div class="p-3 rounded bg-light">

                                <small class="text-muted">
                                    INVESTIMENTO
                                </small>

                                <h4 class="fw-bold text-danger mt-2">

                                    R$
                                    {{ number_format(
                                        $cards['ads'] ?? 0,
                                        2,
                                        ',',
                                        '.'
                                    ) }}

                                </h4>

                            </div>

                        </div>


                        {{-- GMV Ads --}}

                        <div class="col-md-4">

                            <div class="p-3 rounded bg-light">

                                <small class="text-muted">
                                    GMV ADS
                                </small>

                                <h4 class="fw-bold mt-2">

                                    R$
                                    {{ number_format(
                                        $cards['ads_gmv'] ?? 0,
                                        2,
                                        ',',
                                        '.'
                                    ) }}

                                </h4>

                            </div>

                        </div>


                        {{-- ROAS --}}

                        <div class="col-md-4">

                            <div class="p-3 rounded bg-light">

                                <small class="text-muted">
                                    ROAS
                                </small>

                                <h4 class="fw-bold text-success mt-2">

                                    {{ number_format(
                                        $cards['ads_roas'] ?? 0,
                                        2,
                                        ',',
                                        '.'
                                    ) }}

                                </h4>

                            </div>

                        </div>


                        {{-- Impressões --}}

                        <div class="col-md-4">

                            <div class="p-3 rounded bg-light">

                                <small class="text-muted">
                                    IMPRESSÕES
                                </small>

                                <h4 class="fw-bold mt-2">

                                    {{ number_format(
                                        $cards['ads_impressions'] ?? 0,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </h4>

                            </div>

                        </div>


                        {{-- Cliques --}}

                        <div class="col-md-4">

                            <div class="p-3 rounded bg-light">

                                <small class="text-muted">
                                    CLIQUES
                                </small>

                                <h4 class="fw-bold mt-2">

                                    {{ number_format(
                                        $cards['ads_clicks'] ?? 0,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </h4>

                            </div>

                        </div>


                        {{-- Pedidos Ads --}}

                        <div class="col-md-4">

                            <div class="p-3 rounded bg-light">

                                <small class="text-muted">
                                    PEDIDOS ADS
                                </small>

                                <h4 class="fw-bold mt-2">

                                    {{ number_format(
                                        $cards['ads_pedidos'] ?? 0,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </h4>

                            </div>

                        </div>


                    </div>


                </div>

            </div>

        </div>


    </div>



    {{-- ========================================================= --}}
    {{-- EVOLUÇÃO FINANCEIRA --}}
    {{-- ========================================================= --}}

    <div class="row mt-4">


        <div class="col-12">

            <div class="card shadow-sm border-0">


                <div class="card-header bg-white">

                    <strong>
                        Evolução financeira
                    </strong>

                </div>


                <div class="card-body">


                    <div style="
                        height:300px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        color:#999;
                    ">

                        Gráfico de vendas por período

                    </div>


                </div>

            </div>

        </div>


    </div>



</div>



{{-- ============================================================= --}}
{{-- JAVASCRIPT FILTRO PERSONALIZADO --}}
{{-- ============================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {


    const periodo =
        document.getElementById('periodo');


    const datas =
        document.getElementById(
            'datas-personalizadas'
        );


    const dataInicio =
        document.getElementById(
            'data_inicio'
        );


    const dataFim =
        document.getElementById(
            'data_fim'
        );


    if (
        !periodo ||
        !datas ||
        !dataInicio ||
        !dataFim
    ) {

        return;

    }


    function atualizarDatas() {


        const personalizado =
            periodo.value === 'custom';


        if (personalizado) {


            datas.style.display =
                'flex';


            dataInicio.disabled =
                false;


            dataFim.disabled =
                false;


        } else {


            datas.style.display =
                'none';


            dataInicio.disabled =
                true;


            dataFim.disabled =
                true;


        }

    }


    periodo.addEventListener(
        'change',
        atualizarDatas
    );


    atualizarDatas();


});

</script>


@endsection