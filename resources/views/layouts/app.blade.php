<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Inventa Pro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; color: black !important; }
            .print-card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
        }
        .print-only { display: none; }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col md:flex-row min-h-screen">

    <!-- Mobile Top Navigation Header -->
    <header class="no-print md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between shadow-xs sticky top-0 z-50">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white text-sm">
                IP
            </div>
            <span class="font-bold text-slate-900 text-base">Inventa Pro</span>
        </div>
        <button type="button" onclick="document.getElementById('sidebar-menu').classList.toggle('hidden')" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </header>

    <!-- Sidebar Navigation (Clean Enterprise Style) -->
    <aside id="sidebar-menu" class="no-print hidden md:flex flex-col w-full md:w-64 bg-slate-900 text-slate-300 border-r border-slate-800 shrink-0 min-h-screen sticky top-0 z-40">
        <!-- Logo & Branding -->
        <div class="p-5 border-b border-slate-800 flex items-center space-x-3">
            <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white text-base">
                IP
            </div>
            <div>
                <h1 class="font-bold text-white text-base leading-tight">Inventa Pro</h1>
                <span class="text-xs text-slate-400">Inventory & Sales</span>
            </div>
        </div>

        <!-- User Information Box -->
        <div class="px-5 py-3.5 border-b border-slate-800 bg-slate-950/30">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-semibold text-slate-300 text-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <div class="flex items-center mt-0.5">
                        @if(auth()->user()->isOwner())
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-purple-900/60 text-purple-200 border border-purple-700/50">
                                OWNER
                            </span>
                        @elseif(auth()->user()->isAdmin())
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-blue-900/60 text-blue-200 border border-blue-700/50">
                                STAFF / ADMIN
                            </span>
                        @else
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                CUSTOMER
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 px-3 pb-1.5">Menu Utama</div>

            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>

            <div class="pt-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 px-3 pb-1.5">Master Data</div>

            <a href="{{ route('products.index') }}" class="flex items-center px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Produk
            </a>

            <a href="{{ route('categories.index') }}" class="flex items-center px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('categories.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                Kategori
            </a>

            <a href="{{ route('suppliers.index') }}" class="flex items-center px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('suppliers.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Pemasok (Suppliers)
            </a>

            <div class="pt-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 px-3 pb-1.5">Transaksi</div>

            <a href="{{ route('purchases.index') }}" class="flex items-center px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('purchases.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Pembelian (Stock-In)
            </a>

            <a href="{{ route('sales.index') }}" class="flex items-center px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('sales.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Penjualan (Stock-Out)
            </a>

            @if(auth()->user()->isOwner())
                <div class="pt-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 px-3 pb-1.5">Pengaturan Owner</div>

                <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Manajemen Pengguna
                </a>
            @endif

            <div class="pt-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 px-3 pb-1.5">Lainnya</div>

            <a href="{{ route('portal.catalog') }}" class="flex items-center px-3 py-2 rounded-lg text-xs font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition">
                <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Lihat Katalog Pelanggan
            </a>
        </nav>

        <!-- Logout Button -->
        <div class="p-4 border-t border-slate-800">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-3 py-2 text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800 rounded-lg transition border border-slate-700">
                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-50">
        <!-- Top App Bar -->
        <header class="no-print bg-white border-b border-slate-200 px-6 py-3.5 flex items-center justify-between sticky top-0 z-30 shadow-2xs">
            <div>
                <h2 class="text-lg font-bold text-slate-900">@yield('page_title', 'Inventa Pro')</h2>
                <p class="text-xs text-slate-500">@yield('page_subtitle', 'Sistem Manajemen Inventaris & Penjualan')</p>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Low Stock Alert Shortcut -->
                @php
                    $globalLowStockCount = \App\Models\Product::whereColumn('stock', '<=', 'minimum_stock')->count();
                @endphp
                <a href="{{ route('products.index', ['stock_status' => 'low']) }}" class="relative inline-flex items-center p-2 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-slate-100 transition" title="{{ $globalLowStockCount }} Produk Stok Menipis">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    @if($globalLowStockCount > 0)
                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white">
                            {{ $globalLowStockCount }}
                        </span>
                    @endif
                </a>

                <!-- Quick Action Buttons -->
                <div class="hidden sm:flex items-center space-x-2">
                    <a href="{{ route('sales.create') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-2xs transition">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Kasir Penjualan
                    </a>
                    <a href="{{ route('purchases.create') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 shadow-2xs transition">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Stok Masuk
                    </a>
                </div>
            </div>
        </header>

        <!-- Flash Notifications -->
        <div class="no-print px-6 pt-4">
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

            @if($errors->any())
                <div class="mb-4 p-3.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                    <p class="font-bold mb-1">Perhatian:</p>
                    <ul class="list-disc list-inside space-y-0.5 ml-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="no-print mt-auto py-3 px-6 border-t border-slate-200 bg-white text-center text-xs text-slate-400">
            Inventa Pro &copy; {{ date('Y') }} &bull; Web Programming 2
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
