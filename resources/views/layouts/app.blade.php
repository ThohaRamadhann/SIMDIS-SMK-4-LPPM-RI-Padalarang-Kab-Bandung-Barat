<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIMDIS') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased" style="background: #F0F4FB;">

    <div
        x-data="{ sidebarOpen: false }"
        @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
        class="flex flex-col h-screen overflow-hidden"
    >

        {{-- NAVBAR — fixed di atas, tidak ikut scroll --}}
        <div class="flex-shrink-0">
            <livewire:layout.navigation />
        </div>

        <div class="flex flex-1 overflow-hidden">

            {{-- SIDEBAR --}}
            @auth
            <aside
                x-cloak
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed lg:relative z-50 lg:translate-x-0
                       w-64 flex-shrink-0
                       h-full overflow-y-auto
                       transition-transform duration-300 ease-in-out"
                style="background: linear-gradient(180deg, #091E4A 0%, #0D2D6B 60%, #163580 100%);"
            >
                @include('layouts.sidebar')
            </aside>
            @endauth

            {{-- OVERLAY MOBILE --}}
            <div
                x-show="sidebarOpen"
                @click="sidebarOpen = false"
                x-transition.opacity
                class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            ></div>

            {{-- CONTENT — yang scroll hanya area ini --}}
            <main class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-6">
                @isset($header)
                    <div class="mb-4">
                        {{ $header }}
                    </div>
                @endisset

                {{ $slot }}
            </main>

        </div>
    </div>

    @livewireScripts
</body>
</html>