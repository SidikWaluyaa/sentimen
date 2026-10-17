<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ShopeeFood Sentiment Analysis') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='25' fill='%23ee4d2d'/><circle cx='50' cy='30' r='5' fill='white'/><path d='M28 50 C28 38 38 28 50 28 C62 28 72 38 72 50 Z' fill='white'/><rect x='24' y='51' width='52' height='5' rx='2.5' fill='white'/><path d='M32 65 C40 73 60 73 68 65' stroke='white' stroke-width='5' stroke-linecap='round'/></svg>">

        <!-- Google Fonts: Outfit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts & Styles via Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#0b0c10] text-[#f1f2f6] relative min-h-screen">
        <!-- Glowing Aurora Orbs -->
        <div class="aurora-bg" aria-hidden="true">
            <div class="aurora-orb-1"></div>
            <div class="aurora-orb-2"></div>
        </div>

        <div x-data="{ sidebarOpen: false }" class="relative z-10 min-h-screen flex flex-col">
            <!-- Sidebar Navigation (Desktop Fixed & Mobile Drawer) -->
            @include('layouts.navigation')

            <!-- Main Content Area with Left Padding on Desktop -->
            <div class="lg:pl-64 flex flex-col flex-1 min-h-screen">
                <!-- Slim Glassy Topbar -->
                <header class="h-20 bg-cosmos-900/60 backdrop-blur-xl border-b border-white/5 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                    <!-- Left: Mobile Hamburger & Page Context -->
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = true"
                                class="lg:hidden p-2 rounded-xl bg-white/5 border border-white/10 text-gray-300 hover:text-white hover:bg-white/10 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        
                        <!-- Breadcrumb Title -->
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-gray-500 font-semibold uppercase tracking-wider hidden sm:inline">ShopeeFood Analytics</span>
                            <span class="text-gray-600 hidden sm:inline">/</span>
                            <span class="text-gray-200 font-bold">
                                @if(request()->routeIs('dashboard'))
                                    Dashboard
                                @elseif(request()->routeIs('datasets.*'))
                                    Upload Dataset
                                @elseif(request()->routeIs('prediction.single*'))
                                    Simulasi Ulasan
                                @elseif(request()->routeIs('admin.users.*'))
                                    Kelola Pengguna
                                @elseif(request()->routeIs('profile.*'))
                                    Profil Akun
                                @else
                                    Analytics
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Right: Live Clock WIB & Status Badges -->
                    <div class="flex items-center gap-3">
                        <!-- Live WIB Clock Badge -->
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-xs text-gray-300 shadow-sm"
                             x-data="{
                                time: '',
                                init() {
                                    const update = () => {
                                        const now = new Date();
                                        this.time = now.toLocaleTimeString('id-ID', {
                                            timeZone: 'Asia/Jakarta',
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            second: '2-digit'
                                        }).replace(/\./g, ':');
                                    };
                                    update();
                                    setInterval(update, 1000);
                                }
                             }">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="font-mono text-xs font-semibold text-gray-200" x-text="time"></span>
                            <span class="text-[10px] uppercase font-bold text-shopee-light px-1.5 py-0.5 rounded bg-shopee/10 border border-shopee/20">WIB</span>
                        </div>

                        <!-- System Status -->
                        <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Engine: Active</span>
                        </div>
                    </div>
                </header>

                <!-- Global Toast / Alert Notifications -->
                <div class="px-4 sm:px-6 lg:px-8 w-full mt-4">
                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 backdrop-blur-md shadow-lg shadow-emerald-500/5">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-medium">{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-emerald-400/70 hover:text-emerald-400">&times;</button>
                        </div>
                    @endif

                    @if (session('error') || $errors->any())
                        <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 backdrop-blur-md shadow-lg shadow-rose-500/5">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-medium">
                                    {{ session('error') ?? $errors->first() }}
                                </span>
                            </div>
                            <button @click="show = false" class="text-rose-400/70 hover:text-rose-400">&times;</button>
                        </div>
                    @endif
                </div>

                <!-- Page Main Content Slot -->
                <main class="flex-grow py-6 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="mt-auto border-t border-white/5 py-6 px-4 sm:px-6 lg:px-8 bg-cosmos-900/60 backdrop-blur-md text-center text-xs text-gray-500">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-2">
                        <p>© {{ date('Y') }} Sistem Analisis Sentimen ShopeeFood - Powered by Laravel 12 & Pure PHP Naive Bayes</p>
                        <div class="flex items-center gap-3">
                            <span class="text-gray-400 text-[11px]">Metode: TF-IDF & Multinomial Naive Bayes</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
