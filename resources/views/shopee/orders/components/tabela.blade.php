<form id="form-etiquetas" action="{{ route('shopee.orders.etiquetas') }}" method="POST" target="_blank">
    @csrf
    
    <!-- Barra de Ações em Lote (Fixa ou Exibida quando houver seleção) -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check ms-2">
            <input class="form-check-input" type="checkbox" id="select-all">
            <label class="form-check-label fw-semibold" for="select-all">
                Selecionar Todos
            </label>
        </div>
        
        <button type="submit" id="btn-imprimir-etiquetas" class="btn btn-outline-primary btn-sm" disabled>
            <i class="bi bi-printer me-1"></i> Imprimir Etiquetas Selecionadas (<span id="selected-count">0</span>)
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;" class="text-center">#</th>
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
                            <td class="text-center">
                                <input class="form-check-input order-checkbox" 
                                       type="checkbox" 
                                       name="order_ids[]" 
                                       value="{{ $order->id }}">
                            </td>

                            <td>
                                <strong>{{ $order->pedido_externo }}</strong>
                            </td>

                            <td>
                                <span class="badge bg-success">Shopee</span>
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $order->usuario_cliente }}</div>
                            </td>

                            <td>
                                {{ $order->data_pedido?->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">
                                    {{ $order->data_pedido?->format('H:i') }}
                                </small>
                            </td>

                            <td class="text-end">
                                <strong>R$ {{ number_format($order->valor_produtos, 2, ',', '.') }}</strong>
                            </td>

                            <td class="text-end">
                                <span class="{{ $order->lucro_bruto >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                    R$ {{ number_format($order->lucro_bruto, 2, ',', '.') }}
                                </span>
                            </td>

                            <td class="text-center">
                                @php
                                    // Garante que não haverá divisão por zero
                                    $calculoMargem = ($order->valor_total ?? 0) > 0 
                                        ? (($order->lucro_bruto ?? 0) / $order->valor_total) * 100 
                                        : 0;
                                @endphp

                                @include('shopee.orders.components.margin-badge', ['margin' => $calculoMargem])
                            </td>

                            <td class="text-center">
                                @include('shopee.orders.components.status-badge', ['status' => $order->status_marketplace])
                            </td>

                            <td class="text-center">
                                @include('shopee.orders.components.actions')
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
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
</form>

<div class="mt-4">
    {{ $orders->links() }}
</div>

<!-- Script Vanilla JS para gerenciar a seleção dos Checkboxes -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('select-all');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const printBtn = document.getElementById('btn-imprimir-etiquetas');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateCounter() {
        const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
        selectedCountSpan.textContent = checkedCount;
        printBtn.disabled = checkedCount === 0;
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            orderCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
            updateCounter();
        });
    }

    orderCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!this.checked) {
                selectAllCheckbox.checked = false;
            } else if (document.querySelectorAll('.order-checkbox:checked').length === orderCheckboxes.length) {
                selectAllCheckbox.checked = true;
            }
            updateCounter();
        });
    });
});
</script>