@extends('layouts.app')

@section('title', 'Dashboard Financeiro')

@section('content')

<div class="container-fluid p-2 p-sm-3">

    {{-- =========================================================
         CABEÇALHO
         ========================================================= --}}

    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">

        <div>

            <h4 class="fw-bold mb-0 text-dark">

                <i class="bi bi-speedometer2 me-1"></i>

                Painel Financeiro

            </h4>

            <span class="text-muted small">

                Resumo das movimentações e faturamento

            </span>

        </div>


        <a
            href="{{ route('contas.create') }}"
            class="btn btn-primary fw-bold shadow-sm py-2 px-3"
        >

            <i class="bi bi-plus-circle me-1"></i>

            Novo Lançamento

        </a>

    </div>


    {{-- =========================================================
         ALERTAS
         ========================================================= --}}

    @if(session('sucesso'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle me-1"></i>

            {{ session('sucesso') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Verifique os dados:</strong>

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================================
         CARDS
         ========================================================= --}}

    <div class="row g-2 g-sm-3 mb-3">


        {{-- SALDO --}}

        <div class="col-12 col-sm-6 col-lg-3">

            <div class="card shadow-sm border-0 border-start border-4 border-primary rounded-3 h-100">

                <div class="card-body p-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <span class="text-uppercase small fw-bold text-muted">

                                Saldo Atual

                            </span>

                            <h3 class="fw-bold text-dark mb-0 mt-1">

                                R$
                                {{ number_format($saldoAtual ?? 0, 2, ',', '.') }}

                            </h3>

                        </div>

                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">

                            <i class="bi bi-wallet2 fs-3"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- RECEBIDO --}}

        <div class="col-6 col-lg-3">

            <a
                href="{{ route('dashboard', ['filtro' => 'recebido', 'periodo' => $periodo]) }}"
                class="text-decoration-none"
            >

                <div class="card shadow-sm border-0 border-start border-4 border-success rounded-3 h-100 dashboard-card">

                    <div class="card-body p-3">

                        <span class="text-uppercase small fw-bold text-muted">

                            Recebido (Mês)

                        </span>

                        <h4 class="fw-bold text-success mb-0 mt-1">

                            R$
                            {{ number_format($totalRecebidoMensal ?? 0, 2, ',', '.') }}

                        </h4>

                    </div>

                </div>

            </a>

        </div>


        {{-- PAGO --}}

        <div class="col-6 col-lg-3">

            <a
                href="{{ route('dashboard', ['filtro' => 'pago', 'periodo' => $periodo]) }}"
                class="text-decoration-none"
            >

                <div class="card shadow-sm border-0 border-start border-4 border-danger rounded-3 h-100 dashboard-card">

                    <div class="card-body p-3">

                        <span class="text-uppercase small fw-bold text-muted">

                            Pago (Mês)

                        </span>

                        <h4 class="fw-bold text-danger mb-0 mt-1">

                            R$
                            {{ number_format($totalPagoMensal ?? 0, 2, ',', '.') }}

                        </h4>

                    </div>

                </div>

            </a>

        </div>


        {{-- PENDENTE --}}

        <div class="col-12 col-sm-6 col-lg-3">

            <a
                href="{{ route('dashboard', ['filtro' => 'pendente', 'periodo' => $periodo]) }}"
                class="text-decoration-none"
            >

                <div class="card shadow-sm border-0 border-start border-4 border-warning rounded-3 h-100 dashboard-card">

                    <div class="card-body p-3">

                        <span class="text-uppercase small fw-bold text-muted">

                            Pendente no Mês

                        </span>

                        <h4 class="fw-bold text-warning mb-0 mt-1">

                            R$
                            {{ number_format($totalPendenteMensal ?? 0, 2, ',', '.') }}

                        </h4>

                    </div>

                </div>

            </a>

        </div>

    </div>


    {{-- =========================================================
         GRÁFICO FINANCEIRO
         ========================================================= --}}

    <div class="card shadow-sm border-0 rounded-3 mb-4">

        <div class="card-header bg-transparent border-0 pt-3 px-3">

            <div class="d-flex flex-column flex-md-row justify-content-between gap-2">

                <div>

                    <h6 class="fw-bold text-uppercase small text-muted mb-1">

                        <i class="bi bi-bar-chart-line me-1"></i>

                        Movimentação Financeira

                    </h6>

                    <span class="text-muted small">

                        Clique em um dia para visualizar os lançamentos

                    </span>

                </div>


                {{-- CONTROLES --}}

                <div class="d-flex gap-1">

                    <a
                        href="{{ route('dashboard', ['periodo' => 'dia', 'data' => $dataSelecionada->format('Y-m-d')]) }}"
                        class="btn btn-sm {{ $periodo === 'dia' ? 'btn-primary' : 'btn-outline-primary' }}"
                    >

                        Dia

                    </a>


                    <a
                        href="{{ route('dashboard', ['periodo' => 'semana', 'data' => $dataSelecionada->format('Y-m-d')]) }}"
                        class="btn btn-sm {{ $periodo === 'semana' ? 'btn-primary' : 'btn-outline-primary' }}"
                    >

                        Semana

                    </a>


                    <a
                        href="{{ route('dashboard', ['periodo' => 'mes', 'data' => $dataSelecionada->format('Y-m-d')]) }}"
                        class="btn btn-sm {{ $periodo === 'mes' ? 'btn-primary' : 'btn-outline-primary' }}"
                    >

                        Mês

                    </a>

                </div>

            </div>

        </div>


        <div class="card-body">

            <div style="height: 320px;">

                <canvas id="graficoFinanceiro"></canvas>

            </div>

        </div>

    </div>


    {{-- =========================================================
         RESUMO DO PERÍODO
         ========================================================= --}}

    <div class="row g-3 mb-4">


        {{-- ENTRADAS --}}

        <div class="col-12 col-md-4">

            <div class="card shadow-sm border-0 border-start border-4 border-success h-100">

                <div class="card-body">

                    <span class="text-muted small text-uppercase fw-bold">

                        Entradas

                    </span>

                    <h4 class="text-success fw-bold mb-0">

                        + R$
                        {{ number_format($entradasDataSelecionada, 2, ',', '.') }}

                    </h4>

                </div>

            </div>

        </div>


        {{-- SAÍDAS --}}

        <div class="col-12 col-md-4">

            <div class="card shadow-sm border-0 border-start border-4 border-danger h-100">

                <div class="card-body">

                    <span class="text-muted small text-uppercase fw-bold">

                        Saídas

                    </span>

                    <h4 class="text-danger fw-bold mb-0">

                        - R$
                        {{ number_format($saidasDataSelecionada, 2, ',', '.') }}

                    </h4>

                </div>

            </div>

        </div>


        {{-- SALDO DO PERÍODO --}}

        <div class="col-12 col-md-4">

            <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">

                <div class="card-body">

                    <span class="text-muted small text-uppercase fw-bold">

                        Saldo do Período

                    </span>

                    <h4 class="fw-bold mb-0
                        {{ ($entradasDataSelecionada - $saidasDataSelecionada) >= 0
                            ? 'text-primary'
                            : 'text-danger' }}">

                        R$
                        {{ number_format(
                            $entradasDataSelecionada - $saidasDataSelecionada,
                            2,
                            ',',
                            '.'
                        ) }}

                    </h4>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         LANÇAMENTOS DO PERÍODO
         ========================================================= --}}

    <div class="card shadow-sm border-0 rounded-3 mb-4">

        <div class="card-header bg-transparent border-0 pt-3 px-3">

            <h6 class="fw-bold text-uppercase small text-muted mb-0">

                <i class="bi bi-list-ul me-1"></i>

                Lançamentos do Período

            </h6>

        </div>


        <div class="card-body p-0">

            @if($lancamentosDataSelecionada->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th class="ps-3">Tipo</th>

                                <th>Descrição</th>

                                <th>Data</th>

                                <th>Status</th>

                                <th class="text-end pe-3">Valor</th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($lancamentosDataSelecionada as $lancamento)

                                <tr>

                                    <td class="ps-3">

                                        @if($lancamento->tipo === 'receber')

                                            <span class="badge bg-success-subtle text-success">

                                                <i class="bi bi-arrow-down"></i>

                                                Entrada

                                            </span>

                                        @else

                                            <span class="badge bg-danger-subtle text-danger">

                                                <i class="bi bi-arrow-up"></i>

                                                Saída

                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        {{ $lancamento->descricao ?: 'Sem descrição' }}

                                    </td>


                                    <td>

                                        {{ \Carbon\Carbon::parse($lancamento->data_vencimento)->format('d/m/Y') }}

                                    </td>


                                    <td>

                                        @if($lancamento->status === 'pago')

                                            <span class="badge bg-success">

                                                Pago

                                            </span>

                                        @else

                                            <span class="badge bg-warning text-dark">

                                                Pendente

                                            </span>

                                        @endif

                                    </td>


                                    <td class="text-end pe-3 fw-bold
                                        {{ $lancamento->tipo === 'receber'
                                            ? 'text-success'
                                            : 'text-danger' }}">

                                        {{ $lancamento->tipo === 'receber' ? '+' : '-' }}

                                        R$
                                        {{ number_format($lancamento->valor, 2, ',', '.') }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="p-4 text-center text-muted">

                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>

                    Nenhum lançamento encontrado neste período.

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
         GRÁFICO DE ORIGENS
         ========================================================= --}}

    <div class="card shadow-sm border-0 rounded-3 mb-4">

        <div class="card-header bg-transparent border-0">

            <h6 class="fw-bold text-uppercase small text-muted mb-0">

                <i class="bi bi-pie-chart me-1"></i>

                Faturamento por Serviço/Origem

            </h6>

        </div>


        <div class="card-body">

            <div style="height: 260px;">

                <canvas id="graficoOrigens"></canvas>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ÚLTIMAS MOVIMENTAÇÕES
         ========================================================= --}}

    <div class="card shadow-sm border-0 rounded-3 mb-4">

        <div class="card-header bg-transparent border-0">

            <h6 class="fw-bold text-uppercase small text-muted mb-0">

                <i class="bi bi-clock-history me-1"></i>

                Últimas Movimentações

            </h6>

        </div>


        <div class="card-body p-0">

            @if($ultimosLancamentos->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th class="ps-3">Tipo</th>

                                <th>Descrição</th>

                                <th>Data</th>

                                <th>Status</th>

                                <th class="text-end pe-3">Valor</th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($ultimosLancamentos as $lancamento)

                                <tr>

                                    <td class="ps-3">

                                        @if($lancamento->tipo === 'receber')

                                            <span class="badge bg-success-subtle text-success">

                                                Entrada

                                            </span>

                                        @else

                                            <span class="badge bg-danger-subtle text-danger">

                                                Saída

                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        {{ $lancamento->descricao ?: 'Sem descrição' }}

                                    </td>


                                    <td>

                                        {{ \Carbon\Carbon::parse($lancamento->data_vencimento)->format('d/m/Y') }}

                                    </td>


                                    <td>

                                        @if($lancamento->status === 'pago')

                                            <span class="badge bg-success">

                                                Pago

                                            </span>

                                        @else

                                            <span class="badge bg-warning text-dark">

                                                Pendente

                                            </span>

                                        @endif

                                    </td>


                                    <td class="text-end pe-3 fw-bold
                                        {{ $lancamento->tipo === 'receber'
                                            ? 'text-success'
                                            : 'text-danger' }}">

                                        {{ $lancamento->tipo === 'receber' ? '+' : '-' }}

                                        R$
                                        {{ number_format($lancamento->valor, 2, ',', '.') }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="p-4 text-center text-muted">

                    Nenhuma movimentação encontrada.

                </div>

            @endif

        </div>

    </div>

</div>


{{-- =============================================================
     CHART.JS
     ============================================================= --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const canvas = document.getElementById('graficoFinanceiro');

    if (!canvas) {
        return;
    }


    const labels = @json($labelsGrafico);

    const entradas = @json($entradasGrafico);

    const saidas = @json($saidasGrafico);

    const periodo = @json($periodo);

    const dataSelecionada = @json($dataSelecionada->format('Y-m-d'));


    const chart = new Chart(canvas, {

        type: 'bar',

        data: {

            labels: labels,

            datasets: [

                {

                    label: 'Entradas',

                    data: entradas,

                    borderWidth: 1,

                    borderRadius: 5

                },

                {

                    label: 'Saídas',

                    data: saidas,

                    borderWidth: 1,

                    borderRadius: 5

                }

            ]

        },


        options: {

            responsive: true,

            maintainAspectRatio: false,


            interaction: {

                mode: 'index',

                intersect: false

            },


            plugins: {

                legend: {

                    position: 'top'

                },


                tooltip: {

                    callbacks: {

                        label: function(context) {

                            return context.dataset.label + ': R$ ' +

                                Number(context.raw).toLocaleString(
                                    'pt-BR',
                                    {
                                        minimumFractionDigits: 2
                                    }
                                );

                        }

                    }

                }

            },


            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        callback: function(value) {

                            return 'R$ ' +

                                Number(value).toLocaleString(
                                    'pt-BR'
                                );

                        }

                    }

                }

            },


            onClick: function(event, elements) {

                if (!elements.length) {
                    return;
                }


                const indice = elements[0].index;


                /*
                 * No modo MÊS precisamos descobrir
                 * o dia correspondente.
                 */

                let data;


                if (periodo === 'dia') {

                    data = dataSelecionada;

                }

                else if (periodo === 'semana') {

                    const selecionada = new Date(
                        dataSelecionada + 'T00:00:00'
                    );

                    const diaSemana = selecionada.getDay();

                    const segunda = new Date(selecionada);

                    const diferenca =
                        diaSemana === 0
                            ? -6
                            : 1 - diaSemana;

                    segunda.setDate(
                        selecionada.getDate() + diferenca
                    );

                    segunda.setDate(
                        segunda.getDate() + indice
                    );

                    data =
                        segunda.getFullYear() +
                        '-' +
                        String(
                            segunda.getMonth() + 1
                        ).padStart(2, '0') +
                        '-' +
                        String(
                            segunda.getDate()
                        ).padStart(2, '0');

                }

                else {

                    /*
                     * Mês
                     *
                     * O índice representa o dia do mês.
                     */

                    const ano =
                        dataSelecionada.substring(0, 4);

                    const mes =
                        dataSelecionada.substring(5, 7);

                    data =
                        ano +
                        '-' +
                        mes +
                        '-' +
                        String(indice + 1).padStart(2, '0');

                }


                /*
                 * Recarrega a dashboard mostrando
                 * os lançamentos daquela data.
                 */

                const url = new URL(
                    window.location.href
                );

                url.searchParams.set(
                    'periodo',
                    'dia'
                );

                url.searchParams.set(
                    'data',
                    data
                );

                url.searchParams.delete(
                    'filtro'
                );


                window.location.href =
                    url.toString();

            }

        }

    });


    // =========================================================
    // GRÁFICO DE ORIGENS
    // =========================================================

    const canvasOrigens =
        document.getElementById('graficoOrigens');


    if (canvasOrigens) {

        new Chart(canvasOrigens, {

            type: 'doughnut',

            data: {

                labels: [

                    'Corte / Barba',

                    'Combos / Planos',

                    'Outros'

                ],

                datasets: [

                    {

                        data: [

                            {{ $faturamentoOrigem['corte_barba'] ?? 0 }},

                            {{ $faturamentoOrigem['combos_pacotes'] ?? 0 }},

                            {{ $faturamentoOrigem['outros'] ?? 0 }}

                        ],

                        borderWidth: 2

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {

                        position: 'bottom'

                    }

                }

            }

        });

    }

});

</script>


<style>

.dashboard-card {

    transition:
        transform .15s ease-in-out,
        box-shadow .15s ease-in-out;

}


.dashboard-card:hover {

    transform: translateY(-3px);

    box-shadow:
        0 .5rem 1rem rgba(0, 0, 0, .12) !important;

}


.table-responsive {

    overflow-x: auto;

}


@media (max-width: 575px) {

    .table {

        font-size: 13px;

    }

}

</style>

@endsection