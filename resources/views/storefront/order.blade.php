<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pesanan - Sweet Glaze</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
        body { background: #f8f9fa; color: #333; padding: 30px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 20px; font-size: 24px; color: #2c1e23; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-weight: 500; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #2c1e23; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%; }
        button:hover { background: #4a333b; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Konfirmasi Pesanan</h1>

        @if ($errors->any())
            <div class="alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('storefront.order') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="customer_name">Nama Pemesan / Pelanggan</label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required placeholder="Masukkan nama pelanggan">
            </div>

            <!-- Bagian Item Pesanan Sederhana untuk Contoh -->
            <div class="form-group">
                <label for="product_id">Pilih Produk</label>
                <select name="items[0][product_id]" id="product_id" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach(\App\Models\Product::where('stock', '>', 0)->get() as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (Stok: {{ $product->stock }} - Rp {{ number_format($product->selling_price, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="quantity">Jumlah</label>
                <input type="number" name="items[0][quantity]" id="quantity" value="1" min="1" required>
            </div>

            <button type="submit">Konfirmasi & Simpan Pesanan</button>
        </form>
    </div>
</body>
</html>