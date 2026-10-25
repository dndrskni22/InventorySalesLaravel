<?php
$user = auth()->user();
$isAdmin = $user && ($user->role === 'admin' || (method_exists($user, 'hasRole') && $user->hasRole('admin')));

$menu = [
    'Utama' => [
        ...($isAdmin ? [['dashboard', ['dashboard'], 'Dashboard', null, '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1/>']] : []),
        ['menu.index', ['menu.*', 'storefront.*'], 'Katalog Menu', null, '<path d="M3 3h18v18H3z"/><path d="M21 9H3M9 21V9"/>'],
    ],
    'Master data' => [
        ['products.index', ['products.index', 'products.create', 'products.edit', 'products.show'], 'Produk', 'manage-master', '<path d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path d="M3 8l9 5 9-5M12 13v8"/>'],
        ['products.low-stock', ['products.low-stock'], 'Stok rendah', 'manage-master', '<path d="M12 3l10 18H2L12 3z"/><path d="M12 10v5m0 4h.01"/>'],
        ['categories.index', ['categories.*'], 'Kategori', 'manage-master', '<path d="M20 12l-8 8-9-9V3h8l9 9z"/><circle cx="7.5" cy="7.5" r="1.5"/>'],
        ['suppliers.index', ['suppliers.*'], 'Pemasok', 'manage-master', '<path d="M1 6h13v10H1zM14 10h5l3 3v3h-8z"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>'],
    ],
    'Transaksi' => [
        ['sales.index', ['sales.*'], 'Penjualan', 'manage-sales', '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.5 12h11L21 7H6"/>'],
    ],
    'Administrasi' => [
        ['users.index', ['users.*'], 'Pengguna', 'manage-users', '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>'],
    ],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sweet Glaze')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sweetglaze.css') }}">
    @stack('styles')
</head>
<body>

@auth
    <div class="app-shell">
        <aside class="sidebar" id="sidebar" aria-label="Menu utama">
            <a href="{{ $isAdmin ? route('dashboard') : route('menu.index') }}" class="brand">
                <span class="brand-mark" aria-hidden="true">🍩</span>
                <span class="brand-name">Sweet Glaze<small>Inventory &amp; Sales</small></span>
            </a>

            <nav class="nav">
                @foreach ($menu as $groupName => $items)
                    @php 
                        $visible = collect($items)->filter(function ($i) use ($isAdmin, $user, $groupName) {
                            if ($groupName !== 'Utama' && !$isAdmin) {
                                return false;
                            }
                            return !$i[3] || $user->can($i[3]);
                        }); 
                    @endphp

                    @if ($visible->isNotEmpty())
                        <div class="nav-label">{{ $groupName }}</div>
                        @foreach ($visible as $item)
                            <a href="{{ route($item[0]) }}" class="{{ request()->routeIs(...$item[1]) ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item[4] !!}</svg>
                                {{ $item[2] }}
                            </a>
                        @endforeach
                    @endif
                @endforeach
            </nav>

            <div class="sidebar-foot">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ method_exists(Auth::user(), 'roleLabel') ? Auth::user()->roleLabel() : ucfirst(Auth::user()->role ?? 'Pelanggan') }}</div>
                <a href="{{ route('logout') }}" class="logout-btn" style="display:block; text-align:center; text-decoration:none;">Keluar</a>
            </div>
        </aside>

        <div class="scrim" id="scrim"></div>

        <div class="main">
            <header class="mobile-bar">
                <button type="button" class="menu-toggle" id="menuToggle" aria-label="Buka menu">☰</button>
                <strong>🍩 Sweet Glaze</strong>
            </header>

            <main class="content">
                @if (session('success'))
                    <div class="alert alert-success" role="status">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
@else
    <main class="guest-wrap">
        @yield('content')
    </main>
@endauth

<script>
    (function () {
        var toggle = document.getElementById('menuToggle');
        var scrim = document.getElementById('scrim');
        if (!toggle) return;
        toggle.addEventListener('click', function () { document.body.classList.toggle('nav-open'); });
        scrim.addEventListener('click', function () { document.body.classList.remove('nav-open'); });
    })();
</script>
@stack('scripts')
</body>
</html>