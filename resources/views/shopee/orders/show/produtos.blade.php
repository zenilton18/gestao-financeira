<div class="card shadow-sm border-0">

    <div class="card-header bg-white">

        <strong>

            Produtos

        </strong>

    </div>

    <div class="card-body p-0">

        <table class="table table-hover mb-0">

            <thead>

            <tr>

                <th>Produto</th>

                <th>SKU</th>

                <th class="text-center">

                    Qtd

                </th>

                <th class="text-end">

                    Valor

                </th>

            </tr>

            </thead>

            <tbody>

            @foreach($order->items as $item)

                <tr>

                    <td>

                        {{ $item->product_name }}

                    </td>

                    <td>

                        {{ $item->product?->seller_sku }}

                    </td>

                    <td class="text-center">

                        {{ $item->quantity }}

                    </td>

                    <td class="text-end">

                        R$

                        {{ number_format($item->price,2,',','.') }}

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </div>

</div>