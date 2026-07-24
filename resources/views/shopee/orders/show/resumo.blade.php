<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white">

        <strong>

            Resumo

        </strong>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-3">

                <small class="text-muted">

                    Cliente

                </small>

                <div class="fw-bold">

                    {{ $order->buyer_username }}

                </div>

            </div>

            <div class="col-md-3">

                <small class="text-muted">

                    Marketplace

                </small>

                <div>

                    Shopee

                </div>

            </div>

            <div class="col-md-3">

                <small class="text-muted">

                    Data

                </small>

                <div>

                    {{ $order->order_date?->format('d/m/Y H:i') }}

                </div>

            </div>

            <div class="col-md-3">

                <small class="text-muted">

                    Pedido

                </small>

                <div>

                    {{ $order->shopee_order_id }}

                </div>

            </div>

        </div>

    </div>

</div>