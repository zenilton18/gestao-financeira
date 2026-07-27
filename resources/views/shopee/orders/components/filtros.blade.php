<form method="GET" action="{{ route('orders.index') }}">

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <div class="row g-3">


                {{-- Pesquisa --}}
                <div class="col-lg-5">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Pesquisar pedido ou cliente..."
                    >

                </div>



                {{-- Status --}}
                <div class="col-lg-3">

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="">
                            Todos os Status
                        </option>


                        <option value="UNPAID"
                            {{ request('status') == 'UNPAID' ? 'selected' : '' }}>
                            UNPAID
                        </option>


                        <option value="READY_TO_SHIP"
                            {{ request('status') == 'READY_TO_SHIP' ? 'selected' : '' }}>
                            READY_TO_SHIP
                        </option>


                        <option value="PROCESSED"
                            {{ request('status') == 'PROCESSED' ? 'selected' : '' }}>
                            PROCESSED
                        </option>


                        <option value="COMPLETED"
                            {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>
                            COMPLETED
                        </option>


                        <option value="CANCELLED"
                            {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>
                            CANCELLED
                        </option>


                    </select>

                </div>



                {{-- Período --}}
                <div class="col-lg-2">

                    <select
                        name="periodo"
                        class="form-select"
                    >

                        <option value="30"
                            {{ request('periodo') == '30' ? 'selected' : '' }}>
                            30 dias
                        </option>


                        <option value="hoje"
                            {{ request('periodo') == 'hoje' ? 'selected' : '' }}>
                            Hoje
                        </option>


                        <option value="mes"
                            {{ request('periodo') == 'mes' ? 'selected' : '' }}>
                            Este mês
                        </option>


                    </select>

                </div>



                {{-- Botão --}}
                <div class="col-lg-2">

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >

                        <i class="bi bi-search"></i>

                        Filtrar

                    </button>

                </div>


            </div>


            {{-- Limpar filtros --}}
            @if(request()->hasAny(['search','status','periodo']))

                <div class="mt-3">

                    <a
                        href="{{ route('orders.index') }}"
                        class="btn btn-outline-secondary btn-sm"
                    >

                        <i class="bi bi-x-circle"></i>

                        Limpar filtros

                    </a>

                </div>

            @endif


        </div>

    </div>

</form>