<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'ERP Financeiro')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Alpine -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

    @stack('styles')

    <style>
        /* =========================================================
           CORREÇÕES GERAIS DE RESPONSIVIDADE
           ========================================================= */

        html,
        body {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        body {
            min-width: 0;
        }

        .wrapper {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        /*
         * Importante em layouts com sidebar.
         * Impede que o conteúdo interno aumente a largura
         * da página inteira.
         */
        .main {
            min-width: 0;
            max-width: 100%;
            width: 100%;
        }

        .content {
            min-width: 0;
            max-width: 100%;
            width: 100%;
            overflow-x: hidden;
        }

        /* Bootstrap Row pode causar pequenos estouros */
        .content .row {
            max-width: 100%;
        }

        /* Cards nunca devem ultrapassar a tela */
        .content .card {
            max-width: 100%;
        }

        /* Imagens */
        .content img {
            max-width: 100%;
            height: auto;
        }

        /* =========================================================
           CELULAR
           ========================================================= */

        @media (max-width: 767.98px) {

            html,
            body {
                width: 100%;
                max-width: 100%;
                overflow-x: hidden;
            }

            .main {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            .content {
                width: 100%;
                max-width: 100%;
                min-width: 0;
                padding-left: 0;
                padding-right: 0;
                overflow-x: hidden;
            }

            /*
             * Evita que o Bootstrap gere uma largura maior
             * por causa das margens negativas do .row.
             */
            .content .row {
                margin-left: 0;
                margin-right: 0;
            }

            .content .container,
            .content .container-fluid {
                width: 100%;
                max-width: 100%;
            }

            /*
             * Tamanho confortável para os cards no celular.
             */
            .content .card {
                width: 100%;
                max-width: 100%;
            }

            .content .card-body {
                max-width: 100%;
            }

            /*
             * Textos muito grandes não podem criar overflow.
             */
            .content h1,
            .content h2,
            .content h3,
            .content h4,
            .content h5,
            .content h6,
            .content p,
            .content span,
            .content div {
                max-width: 100%;
            }

            /*
             * Tabela pode rolar SOMENTE dentro da própria área.
             * Ela não deve criar scroll na página inteira.
             */
            .content .table-responsive {
                width: 100%;
                max-width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        /* =========================================================
           TELAS MUITO PEQUENAS
           ========================================================= */

        @media (max-width: 400px) {

            .content .container,
            .content .container-fluid {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .content .card-body {
                padding: 10px !important;
            }
        }
    </style>

</head>

<body>

<div class="wrapper" x-data="{ menuAberto: true }">

    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Conteúdo -->
    <div class="main">

        <!-- Navbar -->
        @include('components.navbar')

        <!-- Página -->
        <main class="content">

            @yield('content')

        </main>

        <!-- Rodapé -->
        <footer class="footer">

            <small>
                ERP Financeiro © {{ date('Y') }}
            </small>

        </footer>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('js/app.js') }}"></script>

@stack('scripts')

</body>

</html>