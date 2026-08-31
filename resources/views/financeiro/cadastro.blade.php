
<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Novo Lançamento</title>


    {{-- Bootstrap 5 --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >


    <style>

        /* =========================================================
           CONFIGURAÇÃO GERAL
           ========================================================= */

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #eef3f7;
            font-family: Arial, Helvetica, sans-serif;
        }


        /* =========================================================
           HEADER
           ========================================================= */

        .app-header {
            background: #2196f3;
            color: #fff;

            min-height: 64px;

            display: flex;
            align-items: center;

            padding: 10px 15px;

            box-shadow: 0 2px 5px rgba(0, 0, 0, .18);
        }

        .header-title {
            font-size: 23px;
            font-weight: 700;
        }

        .header-icon {
            font-size: 26px;
        }

        .btn-dashboard {
            color: #fff;

            border: 1px solid rgba(255, 255, 255, .55);

            border-radius: 8px;

            padding: 7px 10px;

            font-size: 14px;
            font-weight: 600;

            text-decoration: none;

            white-space: nowrap;
        }

        .btn-dashboard:hover {
            background: rgba(255, 255, 255, .15);
            color: #fff;
        }


        /* =========================================================
           CONTEÚDO
           ========================================================= */

        .page-content {
            width: 100%;
            max-width: 650px;

            margin: 0 auto;

            padding: 15px 10px 30px;
        }


        /* =========================================================
           ALERTAS
           ========================================================= */

        .alert {
            border-radius: 10px;
        }


        /* =========================================================
           CARD
           ========================================================= */

        .lancamento-card {
            background: #fff;

            border: 0;

            border-radius: 15px;

            box-shadow: 0 2px 8px rgba(0, 0, 0, .15);

            overflow: hidden;
        }

        .lancamento-body {
            padding: 18px 15px;
        }


        /* =========================================================
           RECEBIDO / PAGO
           ========================================================= */

        .operacao-tabs {
            display: flex;

            gap: 12px;

            margin-bottom: 22px;
        }

        .operacao-tabs > div {
            flex: 1;
        }

        /*
         * Botão padrão.
         * Quando não estiver selecionado fica cinza.
         */
        .operacao-tabs .btn {
            width: 100%;

            border: 0;

            border-radius: 25px;

            padding: 10px 15px;

            font-size: 18px;

            font-weight: 700;

            background: #e0e0e0;

            color: #555;

            transition: all .15s ease;
        }


        /* =========================================================
           RECEBIDO
           ========================================================= */

        .btn-recebido {
            background: #e0e0e0 !important;

            color: #555 !important;
        }

        /*
         * Somente quando RECEBIDO estiver selecionado
         */
        #tipo_receber:checked + .btn-recebido {

            background: #4caf50 !important;

            color: #fff !important;

            box-shadow:
                0 3px 8px rgba(76, 175, 80, .35);
        }


        /* =========================================================
           PAGO
           ========================================================= */

        .btn-pago {
            background: #e0e0e0 !important;

            color: #555 !important;
        }

        /*
         * Somente quando PAGO estiver selecionado
         */
        #tipo_pagar:checked + .btn-pago {

            background: #dc3545 !important;

            color: #fff !important;

            box-shadow:
                0 3px 8px rgba(220, 53, 69, .35);
        }


        /*
         * Pequeno efeito ao clicar
         */
        .operacao-tabs .btn:active {
            transform: scale(.97);
        }


        /* =========================================================
           DATA / HORA
           ========================================================= */

        .data-hora {
            display: flex;

            gap: 7px;

            margin-bottom: 20px;
        }

        .campo-data,
        .campo-hora {

            background: #fff;

            border: 1px solid #d0d0d0;

            border-radius: 8px;

            min-height: 54px;

            display: flex;

            align-items: center;
        }

        .campo-data {
            flex: 1;
        }

        .campo-hora {
            width: 145px;
        }

        .campo-data input,
        .campo-hora input {

            border: 0;

            outline: none;

            box-shadow: none;

            width: 100%;

            height: 52px;

            background: transparent;

            font-size: 16px;

            font-weight: 600;
        }

        .campo-data input:focus,
        .campo-hora input:focus {

            border: 0;

            box-shadow: none;
        }

        .icone-azul {

            color: #2196f3;

            font-size: 22px;

            flex-shrink: 0;
        }


        /* =========================================================
           CAMPOS
           ========================================================= */

        .campo {
            margin-bottom: 20px;
        }

        .campo-label {

            display: block;

            margin-bottom: 7px;

            font-size: 13px;

            font-weight: 700;

            color: #6c757d;

            text-transform: uppercase;
        }

        .campo-label.verde {
            color: #267342;
        }

        .form-control,
        .form-select {

            min-height: 52px;

            border-radius: 8px;

            font-size: 17px;
        }

        .form-control:focus,
        .form-select:focus {

            border-color: #2196f3;

            box-shadow:
                0 0 0 .15rem rgba(33, 150, 243, .15);
        }


        /* =========================================================
           TIPO DE LANÇAMENTO
           ========================================================= */

        .tipo-lancamento {

            border: 1px solid #ccc;

            border-radius: 8px;

            padding: 7px;

            background: #fff;
        }

        .tipo-option {
            position: relative;
        }

        .tipo-option input {

            position: absolute;

            opacity: 0;

            pointer-events: none;
        }

        .tipo-option label {

            min-height: 52px;

            display: flex;

            align-items: center;

            gap: 8px;

            padding: 8px 10px;

            border: 2px solid transparent;

            border-radius: 8px;

            cursor: pointer;

            font-size: 17px;

            font-weight: 600;

            transition: .15s;
        }

        .tipo-option label:hover {

            background: #f3f8f4;
        }

        .tipo-option input:checked + label {

            background: #e8f5e9;

            border-color: #4caf50;

            color: #267342;
        }

        .tipo-icone {

            color: #4caf50;

            font-size: 22px;
        }


        /* =========================================================
           VALOR
           ========================================================= */

        .valor-group .input-group-text {

            background: #fff;

            border-right: 0;

            font-weight: 700;

            font-size: 18px;
        }

        .valor-group .form-control {

            border-left: 0;

            font-size: 25px;

            font-weight: 700;
        }

        .valor-group .form-control:focus {

            border-left: 0;
        }


        /* =========================================================
           STATUS
           ========================================================= */

        .status-box {

            background: #f8f9fa;

            border-radius: 9px;

            padding: 13px;

            margin-bottom: 20px;
        }


        /* =========================================================
           PARCELAMENTO
           ========================================================= */

        .parcelamento {
            margin-bottom: 20px;
        }


        /* =========================================================
           BOTÃO SALVAR
           ========================================================= */

        .btn-salvar {

            min-height: 58px;

            border-radius: 9px;

            font-size: 18px;

            font-weight: 700;

            width: 100%;
        }


        /* =========================================================
           MOBILE
           ========================================================= */

        @media (max-width: 575px) {

            .app-header {
                padding: 9px 10px;
            }

            .header-title {
                font-size: 21px;
            }

            .btn-dashboard {

                font-size: 13px;

                padding: 7px 8px;
            }

            .page-content {

                padding: 12px 8px 25px;
            }

            .lancamento-body {

                padding: 16px 12px;
            }

            .campo-hora {

                width: 135px;
            }

            .campo-data input,
            .campo-hora input {

                font-size: 14px;
            }

            .tipo-option label {

                font-size: 15px;

                padding: 7px;
            }

            .tipo-icone {

                font-size: 19px;
            }

            .operacao-tabs .btn {

                font-size: 16px;

                padding: 10px 8px;
            }

        }

    </style>

</head>


<body>


{{-- =============================================================
     HEADER
     ============================================================= --}}

<header class="app-header">

    <a
        href="/dashboard"
        class="btn-dashboard me-3"
    >

        <i class="bi bi-arrow-left me-1"></i>

        Dashboard

    </a>


    <div class="header-title">

        Novo Lançamento

    </div>

</header>



{{-- =============================================================
     CONTEÚDO
     ============================================================= --}}

<main class="page-content">


    {{-- =========================================================
         SUCESSO
         ========================================================= --}}

    @if(session('sucesso'))

        <div
            class="alert alert-success alert-dismissible fade show text-center"
            role="alert"
        >

            <i class="bi bi-check-circle-fill me-1"></i>

            {{ session('sucesso') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif



    {{-- =========================================================
         ERROS
         ========================================================= --}}

    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>
                Verifique os dados:
            </strong>

            <ul class="mb-0 mt-2 ps-3">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif



    {{-- =========================================================
         CARD PRINCIPAL
         ========================================================= --}}

    <div class="lancamento-card">

        <div class="lancamento-body">


            <form
                action="{{ route('contas.store') }}"
                method="POST"
                id="formLancamento"
            >

                @csrf



                {{-- =================================================
                     1. RECEBIDO / PAGO
                     ================================================= --}}

                <div class="operacao-tabs">


                    {{-- RECEBIDO --}}

                    <div>

                        <input
                            type="radio"
                            class="btn-check"
                            name="tipo"
                            id="tipo_receber"
                            value="receber"
                            autocomplete="off"
                            {{ old('tipo', 'receber') == 'receber' ? 'checked' : '' }}
                        >

                        <label
                            for="tipo_receber"
                            class="btn btn-recebido"
                        >

                            <i class="bi bi-arrow-down-circle me-1"></i>

                            Recebido

                        </label>

                    </div>



                    {{-- PAGO --}}

                    <div>

                        <input
                            type="radio"
                            class="btn-check"
                            name="tipo"
                            id="tipo_pagar"
                            value="pagar"
                            autocomplete="off"
                            {{ old('tipo') == 'pagar' ? 'checked' : '' }}
                        >

                        <label
                            for="tipo_pagar"
                            class="btn btn-pago"
                        >

                            <i class="bi bi-arrow-up-circle me-1"></i>

                            Pago

                        </label>

                    </div>

                </div>



                {{-- =================================================
                     2. DATA / HORA
                     ================================================= --}}

                <div class="data-hora">


                    <div class="campo-data">

                        <i
                            class="bi bi-calendar3 icone-azul ms-3 me-2"
                        ></i>

                        <input
                            type="date"
                            name="data_vencimento"
                            id="data_vencimento"
                            value="{{ old('data_vencimento', date('Y-m-d')) }}"
                            required
                        >

                    </div>



                    <div class="campo-hora">

                        <input
                            type="time"
                            name="hora"
                            id="hora"
                            value="{{ old('hora', date('H:i')) }}"
                        >

                        <i
                            class="bi bi-clock icone-azul me-3"
                        ></i>

                    </div>

                </div>



                {{-- =================================================
                     3. TIPO DE LANÇAMENTO
                     ================================================= --}}

                <div
                    class="campo"
                    id="campo_centro_custo"
                >

                    <label class="campo-label verde">

                        Tipo de lançamento

                    </label>


                    <div class="tipo-lancamento">

                        <div class="row g-1">


                            {{-- CORTE --}}

                            <div class="col-6">

                                <div class="tipo-option">

                                    <input
                                        type="radio"
                                        name="centro_custo"
                                        id="tipo_corte"
                                        value="corte"
                                        {{ old('centro_custo', 'corte') == 'corte' ? 'checked' : '' }}
                                    >

                                    <label for="tipo_corte">

                                        <i
                                            class="bi bi-scissors tipo-icone"
                                        ></i>

                                        Corte

                                    </label>

                                </div>

                            </div>



                            {{-- BARBA --}}

                            <div class="col-6">

                                <div class="tipo-option">

                                    <input
                                        type="radio"
                                        name="centro_custo"
                                        id="tipo_barba"
                                        value="barba"
                                        {{ old('centro_custo') == 'barba' ? 'checked' : '' }}
                                    >

                                    <label for="tipo_barba">

                                        <i
                                            class="bi bi-person-badge tipo-icone"
                                        ></i>

                                        Barba

                                    </label>

                                </div>

                            </div>



                            {{-- F1 --}}

                            <div class="col-6">

                                <div class="tipo-option">

                                    <input
                                        type="radio"
                                        name="centro_custo"
                                        id="tipo_f1"
                                        value="f1"
                                        {{ old('centro_custo') == 'f1' ? 'checked' : '' }}
                                    >

                                    <label for="tipo_f1">

                                        <i
                                            class="bi bi-star tipo-icone"
                                        ></i>

                                        F1

                                    </label>

                                </div>

                            </div>



                            {{-- OUTROS --}}

                            <div class="col-6">

                                <div class="tipo-option">

                                    <input
                                        type="radio"
                                        name="centro_custo"
                                        id="tipo_outros"
                                        value="outros"
                                        {{ old('centro_custo') == 'outros' ? 'checked' : '' }}
                                    >

                                    <label for="tipo_outros">

                                        <i
                                            class="bi bi-three-dots tipo-icone"
                                        ></i>

                                        Outros

                                    </label>

                                </div>

                            </div>


                        </div>

                    </div>

                </div>



                {{-- =================================================
                     4. DESCRIÇÃO / CLIENTE
                     ================================================= --}}

                <div class="campo">

                    <label
                        for="descricao"
                        class="campo-label"
                    >

                        Descrição / Cliente

                    </label>


                    <input
                        type="text"
                        name="descricao"
                        id="descricao"
                        class="form-control"
                        placeholder="Ex: João - Corte"
                        value="{{ old('descricao') }}"
                    >

                </div>



                {{-- =================================================
                     5. VALOR
                     ================================================= --}}

                <div class="campo">

                    <label
                        for="valor_total"
                        class="campo-label"
                    >

                        Valor

                    </label>


                    <div
                        class="input-group input-group-lg valor-group"
                    >

                        <span class="input-group-text">
                            R$
                        </span>


                        <input
                            type="number"
                            step="0.01"
                            min="0.01"
                            name="valor_total"
                            id="valor_total"
                            class="form-control"
                            placeholder="0,00"
                            value="{{ old('valor_total') }}"
                            required
                        >

                    </div>

                </div>



                {{-- =================================================
                     6. STATUS / DATA PAGAMENTO
                     ================================================= --}}

                <div class="status-box">

                    <div class="row g-2">


                        {{-- STATUS --}}

                        <div class="col-12 col-sm-6">

                            <label
                                for="status"
                                class="campo-label"
                            >

                                Status

                            </label>


                            <select
                                name="status"
                                id="status"
                                class="form-select"
                            >

                                <option
                                    value="pago"
                                    {{ old('status', 'pago') == 'pago' ? 'selected' : '' }}
                                >
                                    ✅ Pago / Recebido
                                </option>


                                <option
                                    value="pendente"
                                    {{ old('status') == 'pendente' ? 'selected' : '' }}
                                >
                                    ⏳ Pendente
                                </option>

                            </select>

                        </div>



                        {{-- DATA PAGAMENTO --}}

                        <div
                            class="col-12 col-sm-6"
                            id="campo_data_pagamento"
                        >

                            <label
                                for="data_pagamento"
                                class="campo-label"
                            >

                                Data do Pagamento

                            </label>


                            <input
                                type="date"
                                name="data_pagamento"
                                id="data_pagamento"
                                class="form-control"
                                value="{{ old('data_pagamento', date('Y-m-d')) }}"
                            >

                        </div>


                    </div>

                </div>



                {{-- =================================================
                     7. PARCELAMENTO
                     ================================================= --}}

                <div class="parcelamento">

                    <div class="row g-2">


                        {{-- PARCELAS --}}

                        <div class="col-6">

                            <label
                                for="parcelas"
                                class="campo-label"
                            >

                                Nº Parcelas

                            </label>


                            <select
                                name="parcelas"
                                id="parcelas"
                                class="form-select"
                            >

                                @for ($i = 1; $i <= 12; $i++)

                                    <option
                                        value="{{ $i }}"
                                        {{ old('parcelas', 1) == $i ? 'selected' : '' }}
                                    >

                                        {{ $i }}x

                                        @if($i == 1)
                                            (À vista)
                                        @endif

                                    </option>

                                @endfor

                            </select>

                        </div>



                        {{-- INTERVALO --}}

                        <div class="col-6">

                            <label
                                for="intervalo"
                                class="campo-label"
                            >

                                Intervalo

                            </label>


                            <select
                                name="intervalo"
                                id="intervalo"
                                class="form-select"
                            >

                                <option
                                    value="diario"
                                    {{ old('intervalo') == 'diario' ? 'selected' : '' }}
                                >
                                    Diário
                                </option>


                                <option
                                    value="mensal"
                                    {{ old('intervalo', 'mensal') == 'mensal' ? 'selected' : '' }}
                                >
                                    Mensal
                                </option>


                                <option
                                    value="30_dias"
                                    {{ old('intervalo') == '30_dias' ? 'selected' : '' }}
                                >
                                    30 Dias
                                </option>


                                <option
                                    value="quinzenal"
                                    {{ old('intervalo') == 'quinzenal' ? 'selected' : '' }}
                                >
                                    Quinzenal
                                </option>


                                <option
                                    value="semanal"
                                    {{ old('intervalo') == 'semanal' ? 'selected' : '' }}
                                >
                                    Semanal
                                </option>

                            </select>

                        </div>

                    </div>

                </div>



                {{-- =================================================
                     BOTÃO SALVAR
                     ================================================= --}}

                <button
                    type="submit"
                    class="btn btn-primary btn-salvar shadow-sm"
                >

                    <i class="bi bi-check-lg me-1"></i>

                    Salvar Lançamento

                </button>


            </form>

        </div>

    </div>

</main>



{{-- Bootstrap JS --}}

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>



<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
     * ==========================================================
     * ELEMENTOS
     * ==========================================================
     */

    const tipoReceber =
        document.getElementById('tipo_receber');

    const tipoPagar =
        document.getElementById('tipo_pagar');

    const campoCentroCusto =
        document.getElementById('campo_centro_custo');

    const centroCustoInputs =
        document.querySelectorAll(
            'input[name="centro_custo"]'
        );

    const status =
        document.getElementById('status');

    const campoDataPagamento =
        document.getElementById('campo_data_pagamento');

    const dataPagamento =
        document.getElementById('data_pagamento');


    /*
     * ==========================================================
     * RECEBIDO / PAGO
     * ==========================================================
     */

    function atualizarTipoLancamento() {

        if (tipoReceber.checked) {

            /*
             * RECEBIDO
             *
             * Mostra os tipos de lançamento.
             */

            campoCentroCusto.classList.remove('d-none');

            centroCustoInputs.forEach(function (input) {

                input.disabled = false;

                input.required = true;

            });

        } else {

            /*
             * PAGO
             *
             * Esconde os tipos de lançamento.
             */

            campoCentroCusto.classList.add('d-none');

            centroCustoInputs.forEach(function (input) {

                input.disabled = true;

                input.required = false;

                input.checked = false;

            });

        }

    }


    /*
     * ==========================================================
     * STATUS / DATA DE PAGAMENTO
     * ==========================================================
     */

    function atualizarDataPagamento() {

        if (status.value === 'pago') {

            campoDataPagamento.classList.remove('d-none');

            dataPagamento.disabled = false;

            dataPagamento.required = true;

        } else {

            campoDataPagamento.classList.add('d-none');

            dataPagamento.disabled = true;

            dataPagamento.required = false;

        }

    }


    /*
     * ==========================================================
     * EVENTOS
     * ==========================================================
     */

    tipoReceber.addEventListener(
        'change',
        atualizarTipoLancamento
    );

    tipoPagar.addEventListener(
        'change',
        atualizarTipoLancamento
    );

    status.addEventListener(
        'change',
        atualizarDataPagamento
    );


    /*
     * ==========================================================
     * INICIALIZAÇÃO
     * ==========================================================
     */

    atualizarTipoLancamento();

    atualizarDataPagamento();

});

</script>


</body>

</html>