@extends('layouts.app')
@section('title', 'Dashboard - Sweet Glaze')

@section('content')
<section class="hero">
    <div class="top-bar">
        <div>
            <h1 class="page-title">Selamat datang, {{ Auth::user()->name }}</h1>
            <p class="page-subtitle">Ringkasan stok dan penjualan Sweet Glaze.</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('menu.index') }}" class="btn" style="background: #fff; color: #1e293b; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 8px; font-weight: bold; text-decoration: none;">
                🍩 Katalog Menu
            </a>
            @can('manage-sales')
                <a href="{{ route('sales.create') }}" class="btn-primary-action">Penjualan baru</a>
            @endcan
        </div>
    </div>
</section>

<div class="stat-grid">
    <div class="card stat-card">
        <div class="stat-icon berry" aria-hidden="true">📦</div>
        <div>
            <div class="stat-label">Total produk</div>
            <div class="stat-value">{{ $stats['products'] }}</div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon pistachio" aria-hidden="true">👥</div>
        <div>
            <div class="stat-label">Pelanggan terdaftar</div>
            <div class="stat-value">{{ $stats['customers'] }}</div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon sprinkle" aria-hidden="true">⚠️</div>
        <div>
            <div class="stat-label">Produk stok rendah</div>
            <div class="stat-value">{{ $stats['low_stock'] }}</div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon pistachio" aria-hidden="true">💰</div>
        <div>
            <div class="stat-label">Penjualan hari ini</div>
            <div class="stat-value sm">@rupiah($stats['sales_today'])</div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon berry" aria-hidden="true">📅</div>
        <div>
            <div class="stat-label">Penjualan bulan ini</div>
            <div class="stat-value sm">@rupiah($stats['sales_month'])</div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon pistachio" aria-hidden="true">🧾</div>
        <div>
            <div class="stat-label">Jumlah transaksi</div>
            <div class="stat-value">{{ $stats['sales_count'] + ($stats['purchases_count'] ?? 0) }}</div>
            <div class="muted" style="font-size:13px">
                {{ $stats['sales_count'] }} penjualan{{ is_null($stats['purchases_count']) ? '' : ', ' . $stats['purchases_count'] . ' pembelian' }}
            </div>
        </div>
    </div>
</div>

<div class="dash-grid">
    <div class="card">
        <h2>Penjualan 7 hari terakhir</h2>
        <div class="bars" role="img" aria-label="Grafik penjualan 7 hari terakhir">
            @foreach ($chart as $day)
                <div class="bar {{ $day['today'] ? 'today' : '' }}">
                    <div class="bar-value">{{ $day['total'] > 0 ? number_format($day['total'] / 1000, 0, ',', '.') . 'rb' : '' }}</div>
                    <div class="bar-track">
                        <div class="bar-fill" style="height: {{ round($day['total'] / $chartMax * 100) }}%"></div>
                    </div>
                    <div class="bar-label">{{ $day['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <h2>Perlu restok</h2>
        @forelse ($lowStockProducts as $product)
            <div style="display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px solid var(--line)">
                <div>
                    <a href="{{ route('products.show', $product) }}"><strong>{{ $product->name }}</strong></a>
                    <div class="muted" style="font-size:13px">{{ $product->sku }}</div>
                </div>
                <div class="num"><span class="badge badge-danger">{{ $product->stock }} / min {{ $product->minimum_stock }}</span></div>
            </div>
        @empty
            <p class="muted">Semua stok masih aman.</p>
        @endforelse
        @if ($stats['low_stock'] > 0)
            <p style="margin:12px 0 0"><a class="btn-link" href="{{ route('products.low-stock') }}">Lihat semua stok rendah</a></p>
        @endif
    </div>
</div>

<div class="card table-card">
    <div style="padding:18px 20px 6px;display:flex;justify-content:space-between;align-items:center">
        <h2 style="margin:0;font-size:17px">Penjualan terbaru</h2>
        @can('manage-sales')
            <a class="btn-link" href="{{ route('sales.index') }}">Semua penjualan</a>
        @endcan
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>No. transaksi</th><th>Waktu</th><th>Kasir</th><th>Status</th><th class="num">Total</th></tr>
            </thead>
            <tbody>
                @forelse ($recentSales as $sale)
                    <tr>
                        <td><a href="{{ route('sales.show', $sale) }}" class="mono">{{ $sale->sale_number }}</a></td>
                        <td>{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->user->name }}</td>
                        <td>@include('partials.status', ['status' => $sale->status])</td>
                        <td class="num">@rupiah($sale->total)</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada penjualan. Mulai dari menu Penjualan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection