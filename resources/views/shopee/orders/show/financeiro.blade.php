<div class="card shadow-sm border-0">

    <div class="card-header bg-white">

        <strong>

            Financeiro

        </strong>

    </div>

    <div class="card-body">

        <table class="table table-borderless">

            <tr>

                <td>

                    Venda

                </td>

                <td class="text-end fw-bold">

                    R$

                    {{ number_format($order->total_amount,2,',','.') }}

                </td>

            </tr>

            <tr>

                <td>

                    Comissão

                </td>

                <td class="text-end">

                    R$

                    {{ number_format($order->shopee_commission,2,',','.') }}

                </td>

            </tr>

            <tr>

                <td>

                    Taxas

                </td>

                <td class="text-end">

                    R$

                    {{ number_format($order->shopee_fee,2,',','.') }}

                </td>

            </tr>

            <tr>

                <td>

                    Custo

                </td>

                <td class="text-end">

                    R$

                    {{ number_format($order->product_cost,2,',','.') }}

                </td>

            </tr>

            <tr>

                <td>

                    <strong>

                        Lucro

                    </strong>

                </td>

                <td class="text-end text-success fw-bold">

                    R$

                    {{ number_format($order->profit,2,',','.') }}

                </td>

            </tr>

            <tr>

                <td>

                    Margem

                </td>

                <td class="text-end">

                    @include(
                        'shopee.orders.components.margin-badge',
                        ['margin'=>$order->margin_percent]
                    )

                </td>

            </tr>

        </table>

    </div>

</div>