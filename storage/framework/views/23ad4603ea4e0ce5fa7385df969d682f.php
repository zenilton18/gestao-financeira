<div class="card shadow-sm border-0">

    <div class="card-header bg-white">

        <strong>

            Produtos

        </strong>

    </div>

    <div class="card-body p-0">

        <table class="table table-hover mb-0">

            <thead>

            <tr>

                <th>Produto</th>

                <th>SKU</th>

                <th class="text-center">

                    Qtd

                </th>

                <th class="text-end">

                    Valor

                </th>

            </tr>

            </thead>

            <tbody>

            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <tr>

                    <td>

                        <?php echo e($item->product_name); ?>


                    </td>

                    <td>

                        <?php echo e($item->product?->seller_sku); ?>


                    </td>

                    <td class="text-center">

                        <?php echo e($item->quantity); ?>


                    </td>

                    <td class="text-end">

                        R$

                        <?php echo e(number_format($item->price,2,',','.')); ?>


                    </td>

                </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </tbody>

        </table>

    </div>

</div><?php /**PATH C:\projetos\gestao-financeira\resources\views\shopee\orders\show\produtos.blade.php ENDPATH**/ ?>