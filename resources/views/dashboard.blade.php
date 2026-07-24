
@if(session('success'))

{{-- Status Integração Shopee --}}

<div class="row mb-4">

    <div class="col-xl-4 col-md-6">


        <div class="card shadow-sm border-0">


            <div class="card-body">


                <div class="d-flex justify-content-between align-items-center">


                    <div>


                        <small class="text-muted">
                            INTEGRAÇÃO SHOPEE
                        </small>


                        @php
                            $connection = \App\Models\ShopeeConnection::latest()->first();
                        @endphp



                        @if($connection)


                            <h4 class="fw-bold mt-2 text-success">

                                <i class="bi bi-check-circle"></i>

                                Conectada

                            </h4>


                            <span class="text-muted">

                                Loja:
                                {{ $connection->shop_id }}

                            </span>


                        @else


                            <h4 class="fw-bold mt-2 text-danger">

                                <i class="bi bi-x-circle"></i>

                                Não conectada

                            </h4>


                            <a href="/shopee/connect"
                               class="btn btn-primary btn-sm mt-2">

                                Conectar Loja

                            </a>


                        @endif


                    </div>


                    <div class="fs-1 text-primary">

                        <i class="bi bi-shop"></i>

                    </div>


                </div>


            </div>


        </div>


    </div>


</div>

@endif



@if(session('error'))

<div class="alert alert-danger shadow-sm">

    <i class="bi bi-exclamation-triangle"></i>

    {{ session('error') }}

</div>

@endif<x-app-layout>

    <x-slot name="header">

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

    </x-slot>


    <div class="container-fluid py-4">


        {{-- Saudação --}}

        <div class="mb-4">

            <h4 class="fw-bold">
                Olá, {{ auth()->user()->name }}
            </h4>

            <p class="text-muted">
                Acompanhe seus pedidos e vendas em um único lugar.
            </p>

        </div>



        {{-- KPIs --}}

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



        {{-- Área principal --}}

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


</x-app-layout>