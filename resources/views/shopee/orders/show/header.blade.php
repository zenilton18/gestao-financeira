<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <a href="{{ route('orders.index') }}"
           class="btn btn-light border mb-3">

            <i class="bi bi-arrow-left"></i>

            Voltar

        </a>

        <h2 class="fw-bold mb-1">

            Pedido

            #{{ $order->shopee_order_id }}

        </h2>

        <small class="text-muted">

            Detalhes completos do pedido

        </small>

    </div>

   <div class="d-flex gap-2">


    <form method="POST"
          action="{{ route('shopee.orders.syncOne',$order) }}">

        @csrf

        <button class="btn btn-primary">

            <i class="bi bi-arrow-repeat"></i>

            Atualizar Shopee

        </button>

    </form>



    @include(
        'shopee.orders.components.status-badge',
        ['status'=>$order->status]
    )


</div>

</div>