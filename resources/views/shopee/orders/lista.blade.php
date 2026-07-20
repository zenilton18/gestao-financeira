@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold">
                Pedidos
            </h2>

            <span class="text-muted">
                Controle financeiro dos pedidos
            </span>
        </div>

        <div>
            <a href="{{ route('shopee.orders.sync') }}" class="btn btn-primary">
                Sincronizar pedidos
            </a>
        </div>

    </div>


    <div class="card shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                    <tr>

                        <th>Pedido</th>

                        <th>Marketplace</th>

                        <th>Cliente</th>

                        <th>Data</th>

                        <th class="text-end">Venda</th>

                        <th class="text-end">Custo</th>

                        <th class="text-end">Taxas</th>

                        <th class="text-end">Lucro</th>

                        <th class="text-center">Margem</th>

                        <th>Status</th>

                    </tr>

                    </thead>


                    <tbody>

                    @forelse($orders as $order)

                        <tr>

                            <td>
                                {{ $order->shopee_order_id }}
                            </td>

                            <td>
                                <span class="badge bg-success">
                                    Shopee
                                </span>
                            </td>

                            <td>
                                {{ $order->buyer_username }}
                            </td>

                            <td>
                                {{ $order->order_date?->format('d/m/Y H:i') }}
                            </td>

                            <td class="text-end">
                                R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                            </td>

                            <td class="text-end">
                                R$ {{ number_format($order->product_cost, 2, ',', '.') }}
                            </td>

                            <td class="text-end">
                                R$ {{ number_format(($order->shopee_commission ?? 0) + ($order->shopee_fee ?? 0), 2, ',', '.') }}
                            </td>

                            <td class="text-end">
                                <strong>
                                    R$ {{ number_format($order->profit, 2, ',', '.') }}
                                </strong>
                            </td>

                            <td class="text-center">

                                @include(
                                    'shopee.orders.components.margin-badge',
                                    [
                                        'margin' => $order->margin_percent
                                    ]
                                )

                            </td>

                            <td>

                                <span class="badge bg-secondary">
                                    {{ $order->status }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="10" class="text-center py-5">

                                Nenhum pedido encontrado.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">

                {{ $orders->links() }}

            </div>

        </div>

    </div>

</div>

@endsection