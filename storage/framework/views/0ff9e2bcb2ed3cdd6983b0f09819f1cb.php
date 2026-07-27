

<?php $__env->startSection('content'); ?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3>Produtos Shopee</h3>

        <a
            href="<?php echo e(route('shopee.produtos.sincronizar')); ?>"
            class="btn btn-primary">

            Sincronizar Produtos

        </a>

    </div>

    <div class="card shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                <tr>

                    <th>Imagem</th>

                    <th>Produto</th>

                    <th>SKU</th>

                    <th>Marca</th>

                    <th>Preço</th>

                    <th>Estoque</th>

                    <th>Status</th>
                    <th>Ação</th>

                </tr>

                </thead>

                <tbody>

                <?php $__empty_1 = true; $__currentLoopData = $produtos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                    <tr>

                        <td width="80">

                            <img
                                src="<?php echo e($produto->imagem); ?>"
                                width="60"
                                class="rounded">

                        </td>

                        <td>

                            <strong><?php echo e($produto->nome); ?></strong>

                            <br>

                            <small>

                                Shopee ID:
                                <?php echo e($produto->shopee_item_id); ?>


                            </small>

                        </td>

                        <td>

                            <?php echo e($produto->sku); ?>


                        </td>

                        <td>

                            <?php echo e($produto->marca); ?>


                        </td>

                        <td>

                            <?php echo e(number_format($produto->preco_venda,2,',','.')); ?>


                        </td>

                        <td>

                            <?php echo e($produto->estoque_total); ?>


                        </td>

                        <td>

                            <?php echo e($produto->status); ?>


                        </td>
                        <td>
                            <a href="<?php echo e(route('shopee.produtos.editar',$produto->id)); ?>"
                            class="btn btn-primary">
                                Editar
                            </a>
                        </td>

                    </tr>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                    <tr>

                        <td colspan="7" class="text-center py-5">

                            Nenhum produto encontrado.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

    <div class="mt-3">

        <?php echo e($produtos->links()); ?>


    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\gestao-financeira\resources\views\shopee\produtos\listaProdutos.blade.php ENDPATH**/ ?>