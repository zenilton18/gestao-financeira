
<nav class="navbar-custom">

    <!-- Esquerda -->
    <div class="navbar-left">

        <button
            class="btn-menu"
            @click="menuAberto = !menuAberto">

            <i class="bi bi-list"></i>

        </button>

        <div>

            <h4 class="page-title mb-0">

                @yield('title')

            </h4>

            <small class="text-muted">

                Bem-vindo ao ERP Financeiro

            </small>

        </div>

    </div>


    <!-- Centro -->

    <div class="navbar-center">

        <div class="search-box">

            <i class="bi bi-search"></i>

            <input
                type="text"
                placeholder="Pesquisar...">

        </div>

    </div>


    <!-- Direita -->

    <div class="navbar-right">

        <button class="icon-button">

            <i class="bi bi-bell"></i>

            <span class="badge-notification">
                3
            </span>

        </button>

        <button class="icon-button">

            <i class="bi bi-envelope"></i>

        </button>

        <div class="user-profile">

            <div class="user-avatar">

                Z

            </div>

            <div>

                <strong>

                    Zenilton

                </strong>

                <small class="d-block text-muted">

                    Administrador

                </small>

            </div>

            <i class="bi bi-chevron-down"></i>

        </div>

    </div>

</nav>