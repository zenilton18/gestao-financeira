@extends('layouts.app')

@section('title', 'Detalhes do Pedido')

@section('content')

<div class="container-fluid">

    @include('shopee.orders.show.header')

    @include('shopee.orders.show.resumo')

    <div class="row">

        <div class="col-lg-8">

            @include('shopee.orders.show.produtos')

        </div>

        <div class="col-lg-4">

            @include('shopee.orders.show.financeiro')

        </div>

    </div>

</div>

@endsection