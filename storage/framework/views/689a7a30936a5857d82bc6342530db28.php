<?php $__env->startSection('title', 'Novo Lançamento'); ?>

<?php $__env->startSection('content'); ?>
<?php if($errors->any()): ?>

    <div class="alert alert-danger">

        <ul class="mb-0">

            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $erro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <li><?php echo e($erro); ?></li>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </ul>

    </div>

<?php endif; ?>
<div x-data="financeiro()" x-init="init()">
    <div class="container-fluid py-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title mb-0">
                <i class="bi bi-cash-stack me-2"></i>
                Novo Lançamento Financeiro
            </h2>
        </div>

        <!-- LANÇAMENTO -->
        <div class="card box-lancamento">
            <div class="card-header">
                <i class="bi bi-wallet2 me-2"></i>
                Dados do Lançamento
            </div>

            <div class="card-body">
                <div class="bg-soft">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label">Data Emissão</label>
                            <input type="date" class="form-control" x-model="data_emissao" >
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Categoria</label>

                            <select class="form-select" x-model="categoria_id">
                                <option value="">Selecione</option>
                                <option value="1">Entrada</option>
                                <option value="2">Saída</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Observação</label>
                            <input type="text" class="form-control" x-model="observacao" placeholder="Ex: Compra de materiais">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Valor Total</label>
                            <input type="number" step="0.01" class="form-control" x-model="valor_total" placeholder="0,00">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Qtd Parcelas</label>
                            <input type="number" min="1" class="form-control" x-model.number="quantidadeParcelas" @input="gerarParcelas()">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PARCELAS -->
        <!-- PARCELAS -->
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Parcela</th>
                <th>Vencimento</th>
                <th>Valor</th>
                <th>Situação</th>
                <th class="text-end">Ação</th>
            </tr>
        </thead>

        <!-- O template envolve os tbodies. O navegador aceita perfeitamente múltiplos tbodies em uma tabela -->
        <template x-for="(parcela, index) in parcelas" :key="index">
            <tbody class="border-top-0">
                <!-- LINHA DA PARCELA -->
                <tr>
                    <td>
                        <span class="badge bg-primary">
                            <span x-text="(index + 1) + '/' + quantidadeParcelas"></span>
                        </span>
                    </td>

                    <td>
                        <input type="date" class="form-control" x-model="parcela.vencimento">
                    </td>

                    <td>
                        <input type="number" step="0.01" class="form-control" x-model="parcela.valor">
                    </td>

                    <td>
                        <select class="form-select" x-model="parcela.situacao">
                            <option value="1">Aberto</option>
                            <option value="2">Pago</option>
                        </select>
                    </td>

                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary" @click="alternarBotaoPagamento(index)">
                            <i class="bi bi-credit-card"></i> Pagamento
                        </button>
                    </td>
                </tr>

                <!-- DADOS PAGAMENTO -->
                <tr x-show="parcela.situacao === '2' || parcela.mostrarPagamento" x-transition>
                    <td colspan="5" class="border-0">
                        <div class="card bg-light mt-2 mb-3 shadow-sm">
                            <div class="card-header bg-secondary bg-opacity-10">
                                <i class="bi bi-credit-card me-2"></i>
                                Dados do Pagamento da Parcela
                            </div>

                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Data Pagamento</label>
                                        <input type="date" class="form-control" x-model="parcela.data_pagamento">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Valor Pago</label>
                                        <input type="number" step="0.01" class="form-control" x-model="parcela.valor_pago">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Forma Pagamento</label>
                                        <select class="form-select" x-model="parcela.forma_pagamento">
                                            <option value="">Selecione</option>
                                            <option value="pix">PIX</option>
                                            <option value="boleto">Boleto</option>
                                            <option value="dinheiro">Dinheiro</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </template>
    </table>

    </div>
    <div class="d-flex justify-content-end mt-3">
        <button type="button" class="btn btn-success px-4" @click="salvar()">
            <i class="bi bi-check-circle me-2"></i> Gravar Lançamento
        </button>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\gestao-financeira\resources\views\cadastroLancamento.blade.php ENDPATH**/ ?>