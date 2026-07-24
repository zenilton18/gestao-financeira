@extends('layouts.app')

@section('title', 'Pedidos Shopee')

@section('content')

<div class="container-fluid">

    @include('shopee.orders.components.header')

    @include('shopee.orders.components.cards')

    @include('shopee.orders.components.filtros')

    @include('shopee.orders.components.tabela')

</div>

@endsection