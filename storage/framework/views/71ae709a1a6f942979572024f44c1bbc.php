
<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $__env->yieldContent('title','ERP Financeiro'); ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Alpine -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/sidebar.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/navbar.css')); ?>">

    <?php echo $__env->yieldPushContent('styles'); ?>

</head>

<body>

<div class="wrapper" x-data="{ menuAberto:true }">

    <!-- Sidebar -->
    <?php echo $__env->make('components.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Conteúdo -->

    <div class="main">

        <!-- Navbar -->

        <?php echo $__env->make('components.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Página -->

        <main class="content">

            <?php echo $__env->yieldContent('content'); ?>

        </main>

        <!-- Rodapé -->

        <footer class="footer">

            <small>
                ERP Financeiro © <?php echo e(date('Y')); ?>

            </small>

        </footer>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo e(asset('js/app.js')); ?>"></script>

<?php echo $__env->yieldPushContent('scripts'); ?>

</body>

</html>
<?php /**PATH C:\projetos\gestao-financeira\resources\views\layouts\home.blade.php ENDPATH**/ ?>