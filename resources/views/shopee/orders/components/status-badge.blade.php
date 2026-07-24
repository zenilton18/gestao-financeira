@php

$status = strtoupper($status);

$classes = [

    'UNPAID' => 'bg-warning text-dark',

    'READY_TO_SHIP' => 'bg-info',

    'PROCESSED' => 'bg-primary',

    'SHIPPED' => 'bg-primary',

    'COMPLETED' => 'bg-success',

    'CANCELLED' => 'bg-danger',

    'IN_CANCEL' => 'bg-danger',

    'TO_RETURN' => 'bg-warning text-dark',

    'RETURNED' => 'bg-secondary',

];

@endphp

<span class="badge {{ $classes[$status] ?? 'bg-secondary' }}">
    {{ str_replace('_', ' ', $status) }}
</span>