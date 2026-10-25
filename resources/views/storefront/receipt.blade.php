@extends('layouts.app')
@section('title', 'Pembayaran Berhasil - Sweet Glaze')

@section('content')
<div class="card receipt-ok" style="margin-bottom:18px">
    <div class="check-circle" aria-hidden="true">&#10003;</div>
    <h1 class="page-title">Pembayaran berhasil</h1>
    <p class="page-subtitle">
        Nomor pesanan <strong class="mono">{{ $sale->sale_number }}</strong>
        &middot; {{ $sale->paymentLabel() }}
        @if ($sale->customer) &middot; {{ $sale->customer->name }} @endif
    </p>
</div>

<div class="card table-card" style="margin-bottom:18px">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Menu</th><th class="num">Harga</th><th class="num">Jumlah</th><th class="num">Subtotal</th></tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="num">@rupiah($item->unit_price)</td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">@rupiah($item->subtotal)</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="3" class="num">Total dibayar</td><td class="num">@rupiah($sale->total)</td></tr>
            </tfoot>
        </table>
    </div>
</div>

<a href="{{ route('storefront.index') }}" class="btn btn-primary">Pesan lagi</a>
@endsection

@push('scripts')
<script>
    // Pesanan sudah tersimpan, kosongkan keranjang di browser.
    try { localStorage.removeItem('sg_cart'); } catch (e) {}
</script>
@endpush