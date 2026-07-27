<div class="card shadow-sm border-0">

    <div class="card-header bg-white">

        <strong>

            Financeiro

        </strong>

    </div>

    <div class="card-body">

        <table class="table table-borderless">

            <tr>

                <td>

                    Venda

                </td>

                <td class="text-end fw-bold">

                    R$

                    <?php echo e(number_format($order->total_amount,2,',','.')); ?>


                </td>

            </tr>

            <tr>

                <td>

                    Comissão

                </td>

                <td class="text-end">

                    R$

                    <?php echo e(number_format($order->shopee_commission,2,',','.')); ?>


                </td>

            </tr>

            <tr>

                <td>

                    Taxas

                </td>

                <td class="text-end">

                    R$

                    <?php echo e(number_format($order->shopee_fee,2,',','.')); ?>


                </td>

            </tr>

            <tr>

                <td>

                    Custo

                </td>

                <td class="text-end">

                    R$

                    <?php echo e(number_format($order->product_cost,2,',','.')); ?>


                </td>

            </tr>

            <tr>

                <td>

                    <strong>

                        Lucro

                    </strong>

                </td>

                <td class="text-end text-success fw-bold">

                    R$

                    <?php echo e(number_format($order->profit,2,',','.')); ?>


                </td>

            </tr>

            <tr>

                <td>

                    Margem

                </td>

                <td class="text-end">

                    <?php echo $__env->make(
                        'shopee.orders.components.margin-badge',
                        ['margin'=>$order->margin_percent]
                    , array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                </td>

            </tr>

        </table>

    </div>

</div><?php /**PATH C:\projetos\gestao-financeira\resources\views\shopee\orders\show\financeiro.blade.php ENDPATH**/ ?>