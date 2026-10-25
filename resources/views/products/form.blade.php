<div class="form-group" style="margin-bottom: 16px;">
    <label for="sku" style="display: block; margin-bottom: 6px; font-weight: 500;">SKU</label>
    <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku', $product->sku ?? '') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
    @error('sku')
        <span class="text-danger" style="font-size: 12px; color: #e3342f;">{{ $message }}</span>
    @enderror
</div>

<div class="form-group" style="margin-bottom: 16px;">
    <label for="category_id" style="display: block; margin-bottom: 6px; font-weight: 500;">Kategori</label>
    <select name="category_id" id="category_id" class="form-control" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
        <option value="">Pilih kategori</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <span class="text-danger" style="font-size: 12px; color: #e3342f;">{{ $message }}</span>
    @enderror
</div>

<div class="form-group" style="margin-bottom: 16px;">
    <label for="name" style="display: block; margin-bottom: 6px; font-weight: 500;">Nama Produk</label>
    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
    @error('name')
        <span class="text-danger" style="font-size: 12px; color: #e3342f;">{{ $message }}</span>
    @enderror
</div>

<div class="form-group" style="margin-bottom: 16px;">
    <label for="purchase_price" style="display: block; margin-bottom: 6px; font-weight: 500;">Harga Beli (Rp)</label>
    <input type="number" name="purchase_price" id="purchase_price" class="form-control" value="{{ old('purchase_price', $product->purchase_price ?? '') }}" min="0" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
    @error('purchase_price')
        <span class="text-danger" style="font-size: 12px; color: #e3342f;">{{ $message }}</span>
    @enderror
</div>

<div class="form-group" style="margin-bottom: 16px;">
    <label for="selling_price" style="display: block; margin-bottom: 6px; font-weight: 500;">Harga Jual (Rp)</label>
    <input type="number" name="selling_price" id="selling_price" class="form-control" value="{{ old('selling_price', $product->selling_price ?? '') }}" min="0" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
    @error('selling_price')
        <span class="text-danger" style="font-size: 12px; color: #e3342f;">{{ $message }}</span>
    @enderror
</div>

<div class="form-group" style="margin-bottom: 16px;">
    <label for="stock" style="display: block; margin-bottom: 6px; font-weight: 500;">Stok saat ini</label>
    <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
    @error('stock')
        <span class="text-danger" style="font-size: 12px; color: #e3342f;">{{ $message }}</span>
    @enderror
</div>

<div class="form-group" style="margin-bottom: 16px;">
    <label for="supplier_id" style="display: block; margin-bottom: 6px; font-weight: 500;">Pemasok (Opsional)</label>
    <select name="supplier_id" id="supplier_id" class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
        <option value="">Pilih pemasok</option>
        @foreach($suppliers as $supplier)
            <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                {{ $supplier->name }}
            </option>
        @endforeach
    </select>
    @error('supplier_id')
        <span class="text-danger" style="font-size: 12px; color: #e3342f;">{{ $message }}</span>
    @enderror
</div>