@extends('layouts.app')

@section('title', 'Tambah Pemasok - Sweet Glaze')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1>Tambah Pemasok Baru</h1>

    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        @include('suppliers.form')

        <div style="margin-top: 20px;">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="{{ route('suppliers.index') }}" class="btn-secondary" style="margin-left: 10px;">Batal</a>
        </div>
    </form>
</div>
@endsection