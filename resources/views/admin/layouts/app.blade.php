<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vigaza Farm') | Vigaza Farm</title>
    <link rel="icon" type="image/png" href="{{ asset('bolopa/img/icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .home-section {
            position: relative;
            background: #E4E9F7;
            flex: 1;
            left: 78px;
            width: calc(100% - 78px);
            transition: all 0.5s ease;
            z-index: 2;
            display: flex;
            flex-direction: column;
        }

        .bolopa-sidebar.open ~ .home-section {
            left: 250px;
            width: calc(100% - 250px);
        }

        .page-content {
            flex: 1;
            padding: 20px;
        }

        @media (max-width: 420px) {
            .home-section {
                left: 78px;
                width: calc(100% - 78px);
            }

            .bolopa-sidebar.open ~ .home-section {
                left: 250px;
                width: calc(100% - 250px);
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <section class="home-section">
        @php
            $title = $title ?? 'Dashboard';
        @endphp
        @include('admin.partials.header')

        <div class="page-content">
            @yield('content')
        </div>

        @include('admin.partials.footer')
    </section>

    @stack('scripts')
</body>
</html>
