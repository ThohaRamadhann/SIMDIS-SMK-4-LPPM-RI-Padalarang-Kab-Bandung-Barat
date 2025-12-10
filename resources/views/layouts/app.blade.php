<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div
        x-data="{ sidebarOpen: false }"
        @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
        class="min-h-screen flex flex-col"
    >
    
        {{-- NAVBAR --}}
        <livewire:layout.navigation />
    
        <div class="flex flex-1">
    
             {{-- SIDEBAR --}}
             @auth
             <aside
                 x-cloak
                 :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                 class="fixed lg:static z-50 lg:translate-x-0
                        w-64 bg-gray-800 text-white min-h-screen
                        transition-transform duration-200 ease-in-out"
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
     
             {{-- CONTENT --}}
             <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                 @isset($header)
                     <div class="mb-4">
                         {{ $header }}
                     </div>
                 @endisset
     
                 {{ $slot }}
             </main>
     
         </div>
     </div>
 
     {{-- Alpine.js sudah include via @vite di app.js --}}
 </body>
 </html>
