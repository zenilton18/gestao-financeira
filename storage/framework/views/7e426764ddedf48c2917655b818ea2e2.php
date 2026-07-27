

<?php $__env->startSection('content'); ?>
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

    <i class="bi bi-exclamation-triangle">ddddddddddddddd</i>

    <?php echo e(session('error')); ?>


</div>

<?php endif; ?>

<div class="container-fluid">


    

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Dashboard Shopee
            </h2>

            <span class="text-muted">
                Visão financeira e operacional
            </span>

        </div>


        

        <div class="d-flex gap-2">


            <select class="form-select">

                <option>
                    Hoje
                </option>

                <option>
                    Ontem
                </option>

                <option selected>
                    Últimos 30 dias
                </option>

                <option>
                    Este mês
                </option>

                <option>
                    Mês anterior
                </option>

                <option>
                    Personalizado
                </option>

            </select>


            <button class="btn btn-primary">

                Filtrar

            </button>


        </div>


    </div>




    


    <div class="row g-4">


        

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <small class="text-muted">
                        FATURAMENTO
                    </small>


                    <h2 class="fw-bold mt-2">
                        R$ 15.824,35
                    </h2>


                    <span class="text-success">
                        ↑ 12% período anterior
                    </span>


                </div>

            </div>

        </div>



        

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">


                    <small class="text-muted">
                        CUSTO PRODUTOS
                    </small>


                    <h2 class="fw-bold mt-2 text-danger">

                        R$ 5.200,00

                    </h2>


                    <span class="text-muted">

                        Custo mercadoria

                    </span>


                </div>

            </div>

        </div>




        

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">


                    <small class="text-muted">
                        TAXAS SHOPEE
                    </small>


                    <h2 class="fw-bold mt-2 text-warning">

                        R$ 1.235,00

                    </h2>


                    <span class="text-muted">

                        Comissão + serviços

                    </span>


                </div>

            </div>

        </div>




        

        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">


                    <small class="text-muted">
                        CUSTO ADS
                    </small>


                    <h2 class="fw-bold mt-2 text-danger">

                        R$ 800,00

                    </h2>


                    <span class="text-muted">

                        Investimento anúncios

                    </span>


                </div>

            </div>

        </div>




        


        <div class="col-xl-3 col-md-6">


            <div class="card shadow-sm border-0 h-100">


                <div class="card-body">


                    <small class="text-muted">
                        LUCRO BRUTO
                    </small>


                    <h2 class="fw-bold mt-2 text-success">

                        R$ 9.389,35

                    </h2>


                    <span class="text-muted">

                        Antes dos Ads

                    </span>


                </div>


            </div>


        </div>




        


        <div class="col-xl-3 col-md-6">


            <div class="card shadow-sm border-0 h-100">


                <div class="card-body">


                    <small class="text-muted">
                        LUCRO PÓS ADS
                    </small>


                    <h2 class="fw-bold mt-2 text-primary">

                        R$ 8.589,35

                    </h2>


                    <span class="text-muted">

                        Lucro real

                    </span>


                </div>


            </div>


        </div>




        


        <div class="col-xl-3 col-md-6">


            <div class="card shadow-sm border-0 h-100">


                <div class="card-body">


                    <small class="text-muted">
                        MARGEM MÉDIA
                    </small>


                    <h2 class="fw-bold mt-2">

                        54%

                    </h2>


                    <span class="text-success">

                        Excelente

                    </span>


                </div>


            </div>


        </div>




        


        <div class="col-xl-3 col-md-6">


            <div class="card shadow-sm border-0 h-100">


                <div class="card-body">


                    <small class="text-muted">
                        PEDIDOS
                    </small>


                    <h2 class="fw-bold mt-2">

                        358

                    </h2>


                    <span class="text-primary">

                        18 hoje

                    </span>


                </div>


            </div>


        </div>


    </div>





    


    <div class="row mt-4">


        <div class="col-lg-8">


            <div class="card shadow-sm border-0">


                <div class="card-header bg-white">

                    <strong>
                        Evolução financeira
                    </strong>

                </div>


                <div class="card-body">


                    <div style="
                        height:350px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        color:#999;
                    ">


                        Gráfico de vendas por período


                    </div>


                </div>


            </div>


        </div>



        <div class="col-lg-4">


            <div class="card shadow-sm border-0">


                <div class="card-header bg-white">

                    <strong>
                        Indicadores
                    </strong>

                </div>


                <div class="card-body">


                    <table class="table">


                        <tr>

                            <td>
                                Ticket médio
                            </td>

                            <td class="text-end fw-bold">
                                R$ 44,20
                            </td>

                        </tr>


                        <tr>

                            <td>
                                ROAS
                            </td>

                            <td class="text-end fw-bold text-success">
                                4,5
                            </td>

                        </tr>


                        <tr>

                            <td>
                                ACOS
                            </td>

                            <td class="text-end fw-bold">
                                22%
                            </td>

                        </tr>


                        <tr>

                            <td>
                                Cancelamentos
                            </td>

                            <td class="text-end text-danger fw-bold">
                                6
                            </td>

                        </tr>


                    </table>


                </div>


            </div>


        </div>


    </div>



</div>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\gestao-financeira\resources\views\shopee\dashboard\dashboard.blade.php ENDPATH**/ ?>