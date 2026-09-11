<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Employee Portal - ' . config('app.name', 'TechMage') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="h-full antialiased font-sans bg-slate-50 text-slate-900" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex flex-col md:flex-row">
            <!-- Employee Sidebar -->
            <livewire:employee.sidebar />

            <!-- Main Content Container -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Employee Header -->
                <livewire:employee.header />

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-mobile-restriction-popup />

        @livewireScripts
    </body>
</html>
