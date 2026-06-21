<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="beams-instance-id" content="{{ config('services.pusher_beams.instance_id') }}">
    @auth
    <meta name="user-id" content="{{ auth()->user()->id_pengguna }}">
    @endauth

    <title>{{ config('app.name', 'SIMDIS') }}</title>

    <link rel="icon" type="images/png" href="{{ asset('images/logo_simdis.png') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://js.pusher.com/beams/2.1.0/push-notifications-cdn.js"></script>
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    {{-- TOM SELECT --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes loadingSlide {
            0% {
                width: 0%;
                opacity: 1;
            }

            80% {
                width: 90%;
                opacity: 1;
            }

            100% {
                width: 100%;
                opacity: 0;
            }
        }

        @keyframes loadingPulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .simdis-loading-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, #F5B800, #FFD84D, #F5B800);
            z-index: 99999;
            animation: loadingSlide 1.8s ease-in-out forwards;
            box-shadow: 0 0 8px rgba(245, 184, 0, 0.6);
        }

        .simdis-loading-overlay {
            position: fixed;
            inset: 0;
            z-index: 99998;
            background: rgba(240, 244, 251, 0.4);
            backdrop-filter: blur(1px);
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .simdis-loading-spinner {
            width: 36px;
            height: 36px;
            border: 3px solid rgba(13, 45, 107, 0.15);
            border-top-color: #0D2D6B;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="font-sans antialiased" style="background: #F0F4FB;">

    {{-- ── Global Loading Indicator ── --}}
    <div x-data="{ loading: false }" x-on:livewire:navigate.window="loading = true"
        x-on:livewire:navigated.window="loading = false">
        <div x-show="loading" x-cloak class="simdis-loading-bar"></div>
        <div x-show="loading" x-cloak class="simdis-loading-overlay">
            <div class="simdis-loading-spinner"></div>
        </div>
    </div>

    <div x-data="{ sidebarOpen: false }" @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
        class="flex flex-col h-screen overflow-hidden">

        <div class="flex-shrink-0">
            <livewire:layout.navigation />
            <livewire:notifikasi.notifikasi-realtime />
        </div>

        <div class="flex flex-1 overflow-hidden">

            @auth
                <aside x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                    class="fixed lg:relative z-50 lg:translate-x-0
                           w-64 flex-shrink-0 h-full overflow-hidden
                           transition-transform duration-300 ease-in-out"
                    style="background-color: #091E4A;">
                    @include('layouts.sidebar')
                </aside>
            @endauth

            <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity
                class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

            <main class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-6">
                @isset($header)
                    <div class="mb-4">{{ $header }}</div>
                @endisset
                {{ $slot }}
            </main>

        </div>
    </div>

    @livewireScripts

    {{-- Prevent back button setelah logout --}}
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                fetch('/dashboard', {
                    method: 'HEAD',
                    credentials: 'same-origin'
                }).then(function (res) {
                    if (res.redirected || res.url.includes('login')) {
                        window.location.replace('/login');
                    }
                }).catch(function () {
                    window.location.replace('/login');
                });
            }
        });
    </script>

</body>

</html>
