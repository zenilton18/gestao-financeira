@extends('layouts.app')

@section('title', 'Dashboard Financeiro')

@section('content')
<div class="container p-2 p-sm-3">

    {{-- Cabeçalho da Dashboard --}}
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="bi bi-speedometer2 me-1"></i> Painel Financeiro
            </h4>
            <span class="text-muted small">Resumo das movimentações e faturamento</span>
        </div>

        {{-- Botão de Ação Rápida --}}
        <a href="{{ route('contas.create') }}" class="btn btn-primary fw-bold shadow-sm py-2 px-3">
            <i class="bi bi-plus-circle me-1"></i> Novo Lançamento
        </a>
    </div>

    {{-- 1. Cards de Resumo Rápido (KPIs Financeiros) --}}
    <div class="row g-2 g-sm-3 mb-3">
        {{-- Saldo Total --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 border-start border-4 border-primary rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase small fw-bold text-muted">Saldo Atual</span>
                            <h3 class="fw-bold text-dark mb-0 mt-1">
                                R$ {{ number_format($saldoAtual ?? 0, 2, ',', '.') }}
                            </h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                            <i class="bi bi-wallet2 fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Entradas do Mês --}}
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 border-start border-4 border-success rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase small fw-bold text-muted">Recebido (Mês)</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">
                                R$ {{ number_format($totalRecebidoMensal ?? 0, 2, ',', '.') }}
                            </h4>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success p-2 p-sm-3 rounded-circle d-none d-sm-block">
                            <i class="bi bi-arrow-down-circle fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saídas do Mês --}}
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 border-start border-4 border-danger rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase small fw-bold text-muted">Pago (Mês)</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">
                                R$ {{ number_format($totalPagoMensal ?? 0, 2, ',', '.') }}
                            </h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger p-2 p-sm-3 rounded-circle d-none d-sm-block">
                            <i class="bi bi-arrow-up-circle fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contas Pendentes --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 border-start border-4 border-warning rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase small fw-bold text-muted">Pendente no Mês</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">
                                R$ {{ number_format($totalPendenteMensal ?? 0, 2, ',', '.') }}
                            </h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                            <i class="bi bi-clock-history fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. NOVA SEÇÃO: Métricas de Vendas de Produtos --}}
    <div class="row g-2 g-sm-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-6">
            <div class="card shadow-sm border-0 border-start border-4 border-info rounded-3 h-100 bg-light-subtle">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase small fw-bold text-info">
                                <i class="bi bi-box-seam me-1"></i> Faturamento de Produtos (Mês)
                            </span>
                            <h4 class="fw-bold text-dark mb-0 mt-1">
                                R$ {{ number_format($produtosMetricas['faturamento_mensal'] ?? 0, 2, ',', '.') }}
                            </h4>
                            <span class="text-muted small">
                                Total de itens vendidos: <strong>{{ $produtosMetricas['itens_vendidos_mes'] ?? 0 }} un.</strong>
                            </span>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                            <i class="bi bi-cart-check fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-6">
            <div class="card shadow-sm border-0 border-start border-4 border-secondary rounded-3 h-100 bg-light-subtle">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase small fw-bold text-secondary">
                                <i class="bi bi-cart3 me-1"></i> Vendas de Produtos (Hoje)
                            </span>
                            <h4 class="fw-bold text-dark mb-0 mt-1">
                                R$ {{ number_format($produtosMetricas['faturamento_hoje'] ?? 0, 2, ',', '.') }}
                            </h4>
                            <span class="text-muted small">
                                Itens vendidos hoje: <strong>{{ $produtosMetricas['itens_vendidos_hoje'] ?? 0 }} un.</strong>
                            </span>
                        </div>
                        <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded-circle">
                            <i class="bi bi-bag-check fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Seção de Gráficos e Resumo do Dia --}}
    <div class="row g-3 mb-4">
        {{-- Gráfico de Faturamento por Origem (Serviços) --}}
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 px-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-uppercase small text-muted mb-0">
                        <i class="bi bi-pie-chart me-1"></i> Faturamento por Serviço/Origem
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-7">
                            <canvas id="graficoOrigens" style="max-height: 220px;"></canvas>
                        </div>
                        <div class="col-12 col-md-5">
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>✂️ Corte / Barba</span>
                                    <strong class="text-dark">R$ {{ number_format($faturamentoOrigem['corte_barba'] ?? 0, 2, ',', '.') }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>💈 Combos/Planos</span>
                                    <strong class="text-dark">R$ {{ number_format($faturamentoOrigem['combos_pacotes'] ?? 0, 2, ',', '.') }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>➕ Outros</span>
                                    <strong class="text-dark">R$ {{ number_format($faturamentoOrigem['outros'] ?? 0, 2, ',', '.') }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lançamentos do Dia / Atalho Rápido --}}
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 px-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-uppercase small text-muted mb-0">
                        <i class="bi bi-calendar-event me-1"></i> Resumo Financeiro de Hoje
                    </h6>
                    <span class="badge bg-light text-dark">{{ date('d/m/Y') }}</span>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <span class="text-muted small">Entradas Hoje:</span>
                        <strong class="text-success">+ R$ {{ number_format($entradasHoje ?? 0, 2, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <span class="text-muted small">Saídas Hoje:</span>
                        <strong class="text-danger">- R$ {{ number_format($saidasHoje ?? 0, 2, ',', '.') }}</strong>
                    </div>

                    <a href="{{ route('contas.create') }}" class="btn btn-outline-primary w-100 py-2 fw-bold text-uppercase small">
                        <i class="bi bi-plus-lg me-1"></i> Registrar Atendimento
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Tabela de Últimos Lançamentos --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-transparent border-0 pt-3 px-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold text-uppercase small text-muted mb-0">
                <i class="bi bi-clock-history me-1"></i> Últimas Movimentações
            </h6>
        </div>

        <div class="card-body p-0">
            @if(isset($ultimosLancamentos) && count($ultimosLancamentos) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase text-muted">
                            <tr>
                                <th class="ps-3">Tipo</th>
                                <th>Descrição / Origem</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosLancamentos as $lancamento)
                                <tr>
                                    <td class="ps-3">
                                        @if($lancamento->tipo == 'receber')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                <i class="bi bi-arrow-down me-1"></i> Entrada
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                                <i class="bi bi-arrow-up me-1"></i> Saída
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $lancamento->descricao ?? 'Sem descrição' }}</div>
                                        @if($lancamento->tipo == 'receber' && $lancamento->centro_custo)
                                            <span class="badge bg-light text-muted fw-normal">
                                                @switch($lancamento->centro_custo)
                                                    @case('corte_barba') ✂️ Corte/Barba @break
                                                    @case('venda_produtos') 🧴 Produtos @break
                                                    @case('combos_pacotes') 💈 Combos @break
                                                    @default ➕ Outros
                                                @endswitch
                                            </span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">
                                        {{ \Carbon\Carbon::parse($lancamento->data_vencimento)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @if($lancamento->status == 'pago')
                                            <span class="badge bg-success text-white">✅ Pago</span>
                                        @else
                                            <span class="badge bg-warning text-dark">⏳ Pendente</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3 fw-bold {{ $lancamento->tipo == 'receber' ? 'text-success' : 'text-danger' }}">
                                        {{ $lancamento->tipo == 'receber' ? '+' : '-' }} R$ {{ number_format($lancamento->valor, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                    <p class="mb-0">Nenhum lançamento registrado recentemente.</p>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Chart.js via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('graficoOrigens');

        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Corte / Barba', 'Combos / Planos', 'Outros'],
                    datasets: [{
                        data: [
                            {{ $faturamentoOrigem['corte_barba'] ?? 0 }},
                            {{ $faturamentoOrigem['combos_pacotes'] ?? 0 }},
                            {{ $faturamentoOrigem['outros'] ?? 0 }}
                        ],
                        backgroundColor: ['#198754', '#ffc107', '#6c757d'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    });
</script>
@endsection