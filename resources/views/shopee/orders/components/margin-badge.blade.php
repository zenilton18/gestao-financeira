@php

    if($margin < 10){

        $color = 'danger';

    }elseif($margin < 40){

        $color = 'warning';

    }else{

        $color = 'success';

    }


@endphp


<span class="badge bg-{{ $color }} fs-6">

    {{ number_format($margin,2,',','.') }}%

</span>