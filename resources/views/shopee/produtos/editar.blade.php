@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">
        Editar Produto
    </h2>


    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif



    {{-- INFORMAÇÕES SHOPEE --}}
    <div class="card mb-4">

        <div class="card-header">
            Informações Shopee
        </div>


        <div class="card-body">

            <div class="row">


                <div class="col-md-3">

                    @if($produto->imagem)

                        <img 
                            src="{{ $produto->imagem }}"
                            class="img-fluid rounded"
                        >

                    @endif

                </div>



                <div class="col-md-9">


                    <div class="mb-3">

                        <label>
                            Nome do produto
                        </label>

                        <input 
                            class="form-control"
                            value="{{ $produto->nome }}"
                            disabled
                        >

                    </div>



                    <div class="row">


                        <div class="col-md-6">

                            <label>
                                ID Shopee
                            </label>

                            <input 
                                class="form-control"
                                value="{{ $produto->shopee_item_id }}"
                                disabled
                            >

                        </div>



                        <div class="col-md-6">

                            <label>
                                SKU Shopee
                            </label>

                            <input 
                                class="form-control"
                                value="{{ $produto->sku }}"
                                disabled
                            >

                        </div>


                    </div>



                    <div class="mt-3">

                        <label>
                            Categoria Shopee
                        </label>

                        <input 
                            class="form-control"
                            value="{{ $produto->categoria_id }}"
                            disabled
                        >

                    </div>


                </div>


            </div>


        </div>

    </div>





<form method="POST"
      action="{{ route('shopee.produtos.atualizar',$produto->id) }}">


@csrf

@method('PUT')



{{-- DADOS ERP --}}

<div class="card mb-4">

    <div class="card-header">
        Dados Internos ERP
    </div>


    <div class="card-body">


        <div class="row">


            <div class="col-md-4">

                <label>
                    Preço de custo
                </label>

                <input 
                    type="number"
                    step="0.01"
                    name="preco_custo"
                    class="form-control"
                    value="{{ $produto->preco_custo }}"
                >

            </div>




            <div class="col-md-4">

                <label>
                    Preço venda
                </label>

                <input 
                    type="number"
                    step="0.01"
                    name="preco_venda"
                    class="form-control"
                    value="{{ $produto->preco_venda }}"
                >

            </div>




            <div class="col-md-4">

                <label>
                    Estoque mínimo
                </label>

                <input 
                    type="number"
                    name="estoque_minimo"
                    class="form-control"
                    value="{{ $produto->estoque_minimo }}"
                >

            </div>


        </div>





        <div class="row mt-3">


            <div class="col-md-6">

                <label>
                    Código interno
                </label>


                <input 
                    type="text"
                    name="codigo_interno"
                    class="form-control"
                    value="{{ $produto->codigo_interno }}"
                >

            </div>



            <div class="col-md-6">

                <label>
                    Código barras
                </label>


                <input 
                    type="text"
                    name="codigo_barras"
                    class="form-control"
                    value="{{ $produto->codigo_barras }}"
                >

            </div>


        </div>





        <div class="mt-3">


            <label>
                Localização estoque
            </label>


            <input 
                type="text"
                name="localizacao"
                class="form-control"
                value="{{ $produto->localizacao }}"
            >


        </div>




        <div class="mt-3">


            <label>
                Observações
            </label>


            <textarea
                name="observacoes"
                class="form-control"
                rows="4">{{ $produto->observacoes }}</textarea>


        </div>


    </div>

</div>





{{-- VARIAÇÕES --}}

@if($produto->variacoes->count())

<div class="card mb-4">

    <div class="card-header">
        Variações
    </div>

    <div class="card-body">

        <table class="table table-bordered align-middle">

            <thead>
                <tr>
                    <th>Nome</th>
                    <th>SKU</th>
                    <th>Preço Venda</th>
                    <th style="width: 150px;">Preço Custo</th>
                    <th>Estoque</th>
                </tr>
            </thead>

            <tbody>

                @foreach($produto->variacoes as $variacao)

                    {{-- Passamos o ID da variação no name do input para identificarmos no Request --}}
                    <input type="hidden" name="variacoes[{{ $loop->index }}][id]" value="{{ $variacao->id }}">

                    <tr>
                        <td>
                            {{ $variacao->nome }}
                        </td>

                        <td>
                            {{ $variacao->sku }}
                        </td>

                        <td>
                            R$ {{ number_format($variacao->preco, 2, ',', '.') }}
                        </td>

                        <td>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="variacoes[{{ $loop->index }}][custo]" 
                                class="form-control form-control-sm" 
                                value="{{ old('variacoes.' . $loop->index . '.custo', $variacao->custo) }}"
                            >
                        </td>

                        <td>
                            {{ $variacao->estoque }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endif




<button type="submit"
        class="btn btn-primary">

Salvar alterações

</button>



<a href="{{ route('shopee.produtos.lista') }}"
   class="btn btn-secondary">

Voltar

</a>



</form>


</div>


@endsection