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
        :root {
            --bolopa-header-height: 72px; /* approximate header height (matches header padding + content) */
            --bolopa-footer-height: 48px; /* approximate footer height */
        }
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
            /* make the home-section fill the viewport so header/footer can stay put */
            flex: 1;
            height: 100vh;
            left: 78px;
            width: calc(100% - 78px);
            transition: all 0.5s ease;
            z-index: 2;
            display: flex;
            flex-direction: column;
            /* prevent page itself from scrolling so the inner page-content can scroll */
            overflow: hidden;
        }

        .bolopa-sidebar-vigazafarm.open ~ .home-section {
            left: 250px;
            width: calc(100% - 250px);
        }

        .page-content {
            /* this area will handle scrolling so header/footer remain visible */
            flex: 1;
            padding: 20px;
            overflow: auto;
            /* allow the flex child to shrink below its content for correct scrolling */
            min-height: 0;
            /* ensure content is not hidden behind sticky header/footer */
            padding-top: calc(var(--bolopa-header-height) + 8px);
            padding-bottom: calc(var(--bolopa-footer-height) + 12px);
        }

        @media (max-width: 420px) {
            .home-section {
                left: 78px;
                width: calc(100% - 78px);
            }

            .bolopa-sidebar-vigazafarm.open ~ .home-section {
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
