@extends('layouts.app')

@section('title', 'Edit Produk - Sweet Glaze')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1>Edit Produk</h1>

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('products.form')

        <div style="margin-top: 20px;">
            <button type="submit" class="btn-primary">Perbarui</button>
            <a href="{{ route('products.index') }}" class="btn-secondary" style="margin-left: 10px;">Batal</a>
        </div>
    </form>
</div>
@endsection