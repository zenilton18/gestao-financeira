<?php

$status = strtoupper($status);

$classes = [

    'UNPAID' => 'bg-warning text-dark',

    'READY_TO_SHIP' => 'bg-info',

    'PROCESSED' => 'bg-primary',

    'SHIPPED' => 'bg-primary',

    'COMPLETED' => 'bg-success',

    'CANCELLED' => 'bg-danger',

    'IN_CANCEL' => 'bg-danger',

    'TO_RETURN' => 'bg-warning text-dark',

    'RETURNED' => 'bg-secondary',

];

?>

<span class="badge <?php echo e($classes[$status] ?? 'bg-secondary'); ?>">
    <?php echo e(str_replace('_', ' ', $status)); ?>

</span><?php /**PATH C:\projetos\gestao-financeira\resources\views\shopee\orders\components\status-badge.blade.php ENDPATH**/ ?>