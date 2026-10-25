@extends('layouts.app')
@section('title', 'Konfirmasi Pesanan - Sweet Glaze')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Konfirmasi pesanan</h1>
        <p class="page-subtitle">Periksa kembali pesanan Anda sebelum dikonfirmasi.</p>
    </div>
</div>

<form method="POST" action="{{ route('storefront.order') }}">
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card table-card" style="margin-bottom:18px">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Menu</th><th class="num">Harga</th><th class="num">Jumlah</th><th class="num">Subtotal</th></tr>
                </thead>
                <tbody>
                    @foreach ($lines as $i => $line)
                        <tr>
                            <td>
                                <strong>{{ $line['product']->name }}</strong>
                                <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $line['product']->id }}">
                                <input type="hidden" name="items[{{ $i }}][quantity]" value="{{ $line['quantity'] }}">
                            </td>
                            <td class="num">@rupiah($line['product']->selling_price)</td>
                            <td class="num">{{ $line['quantity'] }}</td>
                            <td class="num">@rupiah($line['subtotal'])</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="3" class="num">Total</td><td class="num">@rupiah($total)</td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card" style="margin-bottom:18px">
        <h2 class="section-title">Pembayaran</h2>
        <p class="muted" style="margin:0">
            Pembayaran dilakukan di kasir saat Anda mengambil pesanan. Total dihitung ulang oleh server
            dari harga terbaru, jadi angka akhir selalu sesuai database.
        </p>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Konfirmasi pesanan</button>
        <a href="{{ route('storefront.index') }}" class="btn">Kembali ke menu</a>
    </div>
</form>
@endsection