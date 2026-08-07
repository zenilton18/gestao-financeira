@extends('layouts.app')

@section('title', 'Novo Lançamento')

@section('content')
<div class="container p-2 p-sm-3">
    
    {{-- Cabeçalho Simples --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0 text-dark">
            <i class="bi bi-plus-circle me-1"></i> Novo Lançamento
        </h4>
        {{-- <a href="{{ route('financeiro.cadastro') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Voltar
        </a> --}}
    </div>

    @if(session('sucesso'))
        <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('sucesso') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-3 p-md-4">
            
            <form action="{{ route('contas.store') }}" method="POST" id="formLancamento">
                @csrf

                {{-- 1. Tipo de Lançamento (Botões Grandes estilo Toggle para Celular) --}}
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Tipo de Operação</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="tipo" id="tipo_receber" value="receber" {{ old('tipo', 'receber') == 'receber' ? 'checked' : '' }}>
                            <label class="btn btn-outline-success w-100 py-3 fw-bold shadow-sm d-flex flex-column align-items-center" for="tipo_receber">
                                <i class="bi bi-arrow-down-circle fs-3 mb-1"></i>
                                <span>Entrada (Receber)</span>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="tipo" id="tipo_pagar" value="pagar" {{ old('tipo') == 'pagar' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger w-100 py-3 fw-bold shadow-sm d-flex flex-column align-items-center" for="tipo_pagar">
                                <i class="bi bi-arrow-up-circle fs-3 mb-1"></i>
                                <span>Saída (Pagar)</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 2. Centro de Custo / Origem (Exibido apenas quando for Entrada) --}}
                <div class="mb-3" id="campo_centro_custo">
                    <label for="centro_custo" class="form-label fw-bold small text-muted text-uppercase">
                        Origem da Entrada
                    </label>
                    <select name="centro_custo" id="centro_custo" class="form-select form-select-lg">
                        <option value="corte_barba" {{ old('centro_custo') == 'corte_barba' ? 'selected' : '' }}>✂️ Corte / Barba</option>
                        <option value="venda_produtos" {{ old('centro_custo') == 'venda_produtos' ? 'selected' : '' }}>🧴 Venda de Produtos (Pomada, Óleo, etc.)</option>
                        <option value="combos_pacotes" {{ old('centro_custo') == 'combos_pacotes' ? 'selected' : '' }}>💈 Combos / Planos Mensais</option>
                        <option value="outros" {{ old('centro_custo') == 'outros' ? 'selected' : '' }}>➕ Outros / Diversos</option>
                    </select>
                </div>

                {{-- 3. Descrição --}}
                <div class="mb-3">
                    <label for="descricao" class="form-label fw-bold small text-muted text-uppercase">Descrição / Cliente</label>
                    <input type="text" name="descricao" id="descricao" class="form-control form-control-lg" placeholder="Ex: Pagamento material de limpeza" value="{{ old('descricao') }}" >
                </div>

                {{-- 4. Valor Total e Data de Vencimento --}}
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="valor_total" class="form-label fw-bold small text-muted text-uppercase">Valor Total (R$)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text fw-bold">R$</span>
                            <input type="number" step="0.01" min="0.01" name="valor_total" id="valor_total" class="form-control" placeholder="0,00" value="{{ old('valor_total') }}" required>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="data_vencimento" class="form-label fw-bold small text-muted text-uppercase">Data de Vencimento</label>
                        <input type="date" name="data_vencimento" id="data_vencimento" class="form-control form-control-lg" value="{{ old('data_vencimento', date('Y-m-d')) }}" required>
                    </div>
                    <div class="row g-2 mb-3">
    <div class="col-6">
        <label for="status" class="form-label fw-bold small text-muted text-uppercase">Status Inicial</label>
        <select name="status" id="status" class="form-select form-select-lg">
            <option value="pago" {{ old('status', 'pago') == 'pago' ? 'selected' : '' }}>✅ Pago / Recebido</option>
            <option value="pendente" {{ old('status') == 'pendente' ? 'selected' : '' }}>⏳ Pendente (A vencer)</option>
        </select>
    </div>

    <div class="col-6" id="campo_data_pagamento">
        <label for="data_pagamento" class="form-label fw-bold small text-muted text-uppercase">Data do Pagamento</label>
        <input type="date" name="data_pagamento" id="data_pagamento" class="form-control form-control-lg" value="{{ old('data_pagamento', date('Y-m-d')) }}">
    </div>
</div>

<script>
    // Esconde/mostra a data de pagamento se o status for pendente
    const selectStatus = document.getElementById('status');
    const campoDataPagamento = document.getElementById('campo_data_pagamento');

    selectStatus.addEventListener('change', function() {
        if (this.value === 'pago') {
            campoDataPagamento.style.display = 'block';
        } else {
            campoDataPagamento.style.display = 'none';
        }
    });
</script>

                </div>

                {{-- 5. Parcelamento / Repetição --}}
                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <label for="parcelas" class="form-label fw-bold small text-muted text-uppercase">Nº Parcelas</label>
                        <select name="parcelas" id="parcelas" class="form-select form-select-lg">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('parcelas', 1) == $i ? 'selected' : '' }}>
                                    {{ $i }}x {{ $i == 1 ? '(À vista)' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-6">
                        <label for="intervalo" class="form-label fw-bold small text-muted text-uppercase">Intervalo</label>
                        <select name="intervalo" id="intervalo" class="form-select form-select-lg">
                            <option value="diario" {{ old('intervalo') == 'diario' ? 'selected' : '' }}>Diário</option>
                            <option value="mensal" {{ old('intervalo') == 'mensal' ? 'selected' : '' }}>Mensal</option>
                            <option value="30_dias" {{ old('intervalo') == '30_dias' ? 'selected' : '' }}>30 Dias</option>
                            <option value="quinzenal" {{ old('intervalo') == 'quinzenal' ? 'selected' : '' }}>Quinzenal</option>
                            <option value="semanal" {{ old('intervalo') == 'semanal' ? 'selected' : '' }}>Semanal</option>
                        </select>
                    </div>
                </div>

                {{-- Botão de Salvar Grande (Fácil de Clicar no Celular) --}}
                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold text-uppercase shadow">
                    <i class="bi bi-check-lg me-1"></i> Salvar Lançamento
                </button>

            </form>

        </div>
    </div>
</div>

{{-- Script para ocultar/exibir o Centro de Custo e formatar inputs --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radioReceber = document.getElementById('tipo_receber');
        const radioPagar = document.getElementById('tipo_pagar');
        const campoCentroCusto = document.getElementById('campo_centro_custo');

        function alternarCentroCusto() {
            if (radioReceber.checked) {
                campoCentroCusto.style.display = 'block';
            } else {
                campoCentroCusto.style.display = 'none';
            }
        }

        radioReceber.addEventListener('change', alternarCentroCusto);
        radioPagar.addEventListener('change', alternarCentroCusto);

        // Executa ao carregar a página
        alternarCentroCusto();
    });
</script>
@endsection