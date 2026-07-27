<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?php echo e(config('app.name', 'ERP')); ?>

    </title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

</head>

<body>

<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">

    <div class="card shadow border-0" style="width: 420px;">

        <div class="card-body p-5">

            <div class="text-center mb-4">

                <div class="mb-3">
                    <i class="bi bi-shop fs-1 text-primary"></i>
                </div>

                <h3 class="fw-bold">
                    Gestão Financeira
                </h3>


            </div>


            <?php echo e($slot); ?>



        </div>

    </div>

</div>


</body>

</html><?php /**PATH C:\projetos\gestao-financeira\resources\views\layouts\guest.blade.php ENDPATH**/ ?>