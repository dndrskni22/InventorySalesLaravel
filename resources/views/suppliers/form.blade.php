<div class="form-group" style="margin-bottom: 16px;">
    <label for="name" style="display: block; margin-bottom: 6px; font-weight: 500;">Nama Pemasok</label>
    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $supplier->name ?? '') }}" placeholder="Masukkan nama pemasok" required style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
    @error('name')
        <span class="text-danger" style="font-size: 12px; color: #e3342f;">{{ $message }}</span>
    @enderror
</div>

<div class="form-group" style="margin-bottom: 16px;">
    <label for="address" style="display: block; margin-bottom: 6px; font-weight: 500;">Alamat</label>
    <textarea name="address" id="address" class="form-control" rows="3" placeholder="Masukkan alamat lengkap pemasok" style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">{{ old('address', $supplier->address ?? '') }}</textarea>
    @error('address')
        <span class="text-danger" style="font-size: 12px; color: #e3342f;">{{ $message }}</span>
    @enderror
</div>

<div class="form-group" style="margin-bottom: 16px;">
    <label for="phone" style="display: block; margin-bottom: 6px; font-weight: 500;">No. Telepon</label>
    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $supplier->phone ?? '') }}" placeholder="Contoh: 081234567890" style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
    @error('phone')
        <span class="text-danger" style="font-size: 12px; color: #e3342f;">{{ $message }}</span>
    @enderror
</div>