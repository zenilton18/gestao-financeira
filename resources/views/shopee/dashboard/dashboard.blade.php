@extends('layouts.app')

@section('content')

@if(session('success'))

<div class="alert alert-success shadow-sm">
    <i class="bi bi-check-circle"></i>
    {{ session('success') }}
</div>

@endif


@if(session('error'))

<div class="alert alert-danger shadow-sm">

    <i class="bi bi-exclamation-triangle"></i>

    {{ session('error') }}

</div>

@endif



<div class="container-fluid">


{{-- Cabeçalho --}}

<div class="d-flex justify-content-between align-items-center mb-4">


    <div>

        <h2 class="fw-bold mb-1">
            Dashboard Shopee
        </h2>


        <span class="text-muted">
            Visão financeira e operacional
        </span>


    </div>



    {{-- Filtro período --}}

    <form action="{{ route('shopee.dashboard') }}"
          method="GET"
          class="d-flex gap-2">


        <select name="periodo"
                class="form-select">


            <option value="today"
                {{ request('periodo','today') == 'today' ? 'selected' : '' }}>
                Hoje
            </option>


            <option value="yesterday"
                {{ request('periodo') == 'yesterday' ? 'selected' : '' }}>
                Ontem
            </option>


            <option value="30"
                {{ request('periodo') == '30' ? 'selected' : '' }}>
                Últimos 30 dias
            </option>


            <option value="month"
                {{ request('periodo') == 'month' ? 'selected' : '' }}>
                Este mês
            </option>


            <option value="last_month"
                {{ request('periodo') == 'last_month' ? 'selected' : '' }}>
                Mês anterior
            </option>


        </select>


        <button class="btn btn-primary">
            Filtrar
        </button>


    </form>


</div>





{{-- Cards Financeiros --}}

<div class="row g-4">



{{-- Faturamento --}}

<div class="col-xl-3 col-md-6">

<div class="card shadow-sm border-0 h-100">

<div class="card-body">

<small class="text-muted">
FATURAMENTO
</small>


<h2 class="fw-bold mt-2">

R$
{{ number_format($cards['faturamento'] ?? 0,2,',','.') }}

</h2>


</div>

</div>

</div>






{{-- Custo Produtos --}}

<div class="col-xl-3 col-md-6">

<div class="card shadow-sm border-0 h-100">

<div class="card-body">


<small class="text-muted">
CUSTO PRODUTOS
</small>


<h2 class="fw-bold mt-2 text-danger">

R$
{{ number_format($cards['custo_produtos'] ?? 0,2,',','.') }}

</h2>


<span class="text-muted">
Mercadoria vendida
</span>


</div>

</div>

</div>






{{-- Taxas Shopee --}}

<div class="col-xl-3 col-md-6">

<div class="card shadow-sm border-0 h-100">

<div class="card-body">


<small class="text-muted">
TAXAS SHOPEE
</small>


<h2 class="fw-bold mt-2 text-warning">

R$
{{ number_format($cards['taxas_marketplace'] ?? 0,2,',','.') }}

</h2>


<span class="text-muted">
Comissão estimada
</span>


</div>

</div>

</div>






{{-- Valor Líquido --}}

<div class="col-xl-3 col-md-6">

<div class="card shadow-sm border-0 h-100">

<div class="card-body">


<small class="text-muted">
VALOR LÍQUIDO
</small>


<h2 class="fw-bold mt-2 text-info">

R$
{{ number_format($cards['valor_liquido_estimado'] ?? 0,2,',','.') }}

</h2>


<span class="text-muted">
Após taxas e operação
</span>


</div>

</div>

</div>






{{-- Lucro Bruto --}}

<div class="col-xl-3 col-md-6">

<div class="card shadow-sm border-0 h-100">

<div class="card-body">


<small class="text-muted">
LUCRO BRUTO
</small>


<h2 class="fw-bold mt-2 text-success">

R$
{{ number_format($cards['lucro_bruto'] ?? 0,2,',','.') }}

</h2>


<span class="text-muted">
Antes dos custos operacionais
</span>


</div>

</div>

</div>






{{-- Lucro Líquido --}}

<div class="col-xl-3 col-md-6">

<div class="card shadow-sm border-0 h-100">

<div class="card-body">


<small class="text-muted">
LUCRO LÍQUIDO
</small>


<h2 class="fw-bold mt-2 text-primary">

R$
{{ number_format($cards['lucro_liquido'] ?? 0,2,',','.') }}

</h2>


<span class="text-muted">
Resultado final
</span>


</div>

</div>

</div>






{{-- Margem --}}

<div class="col-xl-3 col-md-6">

<div class="card shadow-sm border-0 h-100">

<div class="card-body">


<small class="text-muted">
MARGEM
</small>


<h2 class="fw-bold mt-2">

{{ number_format($cards['margem'] ?? 0,2,',','.') }}%

</h2>


<span class="text-success">
Rentabilidade
</span>


</div>

</div>

</div>






{{-- Pedidos --}}

<div class="col-xl-3 col-md-6">

<div class="card shadow-sm border-0 h-100">

<div class="card-body">


<small class="text-muted">
PEDIDOS
</small>


<h2 class="fw-bold mt-2">

{{ number_format($cards['pedidos'] ?? 0,0,',','.') }}

</h2>


</div>

</div>

</div>



</div>







{{-- Indicadores --}}

<div class="row mt-4">


<div class="col-lg-4">


<div class="card shadow-sm border-0">


<div class="card-header bg-white">

<strong>
Indicadores
</strong>

</div>


<div class="card-body">


<table class="table">


<tr>

<td>
Ticket médio
</td>


<td class="text-end fw-bold">

R$
{{ number_format($cards['ticket_medio'] ?? 0,2,',','.') }}

</td>


</tr>



<tr>

<td>
Custo operacional
</td>


<td class="text-end fw-bold">

R$
{{ number_format($cards['custo_operacional'] ?? 0,2,',','.') }}

</td>


</tr>



<tr>

<td>
Ads
</td>


<td class="text-end fw-bold">

R$
{{ number_format($cards['ads'] ?? 0,2,',','.') }}

</td>


</tr>



</table>


</div>


</div>


</div>





<div class="col-lg-8">


<div class="card shadow-sm border-0">


<div class="card-header bg-white">

<strong>
Evolução financeira
</strong>


</div>



<div class="card-body">


<div style="
height:300px;
display:flex;
align-items:center;
justify-content:center;
color:#999;
">


Gráfico de vendas por período


</div>


</div>


</div>


</div>



</div>



</div>


@endsection