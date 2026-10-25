@extends('layouts.app')
@section('title', 'Menu - Sweet Glaze')

@section('content')
@php
    $icons = ['Donat' => '🍩', 'Minuman' => '🥤', 'Paket' => '🎁'];
@endphp

<div class="store-head">
    <div>
        <h1 class="page-title">Menu Sweet Glaze</h1>
        <p class="page-subtitle">Pilih donat dan minuman favoritmu, lalu lanjutkan pesanan.</p>
    </div>
    <div class="store-search">
        <label for="menuSearch" class="sr-only">Cari menu</label>
        <input type="search" id="menuSearch" placeholder="Cari menu...">
    </div>
</div>

<div class="chips" id="chips">
    <button type="button" class="chip active" data-cat="all">Semua</button>
    @foreach ($categories as $category)
        <button type="button" class="chip" data-cat="{{ $category->id }}">{{ $category->name }}</button>
    @endforeach
</div>

<div class="menu-grid" id="menuGrid">
    @foreach ($products as $product)
        <article class="menu-card"
                 data-id="{{ $product->id }}"
                 data-cat="{{ $product->category_id }}"
                 data-name="{{ strtolower($product->name) }}"
                 data-price="{{ (float) $product->selling_price }}"
                 data-stock="{{ $product->stock }}">
            <div class="menu-art" aria-hidden="true">
                {{ $icons[$product->category->name] ?? '🍰' }}
                @if ($product->isLowStock())
                    <span class="badge badge-warning stock-tag">Tersisa {{ $product->stock }}</span>
                @endif
            </div>
            <div class="menu-body">
                <span class="menu-cat">{{ $product->category->name }}</span>
                <h3 class="menu-name">{{ $product->name }}</h3>
                <div class="menu-price">@rupiah($product->selling_price)</div>
                <div class="menu-stock">Tersedia {{ $product->stock }}</div>
                <div class="menu-action">
                    <button type="button" class="btn btn-primary" data-act="add">Tambah</button>
                    <div class="stepper" hidden>
                        <button type="button" data-act="dec" aria-label="Kurangi">&minus;</button>
                        <span class="qty">1</span>
                        <button type="button" data-act="inc" aria-label="Tambah">+</button>
                    </div>
                </div>
            </div>
        </article>
    @endforeach
    <p class="menu-empty" id="menuEmpty" hidden>Menu tidak ditemukan.</p>
</div>

<form method="POST" action="{{ route('storefront.checkout') }}" id="cartForm">
    @csrf
    <div id="cartInputs"></div>
    <div class="cart-bar" id="cartBar" hidden>
        <div class="cart-info">
            <small><span id="cartCount">0</span> item dipilih</small>
            <strong id="cartTotal">Rp 0</strong>
        </div>
        <button type="submit" class="btn btn-primary">Lanjut pesan &rarr;</button>
    </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/storefront.js') }}"></script>
@endpush