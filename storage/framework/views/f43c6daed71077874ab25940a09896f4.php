

<?php $__env->startSection('title', 'Pedidos Shopee'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <?php echo $__env->make('shopee.orders.components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('shopee.orders.components.cards', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('shopee.orders.components.filtros', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('shopee.orders.components.tabela', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\gestao-financeira\resources\views\shopee\orders\lista.blade.php ENDPATH**/ ?>