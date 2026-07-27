<div class="card shadow-sm border-0">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                <tr>

                    <th>Pedido</th>

                    <th>Marketplace</th>

                    <th>Cliente</th>

                    <th>Data</th>

                    <th class="text-end">Venda</th>

                    <th class="text-end">Lucro</th>

                    <th class="text-center">Margem</th>

                    <th class="text-center">Status</th>

                    <th class="text-center">Ações</th>

                </tr>

                </thead>

                <tbody>

                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                    <tr>

                        <td>

                            <strong>

                                <?php echo e($order->shopee_order_id); ?>


                            </strong>

                        </td>

                        <td>

                            <span class="badge bg-success">

                                Shopee

                            </span>

                        </td>

                        <td>

                            <div class="fw-semibold">

                                <?php echo e($order->buyer_username); ?>


                            </div>

                        </td>

                        <td>

                            <?php echo e($order->order_date?->format('d/m/Y')); ?>


                            <br>

                            <small class="text-muted">

                                <?php echo e($order->order_date?->format('H:i')); ?>


                            </small>

                        </td>

                        <td class="text-end">

                            <strong>

                                R$
                                <?php echo e(number_format($order->total_amount,2,',','.')); ?>


                            </strong>

                        </td>

                        <td class="text-end">

                            <?php if($order->profit >= 0): ?>

                                <span class="text-success fw-bold">

                                    R$
                                    <?php echo e(number_format($order->profit,2,',','.')); ?>


                                </span>

                            <?php else: ?>

                                <span class="text-danger fw-bold">

                                    R$
                                    <?php echo e(number_format($order->profit,2,',','.')); ?>


                                </span>

                            <?php endif; ?>

                        </td>

                        <td class="text-center">

                            <?php echo $__env->make(
                                'shopee.orders.components.margin-badge',
                                ['margin'=>$order->margin_percent]
                            , array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        </td>

                        <td class="text-center">

                            <?php echo $__env->make(
                                'shopee.orders.components.status-badge',
                                ['status'=>$order->status]
                            , array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        </td>

                        <td class="text-center">

                            <?php echo $__env->make(
                                'shopee.orders.components.actions'
                            , array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        </td>

                    </tr>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                    <tr>

                        <td colspan="9"
                            class="text-center py-5">

                            <i class="bi bi-inbox display-5 d-block mb-3 text-muted"></i>

                            Nenhum pedido encontrado.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<div class="mt-4">

    <?php echo e($orders->links()); ?>


</div><?php /**PATH C:\projetos\gestao-financeira\resources\views\shopee\orders\components\tabela.blade.php ENDPATH**/ ?>