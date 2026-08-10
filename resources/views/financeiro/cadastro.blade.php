@extends('layouts.app')

@section('title', 'Novo Lançamento')

@section('content')
<div class="container p-2 p-sm-3">
    
    {{-- Cabeçalho Simples --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0 text-dark">
            <i class="bi bi-plus-circle me-1"></i> Novo Lançamento
        </h4>
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

                {{-- 1. Tipo de Lançamento --}}
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

                {{-- 2. Centro de Custo / Origem --}}
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

                {{-- 2.1. Seleção de Produto e Quantidade (Exibido apenas em Venda de Produtos) --}}
                <div class="row g-2 mb-3" id="campo_produto" style="display: none;">
                    <div class="col-8 col-md-9">
                        <label for="produto_selecionado" class="form-label fw-bold small text-primary text-uppercase">
                            <i class="bi bi-box-seam me-1"></i> Produto
                        </label>
                        <select name="produto_id" id="produto_selecionado" class="form-select form-select-lg border-primary">
                            <option value="" disabled selected>Escolha o produto...</option>
                            <option value="pomada_modeladora" data-nome="Pomada Modeladora" data-valor="35.00">Pomada Modeladora - R$ 35,00</option>
                            <option value="oleo_barba" data-nome="Óleo para Barba" data-valor="40.00">Óleo para Barba - R$ 40,00</option>
                            <option value="shampoo_barba" data-nome="Shampoo Especial Barba" data-valor="45.00">Shampoo Especial para Barba - R$ 45,00</option>
                            <option value="minoxidil" data-nome="Tônico Minoxidil" data-valor="80.00">Tônico / Minoxidil - R$ 80,00</option>
                            <option value="cera_capilar" data-nome="Cera Capilar" data-valor="30.00">Cera Capilar - R$ 30,00</option>
                        </select>
                    </div>
                    <div class="col-4 col-md-3">
                        <label for="quantidade_produto" class="form-label fw-bold small text-primary text-uppercase">Qtd</label>
                        <input type="number" name="quantidade" id="quantidade_produto" class="form-control form-control-lg border-primary text-center" value="1" min="1" step="1">
                    </div>
                </div>

                {{-- 3. Descrição --}}
                <div class="mb-3">
                    <label for="descricao" class="form-label fw-bold small text-muted text-uppercase">Descrição / Cliente</label>
                    <input type="text" name="descricao" id="descricao" class="form-control form-control-lg" placeholder="Ex: Pagamento material de limpeza" value="{{ old('descricao') }}">
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
                </div>

                {{-- Status e Data de Pagamento --}}
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

                {{-- Botão de Salvar --}}
                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold text-uppercase shadow">
                    <i class="bi bi-check-lg me-1"></i> Salvar Lançamento
                </button>

            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radioReceber = document.getElementById('tipo_receber');
        const radioPagar = document.getElementById('tipo_pagar');
        const campoCentroCusto = document.getElementById('campo_centro_custo');
        const selectCentroCusto = document.getElementById('centro_custo');
        
        const campoProduto = document.getElementById('campo_produto');
        const selectProduto = document.getElementById('produto_selecionado');
        const inputQuantidade = document.getElementById('quantidade_produto');
        const inputDescricao = document.getElementById('descricao');
        const inputValor = document.getElementById('valor_total');
        
        const selectStatus = document.getElementById('status');
        const campoDataPagamento = document.getElementById('campo_data_pagamento');

        // Alterna a exibição do Centro de Custo e dos Produtos
        function atualizarVisibilidade() {
            if (radioReceber.checked) {
                campoCentroCusto.style.display = 'block';
                verificarProduto();
            } else {
                campoCentroCusto.style.display = 'none';
                campoProduto.style.display = 'none';
            }
        }

        function verificarProduto() {
            if (radioReceber.checked && selectCentroCusto.value === 'venda_produtos') {
                campoProduto.style.display = 'flex';
            } else {
                campoProduto.style.display = 'none';
            }
        }

        // Calcula o valor total e atualiza a descrição com base no produto e quantidade
        function calcularTotalProduto() {
            if (selectProduto.selectedIndex <= 0) return;

            const opcaoSelecionada = selectProduto.options[selectProduto.selectedIndex];
            const nomeProduto = opcaoSelecionada.getAttribute('data-nome');
            const precoUnitario = parseFloat(opcaoSelecionada.getAttribute('data-valor')) || 0;
            const qtd = parseInt(inputQuantidade.value) || 1;

            const total = (precoUnitario * qtd).toFixed(2);

            // Atualiza os inputs
            inputValor.value = total;
            inputDescricao.value = `Venda: ${qtd}x ${nomeProduto}`;
        }

        // Eventos para recálculo automático
        selectProduto.addEventListener('change', calcularTotalProduto);
        inputQuantidade.addEventListener('input', calcularTotalProduto);

        // Alterna exibição do campo de data de pagamento
        selectStatus.addEventListener('change', function() {
            campoDataPagamento.style.display = (this.value === 'pago') ? 'block' : 'none';
        });

        radioReceber.addEventListener('change', atualizarVisibilidade);
        radioPagar.addEventListener('change', atualizarVisibilidade);
        selectCentroCusto.addEventListener('change', verificarProduto);

        // Estado inicial da tela
        atualizarVisibilidade();
        campoDataPagamento.style.display = (selectStatus.value === 'pago') ? 'block' : 'none';
    });
</script>
@endsection