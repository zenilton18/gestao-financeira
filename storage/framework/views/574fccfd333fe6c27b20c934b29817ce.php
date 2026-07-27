<?php

    if($margin < 10){

        $color = 'danger';

    }elseif($margin < 40){

        $color = 'warning';

    }else{

        $color = 'success';

    }


?>


<span class="badge bg-<?php echo e($color); ?> fs-6">

    <?php echo e(number_format($margin,2,',','.')); ?>%

</span><?php /**PATH C:\projetos\gestao-financeira\resources\views\shopee\orders\components\margin-badge.blade.php ENDPATH**/ ?>