@extends('layouts.app')

@section('title', 'Novo Lançamento')

@section('content')
@if ($errors->any())

    <div class="alert alert-danger">

        <ul class="mb-0">

            @foreach ($errors->all() as $erro)

                <li>{{ $erro }}</li>

            @endforeach

        </ul>

    </div>

@endif
<div>
    Dashboard
</div>
@endsection