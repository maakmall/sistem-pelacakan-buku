<nav class="navbar navbar-expand navbar-light navbar-top">
    <div class="container-fluid">
        <a href="#" class="burger-btn d-block">
            <i class="bi bi-justify fs-3"></i>
        </a>
        <div class="dropdown ms-auto">
            <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-menu d-flex">
                    <div class="user-name text-end me-3">
                        <h6 class="mb-0 text-gray-600">
                            {{ auth()->user()->username }}
                        </h6>
                        <p class="mb-0 text-sm text-gray-600">
                            {{ auth()->user()->realname }}
                        </p>
                    </div>
                    <div class="user-img d-flex align-items-center">
                        <div class="avatar avatar-md">
                            <img
                                src="/assets/static/images/faces/{{ auth()->user()->user_image }}"
                            />
                        </div>
                    </div>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" style="min-width: 11rem;">
                <li>
                    <h6 class="dropdown-header">
                        Hello, {{ auth()->user()->username }}!
                    </h6>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form action="/logout" method="post">
                        @csrf
                        <button class="dropdown-item">
                            <i class="icon-mid bi bi-box-arrow-left me-2"></i>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
