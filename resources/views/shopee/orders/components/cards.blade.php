<div class="row g-3 mb-4">

    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <small class="text-muted">

                    TOTAL DE PEDIDOS

                </small>

                <h2 class="fw-bold mt-2">

                    {{ number_format($orders->total(),0,',','.') }}

                </h2>

                <span class="text-primary">

                    Pedidos cadastrados

                </span>

            </div>

        </div>

    </div>



    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <small class="text-muted">

                    FATURAMENTO

                </small>

                <h2 class="fw-bold mt-2 text-success">

                    R$
                    {{ number_format($orders->sum('total_amount'),2,',','.') }}

                </h2>

                <span class="text-muted">

                    Página atual

                </span>

            </div>

        </div>

    </div>



    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <small class="text-muted">

                    LUCRO

                </small>

                <h2 class="fw-bold mt-2 text-primary">

                    R$
                    {{ number_format($orders->sum('profit'),2,',','.') }}

                </h2>

                <span class="text-muted">

                    Página atual

                </span>

            </div>

        </div>

    </div>



    <div class="col-xl-3 col-md-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <small class="text-muted">

                    TICKET MÉDIO

                </small>

                <h2 class="fw-bold mt-2 text-warning">

                    R$

                    {{ number_format($orders->avg('total_amount') ?? 0,2,',','.') }}

                </h2>

                <span class="text-muted">

                    Página atual

                </span>

            </div>

        </div>

    </div>

</div>