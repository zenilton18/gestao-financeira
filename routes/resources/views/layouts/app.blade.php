<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
          <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ERP Financeiro')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    <!-- Alpine -->
    <script defer
            src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- CSS -->

    <link rel="stylesheet"
          href="{{ asset('css/app.css') }}">

    <link rel="stylesheet"
          href="{{ asset('css/sidebar.css') }}">

    <link rel="stylesheet"
          href="{{ asset('css/navbar.css') }}">

    @stack('styles')

</head>

<body>

<div class="wrapper">

    <!-- Sidebar -->

    @include('components.sidebar')

    <!-- Área Principal -->

    <div class="main">

        <!-- Navbar -->

        @include('components.navbar')

        <!-- Conteúdo -->

        <main class="content">

            @yield('content')

        </main>

        <!-- Rodapé -->

        <footer class="footer">

            ERP Financeiro © {{ date('Y') }}

        </footer>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('js/app.js') }}"></script>

@stack('scripts')

</body>

</html>