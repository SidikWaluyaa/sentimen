<!-- ============================================== -->
<!-- DESKTOP SIDEBAR (Fixed Left, Width 260px)       -->
<!-- ============================================== -->
<aside class="hidden lg:flex fixed inset-y-0 left-0 w-64 flex-col bg-cosmos-900/90 backdrop-blur-2xl border-r border-white/10 z-40 select-none">
    <!-- Brand Logo Section -->
    <div class="h-20 flex items-center px-6 border-b border-white/5">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group w-full">
            <x-application-logo class="w-11 h-11 flex-shrink-0 group-hover:scale-105 transition-transform duration-200" />
            <div class="flex flex-col min-w-0">
                <div class="flex items-center gap-1.5">
                    <span class="text-sm font-black tracking-wider text-gradient-shopee uppercase truncate">ShopeeFood</span>
                    <span class="text-[9px] px-1.5 py-0.2 rounded font-bold uppercase tracking-wider bg-shopee/10 text-shopee-light border border-shopee/20">AI</span>
                </div>
                <span class="text-xs font-bold text-gray-200 truncate">Analisis Sentimen</span>
                <span class="text-[9px] text-gray-500 font-medium tracking-widest uppercase">Laravel 12 Engine</span>
            </div>
        </a>
    </div>

    <!-- Navigation Menu Items -->
    <div class="flex-1 px-4 py-6 space-y-6 overflow-y-auto custom-scrollbar">
        <!-- Main Menu Section -->
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                Menu Utama
            </div>
            <nav class="space-y-1.5">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-shopee/20 text-shopee-light border border-shopee/30 shadow-lg shadow-shopee/10' : 'text-gray-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-shopee-light' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Upload Dataset -->
                <a href="{{ route('datasets.create') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('datasets.*') ? 'bg-shopee/20 text-shopee-light border border-shopee/30 shadow-lg shadow-shopee/10' : 'text-gray-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('datasets.*') ? 'text-shopee-light' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Upload Dataset</span>
                </a>

                <!-- Simulasi Ulasan Tunggal -->
                <a href="{{ route('prediction.single') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('prediction.single*') ? 'bg-shopee/20 text-shopee-light border border-shopee/30 shadow-lg shadow-shopee/10' : 'text-gray-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('prediction.single*') ? 'text-shopee-light' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Simulasi Ulasan</span>
                </a>
            </nav>
        </div>

        <!-- Admin Management Section (If Admin) -->
        @if(Auth::user()->isAdmin())
            <div>
                <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest text-purple-400/80">
                    Administrasi
                </div>
                <nav class="space-y-1.5">
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30 shadow-lg shadow-purple-500/10' : 'text-gray-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-purple-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Kelola Pengguna</span>
                    </a>
                </nav>
            </div>
        @endif

        <!-- Quick System Info Badge -->
        <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5 space-y-2">
            <div class="flex items-center justify-between text-[11px] text-gray-400">
                <span>Model Engine</span>
                <span class="text-emerald-400 font-mono font-semibold">Naive Bayes</span>
            </div>
            <div class="flex items-center justify-between text-[11px] text-gray-400">
                <span>Ekstraksi Fitur</span>
                <span class="text-shopee-light font-mono font-semibold">TF-IDF</span>
            </div>
            <div class="flex items-center justify-between text-[11px] text-gray-400">
                <span>Status Inferensi</span>
                <span class="inline-flex items-center gap-1 text-emerald-400 text-[10px] font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Ready
                </span>
            </div>
        </div>
    </div>

    <!-- User Profile & Logout Bottom Card -->
    <div class="p-4 border-t border-white/5 bg-cosmos-900/60">
        <div class="flex items-center justify-between gap-3 p-2 rounded-xl bg-white/5 border border-white/10">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr {{ Auth::user()->isAdmin() ? 'from-amber-500 to-rose-500' : 'from-blue-500 to-indigo-500' }} flex items-center justify-center font-bold text-xs text-white flex-shrink-0 shadow-md">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-white truncate leading-tight">{{ Auth::user()->name }}</p>
                    <span class="inline-block px-1.5 py-0.2 rounded text-[8px] uppercase tracking-wider font-extrabold {{ Auth::user()->isAdmin() ? 'text-amber-400 bg-amber-400/10' : 'text-indigo-400 bg-indigo-400/10' }}">
                        {{ Auth::user()->role }}
                    </span>
                </div>
            </div>

            <!-- Profile & Logout Actions -->
            <div class="flex items-center gap-1">
                <a href="{{ route('profile.edit') }}" title="Profil Akun" class="p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" title="Keluar (Logout)" class="p-1.5 rounded-lg text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

<!-- ============================================== -->
<!-- MOBILE DRAWER SIDEBAR (Slide-Over on < lg)     -->
<!-- ============================================== -->
<div x-show="sidebarOpen"
     class="lg:hidden fixed inset-0 z-50 flex"
     x-cloak>
    <!-- Dark Backdrop Overlay -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/80 backdrop-blur-sm"></div>

    <!-- Drawer Panel -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="relative flex-1 flex flex-col max-w-xs w-full bg-cosmos-900 border-r border-white/10 shadow-2xl">
        
        <!-- Mobile Drawer Header -->
        <div class="h-20 flex items-center justify-between px-6 border-b border-white/5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <x-application-logo class="w-10 h-10 flex-shrink-0" />
                <div class="flex flex-col">
                    <span class="text-sm font-black tracking-wider text-gradient-shopee uppercase">ShopeeFood</span>
                    <span class="text-xs font-bold text-gray-200">Sentimen AI</span>
                </div>
            </a>
            <!-- Close Button -->
            <button @click="sidebarOpen = false" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Mobile Drawer Nav Links -->
        <div class="flex-1 px-4 py-6 space-y-4 overflow-y-auto">
            <nav class="space-y-1.5">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('dashboard') ? 'bg-shopee/20 text-shopee-light border border-shopee/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('datasets.create') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('datasets.*') ? 'bg-shopee/20 text-shopee-light border border-shopee/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Upload Dataset</span>
                </a>
                <a href="{{ route('prediction.single') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('prediction.single*') ? 'bg-shopee/20 text-shopee-light border border-shopee/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Simulasi Ulasan</span>
                </a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Kelola Pengguna</span>
                    </a>
                @endif
            </nav>
        </div>

        <!-- Mobile Drawer Bottom User Card -->
        <div class="p-4 border-t border-white/5">
            <div class="flex items-center justify-between p-2 rounded-xl bg-white/5 border border-white/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-shopee to-shopee-light flex items-center justify-center font-bold text-xs text-white">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white leading-tight">{{ Auth::user()->name }}</p>
                        <span class="text-[9px] text-gray-400 font-semibold uppercase">{{ Auth::user()->role }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
