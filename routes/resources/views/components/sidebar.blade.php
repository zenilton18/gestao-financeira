
<aside class="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">

        <div class="logo-icon">
            <i class="bi bi-wallet2"></i>
        </div>

        <div>

            <h5 class="mb-0">
                ERP Financeiro
            </h5>

            <small>Sistema de Gestão</small>

        </div>

    </div>

    <!-- Menu -->

    <nav class="sidebar-menu">

        <a href="#" class="menu-item active">

            <i class="bi bi-speedometer2"></i>

            <span>Dashboard</span>

        </a>

        <div x-data="{ aberto:true }">

            <button
                class="menu-item menu-dropdown"
                @click="aberto=!aberto">

                <div>

                    <i class="bi bi-cash-stack"></i>

                    <span>Financeiro</span>

                </div>

                <i
                    class="bi"
                    :class="aberto ? 'bi-chevron-down' : 'bi-chevron-right'">
                </i>

            </button>

            <div
                x-show="aberto"
                x-transition
                class="submenu">

                <a href="#" class="submenu-item">
                    Novo Lançamento
                </a>

                <a href="#" class="submenu-item">
                    Contas a Pagar
                </a>

                <a href="#" class="submenu-item">
                    Contas a Receber
                </a>

                <a href="#" class="submenu-item">
                    Fluxo de Caixa
                </a>

            </div>

        </div>

        <div x-data="{ aberto:false }">

            <button
                class="menu-item menu-dropdown"
                @click="aberto=!aberto">

                <div>

                    <i class="bi bi-box-seam"></i>

                    <span>Estoque</span>

                </div>

                <i
                    class="bi"
                    :class="aberto ? 'bi-chevron-down' : 'bi-chevron-right'">
                </i>

            </button>

            <div
                x-show="aberto"
                x-transition
                class="submenu">

                <a href="#" class="submenu-item">
                    Produtos
                </a>

                <a href="#" class="submenu-item">
                    Entradas
                </a>

                <a href="#" class="submenu-item">
                    Saídas
                </a>

            </div>

        </div>

        <a href="#" class="menu-item">

            <i class="bi bi-people"></i>

            <span>Clientes</span>

        </a>

        <a href="#" class="menu-item">

            <i class="bi bi-truck"></i>

            <span>Fornecedores</span>

        </a>

        <a href="#" class="menu-item">

            <i class="bi bi-file-earmark-text"></i>

            <span>Relatórios</span>

        </a>

        <a href="#" class="menu-item">

            <i class="bi bi-gear"></i>

            <span>Configurações</span>

        </a>

    </nav>

    <!-- Rodapé -->

    <div class="sidebar-footer">

        <div class="user-avatar">

            Z

        </div>

        <div>

            <strong>Zenilton Sousa</strong>

            <small class="d-block text-muted">
                Administrador
            </small>

        </div>

    </div>

</aside>