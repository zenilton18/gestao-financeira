@extends('layouts.app')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3>Produtos Shopee</h3>

        <a
            href="{{ route('shopee.produtos.sincronizar') }}"
            class="btn btn-primary">

            Sincronizar Produtos

        </a>

    </div>

    <div class="card shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                <tr>

                    <th>Imagem</th>

                    <th>Produto</th>

                    <th>SKU</th>

                    <th>Marca</th>

                    <th>Preço</th>

                    <th>Estoque</th>

                    <th>Status</th>
                    <th>Ação</th>

                </tr>

                </thead>

                <tbody>

                @forelse($produtos as $produto)

                    <tr>

                        <td width="80">

                            <img
                                src="{{ $produto->imagem }}"
                                width="60"
                                class="rounded">

                        </td>

                        <td>

                            <strong>{{ $produto->nome }}</strong>

                            <br>

                            <small>

                                Shopee ID:
                                {{ $produto->shopee_item_id }}

                            </small>

                        </td>

                        <td>

                            {{ $produto->sku }}

                        </td>

                        <td>

                            {{ $produto->marca }}

                        </td>

                        <td>

                            {{ number_format($produto->preco_venda,2,',','.') }}

                        </td>

                        <td>

                            {{ $produto->estoque_total }}

                        </td>

                        <td>

                            {{ $produto->status }}

                        </td>
                        <td>
                            <a href="{{ route('shopee.produtos.editar',$produto->id) }}"
                            class="btn btn-primary">
                                Editar
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center py-5">

                            Nenhum produto encontrado.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="mt-3">

        {{ $produtos->links() }}

    </div>

</div>

@endsection