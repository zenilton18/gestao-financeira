<aside class="sidebar">


    
    <div class="sidebar-logo">

        <div class="logo-icon">
            <i class="bi bi-grid-1x2"></i>
        </div>


        <div>

            <h5 class="mb-0">
                Gestão ERP
            </h5>


            <small>
                Marketplace + Financeiro
            </small>

        </div>

    </div>



    
    <nav class="sidebar-menu">


        
        <a href="<?php echo e(route('shopee.dashboard')); ?>"
           class="menu-item">


            <i class="bi bi-speedometer2"></i>


            <span>
                Dashboard
            </span>


        </a>




        
        <div x-data="{ aberto:true }">


            <button
                class="menu-item menu-dropdown"
                @click="aberto=!aberto">


                <div>

                    <i class="bi bi-shop"></i>


                    <span>
                        Marketplaces
                    </span>


                </div>


                <i class="bi"
                   :class="aberto ? 'bi-chevron-down' : 'bi-chevron-right'">
                </i>


            </button>



            <div
                x-show="aberto"
                x-transition
                class="submenu">


                <a href="<?php echo e(route('orders.index')); ?>"
                   class="submenu-item">

                    Pedidos Shopee

                </a>



                <a href="<?php echo e(route('shopee.produtos.lista')); ?>"
                   class="submenu-item">

                    Produtos Shopee

                </a>



                <a href="/shopee/connect"
                   class="submenu-item">

                    Conectar Loja

                </a>



            </div>


        </div>





        
        <div x-data="{ aberto:true }">


            <button
                class="menu-item menu-dropdown"
                @click="aberto=!aberto">


                <div>

                    <i class="bi bi-cash-stack"></i>


                    <span>
                        Financeiro
                    </span>


                </div>


                <i class="bi"
                   :class="aberto ? 'bi-chevron-down' : 'bi-chevron-right'">
                </i>


            </button>



            <div
                x-show="aberto"
                x-transition
                class="submenu">


                <a href="/lancamento/cadastro"
                   class="submenu-item">

                    Novo Lançamento

                </a>


                <a href="#"
                   class="submenu-item">

                    Contas a Pagar

                </a>


                <a href="#"
                   class="submenu-item">

                    Contas a Receber

                </a>


                <a href="#"
                   class="submenu-item">

                    Fluxo de Caixa

                </a>


            </div>


        </div>





        
        <div x-data="{ aberto:false }">


            <button
                class="menu-item menu-dropdown"
                @click="aberto=!aberto">


                <div>

                    <i class="bi bi-box-seam"></i>


                    <span>
                        Estoque
                    </span>


                </div>


                <i class="bi"
                   :class="aberto ? 'bi-chevron-down' : 'bi-chevron-right'">
                </i>


            </button>




            <div
                x-show="aberto"
                x-transition
                class="submenu">


                <a href="<?php echo e(route('shopee.produtos.lista')); ?>"
                   class="submenu-item">

                    Produtos

                </a>


                <a href="#"
                   class="submenu-item">

                    Entradas

                </a>


                <a href="#"
                   class="submenu-item">

                    Saídas

                </a>


            </div>


        </div>





        

        <a href="#"
           class="menu-item">


            <i class="bi bi-people"></i>


            <span>
                Clientes
            </span>


        </a>



        <a href="#"
           class="menu-item">


            <i class="bi bi-truck"></i>


            <span>
                Fornecedores
            </span>


        </a>




        <a href="#"
           class="menu-item">


            <i class="bi bi-file-earmark-bar-graph"></i>


            <span>
                Relatórios
            </span>


        </a>




        <a href="#"
           class="menu-item">


            <i class="bi bi-gear"></i>


            <span>
                Configurações
            </span>


        </a>



    </nav>





    
    <div class="sidebar-footer">


        <div class="user-avatar">

            <?php echo e(strtoupper(substr(Auth::user()->name,0,1))); ?>


        </div>



        <div>


            <strong>
                <?php echo e(Auth::user()->name); ?>

            </strong>


            <small class="d-block text-muted">

                Administrador

            </small>


        </div>


    </div>



</aside><?php /**PATH C:\projetos\gestao-financeira\resources\views\components\sidebar.blade.php ENDPATH**/ ?>