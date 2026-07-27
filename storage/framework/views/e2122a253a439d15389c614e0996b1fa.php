<form method="GET" action="<?php echo e(route('orders.index')); ?>">

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <div class="row g-3">


                
                <div class="col-lg-5">

                    <input
                        type="text"
                        name="search"
                        value="<?php echo e(request('search')); ?>"
                        class="form-control"
                        placeholder="Pesquisar pedido ou cliente..."
                    >

                </div>



                
                <div class="col-lg-3">

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="">
                            Todos os Status
                        </option>


                        <option value="UNPAID"
                            <?php echo e(request('status') == 'UNPAID' ? 'selected' : ''); ?>>
                            UNPAID
                        </option>


                        <option value="READY_TO_SHIP"
                            <?php echo e(request('status') == 'READY_TO_SHIP' ? 'selected' : ''); ?>>
                            READY_TO_SHIP
                        </option>


                        <option value="PROCESSED"
                            <?php echo e(request('status') == 'PROCESSED' ? 'selected' : ''); ?>>
                            PROCESSED
                        </option>


                        <option value="COMPLETED"
                            <?php echo e(request('status') == 'COMPLETED' ? 'selected' : ''); ?>>
                            COMPLETED
                        </option>


                        <option value="CANCELLED"
                            <?php echo e(request('status') == 'CANCELLED' ? 'selected' : ''); ?>>
                            CANCELLED
                        </option>


                    </select>

                </div>



                
                <div class="col-lg-2">

                    <select
                        name="periodo"
                        class="form-select"
                    >

                        <option value="30"
                            <?php echo e(request('periodo') == '30' ? 'selected' : ''); ?>>
                            30 dias
                        </option>


                        <option value="hoje"
                            <?php echo e(request('periodo') == 'hoje' ? 'selected' : ''); ?>>
                            Hoje
                        </option>


                        <option value="mes"
                            <?php echo e(request('periodo') == 'mes' ? 'selected' : ''); ?>>
                            Este mês
                        </option>


                    </select>

                </div>



                
                <div class="col-lg-2">

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >

                        <i class="bi bi-search"></i>

                        Filtrar

                    </button>

                </div>


            </div>


            
            <?php if(request()->hasAny(['search','status','periodo'])): ?>

                <div class="mt-3">

                    <a
                        href="<?php echo e(route('orders.index')); ?>"
                        class="btn btn-outline-secondary btn-sm"
                    >

                        <i class="bi bi-x-circle"></i>

                        Limpar filtros

                    </a>

                </div>

            <?php endif; ?>


        </div>

    </div>

</form><?php /**PATH C:\projetos\gestao-financeira\resources\views\shopee\orders\components\filtros.blade.php ENDPATH**/ ?>