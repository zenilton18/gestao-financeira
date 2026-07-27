<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <a href="<?php echo e(route('orders.index')); ?>"
           class="btn btn-light border mb-3">

            <i class="bi bi-arrow-left"></i>

            Voltar

        </a>

        <h2 class="fw-bold mb-1">

            Pedido

            #<?php echo e($order->shopee_order_id); ?>


        </h2>

        <small class="text-muted">

            Detalhes completos do pedido

        </small>

    </div>

   <div class="d-flex gap-2">


    <form method="POST"
          action="<?php echo e(route('shopee.orders.syncOne',$order)); ?>">

        <?php echo csrf_field(); ?>

        <button class="btn btn-primary">

            <i class="bi bi-arrow-repeat"></i>

            Atualizar Shopee

        </button>

    </form>



    <?php echo $__env->make(
        'shopee.orders.components.status-badge',
        ['status'=>$order->status]
    , array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


</div>

</div><?php /**PATH C:\projetos\gestao-financeira\resources\views\shopee\orders\show\header.blade.php ENDPATH**/ ?>