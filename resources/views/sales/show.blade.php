@extends('layouts.app')
@section('title', $sale->sale_number . ' - Sweet Glaze')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">{{ $sale->sale_number }}</h1>
        <p class="page-subtitle">@include('partials.status', ['status' => $sale->status])</p>
    </div>
    <div class="btn-row">
        <a href="{{ route('sales.index') }}" class="btn">Kembali</a>
        @can('cancel-transaction')
            @if ($sale->isCompleted())
                <form method="POST" action="{{ route('sales.cancel', $sale) }}" onsubmit="return confirm('Batalkan penjualan ini? Stok akan dipulihkan.')">
                    @csrf
                    <button type="submit" class="btn btn-danger">Batalkan penjualan</button>
                </form>
            @endif
        @endcan
    </div>
</div>

<div class="card" style="margin-bottom:18px">
    <dl class="detail-list">
        <dt>Waktu</dt><dd>{{ $sale->sold_at->format('d/m/Y H:i') }}</dd>
        <dt>Pelanggan</dt>
        <dd>
            @if ($sale->customer)
                <a href="{{ route('customers.show', $sale->customer) }}">{{ $sale->customer->name }}</a>
            @else
                Pembeli umum
            @endif
        </dd>
        <dt>Dicatat oleh</dt><dd>{{ $sale->user->name }}</dd>
        <dt>Pembayaran</dt><dd>{{ $sale->paymentLabel() }}</dd>
        <dt>Total</dt><dd><strong>@rupiah($sale->total)</strong></dd>
    </dl>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>SKU</th><th>Produk</th><th class="num">Harga satuan</th><th class="num">Jumlah</th><th class="num">Subtotal</th></tr></thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td class="mono">{{ $item->product->sku }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td class="num">@rupiah($item->unit_price)</td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">@rupiah($item->subtotal)</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="4" class="num">Total</td><td class="num">@rupiah($sale->total)</td></tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection