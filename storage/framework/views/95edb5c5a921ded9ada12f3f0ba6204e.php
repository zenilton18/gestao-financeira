

<?php $__env->startSection('title', 'Detalhes do Pedido'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <?php echo $__env->make('shopee.orders.show.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('shopee.orders.show.resumo', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row">

        <div class="col-lg-8">

            <?php echo $__env->make('shopee.orders.show.produtos', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </div>

        <div class="col-lg-4">

            <?php echo $__env->make('shopee.orders.show.financeiro', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\gestao-financeira\resources\views\shopee\orders\show\show.blade.php ENDPATH**/ ?>