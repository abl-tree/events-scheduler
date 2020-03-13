<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        .container {
            width: 100%!important;
        }
        .form-group {
            margin-bottom: unset!important;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container">
                <!-- Image and text -->
                <nav class="navbar">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <span class="d-none d-lg-inline">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                </nav>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                <home-component></home-component>
            </div>
        </main>
    </div>
</body>
</html>
