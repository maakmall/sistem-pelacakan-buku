@extends('layouts.otentikasi')

@section('content')
<div class="row h-100">
    <div class="col-lg-5 col-12">
        <div id="auth-left">
            <div class="auth-logo row align-items-center mb-5">
                <div class="col-3">
                    <img
                        class="h-auto img-fluid"
                        src="/assets/static/images/logo/logo-siu.png"
                        alt="Logo"
                    />
                </div>
                <div class="col">
                    <h2 class="fs-3 lh-base pt-3">
                        Perpustakaan <br>
                        STTIKOM Insan Unggul
                    </h2>
                </div>
            </div>

            <p class="auth-subtitle mb-4">Masuk Pustakawan</p>

            <form action="/login" method="POST">
                @csrf
                <div class="form-group position-relative has-icon-left mb-4">
                    <input
                        type="text"
                        class="form-control form-control-xl"
                        placeholder="Username"
                        name="username"
                    />
                    <div class="invalid-feedback"></div>
                    <div class="form-control-icon">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
                <div class="form-group position-relative has-icon-left mb-4">
                    <input
                        type="password"
                        class="form-control form-control-xl"
                        placeholder="Password"
                        name="password"
                    />
                    <div class="invalid-feedback"></div>
                    <div class="form-control-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                </div>
                <button class="btn btn-primary btn-block btn-lg shadow-lg">
                    <span class="spinner-border spinner-border-sm mx-3 d-none" role="status" aria-hidden="true"></span>
                    <span class="visually-hidden">Loading...</span>
                    <span>Log in</span>
                </button>
            </form>
        </div>
    </div>
    <div class="col-lg-7 d-none d-lg-block">
        <div id="auth-right"></div>
    </div>
</div>
@endsection

@push('script')
    <script>
        const form = document.forms[0];
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const button = form.querySelector('button');
            button.disabled = true;
            button.children[0].classList.remove('d-none');
            button.children[2].innerHTML = '';

            const response = await fetch(form.action, {
                method: form.method,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form)
            });

            if (response.ok) {
                form.querySelectorAll('.is-invalid').forEach(e => {
                    e.classList.remove('is-invalid');
                });

                location.href = '/';
            } else {
                const { errors } = await response.json();

                form.querySelectorAll('input:not([type="hidden"])').forEach(e => {
                    if (e.name in errors) {
                        e.classList.add('is-invalid');
                        e.nextElementSibling.innerHTML = errors[e.name];
                    } else {
                        e.classList.remove('is-invalid');
                        e.nextElementSibling.innerHTML = '';
                    }
                });
            }

            button.disabled = false;
            button.children[0].classList.add('d-none');
            button.children[2].innerHTML = 'Log In';
        });
    </script>
@endpush
