<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIMDIS') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo_simdis.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .guest-page-wrapper {
                min-height: 100vh;
                width: 100%;
                background: #F3F4F6;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px 16px;
                box-sizing: border-box;
            }

            .guest-container {
                width: 100%;
                max-width: 420px;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="guest-page-wrapper">
            <div class="guest-container">
                {{ $slot }}
            </div>
        </div>

        {{-- Kalau sudah di halaman login tapi user pencet back,
             replace history supaya tidak bisa balik ke halaman protected --}}
        <script>
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    window.location.replace('/login');
                }
            });
        </script>

    </body>
</html>