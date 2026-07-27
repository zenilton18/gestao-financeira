
<?php if(session('success')): ?>



<div class="row mb-4">

    <div class="col-xl-4 col-md-6">


        <div class="card shadow-sm border-0">


            <div class="card-body">


                <div class="d-flex justify-content-between align-items-center">


                    <div>


                        <small class="text-muted">
                            INTEGRAÇÃO SHOPEE
                        </small>


                        <?php
                            $connection = \App\Models\ShopeeConnection::latest()->first();
                        ?>



                        <?php if($connection): ?>


                            <h4 class="fw-bold mt-2 text-success">

                                <i class="bi bi-check-circle"></i>

                                Conectada

                            </h4>


                            <span class="text-muted">

                                Loja:
                                <?php echo e($connection->shop_id); ?>


                            </span>


                        <?php else: ?>


                            <h4 class="fw-bold mt-2 text-danger">

                                <i class="bi bi-x-circle"></i>

                                Não conectada

                            </h4>


                            <a href="/shopee/connect"
                               class="btn btn-primary btn-sm mt-2">

                                Conectar Loja

                            </a>


                        <?php endif; ?>


                    </div>


                    <div class="fs-1 text-primary">

                        <i class="bi bi-shop"></i>

                    </div>


                </div>


            </div>


        </div>


    </div>


</div>

<?php endif; ?>



<?php if(session('error')): ?>

<div class="alert alert-danger shadow-sm">

    <i class="bi bi-exclamation-triangle"></i>

    <?php echo e(session('error')); ?>


</div>

<?php endif; ?><?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

     <?php $__env->slot('header', null, []); ?> 

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h2 class="fw-bold mb-0">
                    Dashboard
                </h2>

                <small class="text-muted">
                    Visão geral da operação
                </small>
            </div>

        </div>

     <?php $__env->endSlot(); ?>


    <div class="container-fluid py-4">


        

        <div class="mb-4">

            <h4 class="fw-bold">
                Olá, <?php echo e(auth()->user()->name); ?>

            </h4>

            <p class="text-muted">
                Acompanhe seus pedidos e vendas em um único lugar.
            </p>

        </div>



        

        <div class="row g-4">


            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <div class="text-muted">
                            Pedidos
                        </div>

                        <h2 class="fw-bold">
                            0
                        </h2>

                        <small class="text-success">
                            Operação ativa
                        </small>

                    </div>

                </div>

            </div>



            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <div class="text-muted">
                            Vendas
                        </div>

                        <h2 class="fw-bold">
                            R$ 0,00
                        </h2>

                        <small class="text-success">
                            Este período
                        </small>

                    </div>

                </div>

            </div>



            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <div class="text-muted">
                            Produtos
                        </div>

                        <h2 class="fw-bold">
                            0
                        </h2>

                        <small class="text-muted">
                            Cadastrados
                        </small>

                    </div>

                </div>

            </div>



            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <div class="text-muted">
                            Shopee
                        </div>

                        <h2 class="fw-bold text-success">
                            Online
                        </h2>

                        <small class="text-muted">
                            Integração ativa
                        </small>

                    </div>

                </div>

            </div>


        </div>



        

        <div class="row mt-4 g-4">


            <div class="col-md-8">


                <div class="card shadow-sm border-0">

                    <div class="card-header bg-white fw-bold">

                        Últimos pedidos

                    </div>


                    <div class="card-body text-center text-muted">

                        Nenhum pedido carregado ainda.

                    </div>


                </div>


            </div>




            <div class="col-md-4">


                <div class="card shadow-sm border-0">


                    <div class="card-header bg-white fw-bold">

                        Status Shopee

                    </div>


                    <div class="card-body">


                        <div class="d-flex align-items-center">

                            <span class="badge bg-success me-2">
                                Online
                            </span>

                            Conectado


                        </div>


                        <hr>


                        <small class="text-muted">

                            Última sincronização:
                            aguardando

                        </small>


                    </div>


                </div>


            </div>


        </div>


    </div>


 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\projetos\gestao-financeira\resources\views\dashboard.blade.php ENDPATH**/ ?>