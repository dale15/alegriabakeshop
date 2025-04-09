<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-[#FDFDFC] text-[#1b1b18]">

    <header class="bg-black text-white shadow-md">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">

                <div class="flex items-center">
                    <a href="/">
                        <img src="{{ asset('images/text_logo.jpg') }}" alt="logo" class="w-full h-24 object-cover" />
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <span>{{ now()->format('M d, Y h:i A') }}</span>

                    <div class="relative" x-data="{ open: false }">
                        <button class="flex items-center space-x-1" x-on:click="open = !open">
                            <span>Cashier: {{ Auth::user()->name ?? 'User' }}</span>

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div class="absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg" x-show="open"
                            x-on:click.away="open = false" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95">

                            @if (Auth::user())
                                <a href="/admin"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                <a href="/admin/sales-overview"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sales
                                    Report</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"> Logout </a>
                            @else
                                <a href="/admin/login" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Login </a>
                            @endif

                            {{-- <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6 h-screen">
        {{ $slot }}
    </main>

    <footer class="bg-gray-200 shadow-sm">
        <div class="w-full mx-auto max-w-screen-xl p-4 md:flex md:items-center md:justify-between">
            <img class="w-24 h-24" src="{{ asset('images/cajidatech.png') }}" />

            <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2025 Cajida Tech. All Rights
                Reserved.
            </span>
            {{-- <ul
                class="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-500 dark:text-gray-400 sm:mt-0">
                <li>
                    <a href="#" class="hover:underline me-4 md:me-6">About</a>
                </li>
                <li>
                    <a href="#" class="hover:underline me-4 md:me-6">Privacy Policy</a>
                </li>
                <li>
                    <a href="#" class="hover:underline me-4 md:me-6">Licensing</a>
                </li>
                <li>
                    <a href="#" class="hover:underline">Contact</a>
                </li>
            </ul> --}}
        </div>
    </footer>

    @livewireScripts
</body>

</html>