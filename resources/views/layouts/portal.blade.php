<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Katalog Produk') - Inventa Pro Store</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-100 flex flex-col min-h-screen">

    <!-- Top Navigation Bar (Clean White Storefront Style) -->
    <header class="no-print bg-white border-b border-slate-200 sticky top-0 z-40 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('portal.catalog') }}" class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white text-sm">
                            IP
                        </div>
                        <div>
                            <span class="font-bold text-base text-slate-900 block leading-tight">Inventa Pro</span>
                            <span class="text-[11px] text-slate-500 block">Katalog Pelanggan</span>
                        </div>
                    </a>
                </div>

                <!-- Nav Links -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <a href="{{ route('portal.catalog') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('portal.catalog') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Katalog Produk
                    </a>
                    <a href="{{ route('portal.orders') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('portal.orders*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Pesanan Saya
                    </a>

                    @if(auth()->user()->hasRole(['owner', 'admin']))
                        <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200">
                            &larr; Panel Admin
                        </a>
                    @endif

                    <div class="border-l border-slate-200 pl-3 flex items-center space-x-2">
                        <div class="text-right hidden md:block">
                            <span class="text-xs font-semibold text-slate-900 block">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-slate-400 block">{{ auth()->user()->email }}</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-1.5 text-xs text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Keluar">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="no-print max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
        @if(session('success'))
            <div class="mb-4 flex items-center p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">
                <svg class="w-4 h-4 text-emerald-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 flex items-center p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <svg class="w-4 h-4 text-rose-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="no-print mt-auto py-4 border-t border-slate-200 bg-white text-center text-xs text-slate-400">
        Inventa Pro &copy; {{ date('Y') }} &bull; Portal Belanja & Pemesanan Pelanggan
    </footer>

    @stack('scripts')
</body>
</html>
