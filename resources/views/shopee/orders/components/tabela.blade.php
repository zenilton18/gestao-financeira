<div class="card shadow-sm border-0">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                <tr>

                    <th>Pedido</th>

                    <th>Marketplace</th>

                    <th>Cliente</th>

                    <th>Data</th>

                    <th class="text-end">Venda</th>

                    <th class="text-end">Lucro</th>

                    <th class="text-center">Margem</th>

                    <th class="text-center">Status</th>

                    <th class="text-center">Ações</th>

                </tr>

                </thead>

                <tbody>

                @forelse($orders as $order)

                    <tr>

                        <td>

                            <strong>

                                {{ $order->shopee_order_id }}

                            </strong>

                        </td>

                        <td>

                            <span class="badge bg-success">

                                Shopee

                            </span>

                        </td>

                        <td>

                            <div class="fw-semibold">

                                {{ $order->buyer_username }}

                            </div>

                        </td>

                        <td>

                            {{ $order->order_date?->format('d/m/Y') }}

                            <br>

                            <small class="text-muted">

                                {{ $order->order_date?->format('H:i') }}

                            </small>

                        </td>

                        <td class="text-end">

                            <strong>

                                R$
                                {{ number_format($order->total_amount,2,',','.') }}

                            </strong>

                        </td>

                        <td class="text-end">

                            @if($order->profit >= 0)

                                <span class="text-success fw-bold">

                                    R$
                                    {{ number_format($order->profit,2,',','.') }}

                                </span>

                            @else

                                <span class="text-danger fw-bold">

                                    R$
                                    {{ number_format($order->profit,2,',','.') }}

                                </span>

                            @endif

                        </td>

                        <td class="text-center">

                            @include(
                                'shopee.orders.components.margin-badge',
                                ['margin'=>$order->margin_percent]
                            )

                        </td>

                        <td class="text-center">

                            @include(
                                'shopee.orders.components.status-badge',
                                ['status'=>$order->status]
                            )

                        </td>

                        <td class="text-center">

                            @include(
                                'shopee.orders.components.actions'
                            )

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="9"
                            class="text-center py-5">

                            <i class="bi bi-inbox display-5 d-block mb-3 text-muted"></i>

                            Nenhum pedido encontrado.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

<div class="mt-4">

    {{ $orders->links() }}

</div>