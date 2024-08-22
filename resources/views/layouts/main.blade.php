<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ env('APP_NAME') }}</title>

    @stack('style')
    <link rel="stylesheet" crossorigin href="/assets/compiled/css/app.css">
    <link rel="stylesheet" crossorigin href="/assets/compiled/css/app-dark.css">
</head>

<body>
    <script src="/assets/static/js/initTheme.js"></script>
    <div id="app">
        @include('layouts.sidebar')
        <div id="main" class='layout-navbar navbar-fixed'>
            <header>
                @include('layouts.navbar')
            </header>
            <div id="main-content">
                <div class="page-heading">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <script src="/assets/static/js/components/dark.js"></script>
    <script src="/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>

    <script src="/assets/compiled/js/app.js"></script>
    @stack('script')
</body>

</html>
