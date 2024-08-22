<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <img
                        class="h-auto img-fluid"
                        src="/assets/static/images/logo/logo-siu.png"
                        alt="Logo"
                        width="50"
                    />
                    <span class="fs-5">Insan Unggul</span>
                </div>
                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block">
                        <i class="bi bi-x bi-middle"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <li @class([
                    'sidebar-item',
                    'has-sub',
                    'active' => request()->is('*')
                ])>
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-book"></i>
                        <span>Bibliografi</span>
                    </a>

                    <ul class="submenu">
                        <li @class([
                            'submenu-item',
                            'active' => request()->is('/')
                        ])>
                            <a href="/" class="submenu-link">Bibliografi</a>
                        </li>

                        <li @class([
                            'submenu-item',
                            'active' => request()->is('create')
                        ])>
                            <a href="/create" class="submenu-link">
                                Tambah Bibliografi Baru
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
