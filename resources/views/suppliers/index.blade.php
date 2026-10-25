@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Pemasok</h1>
        <a href="{{ route('suppliers.create') }}" class="btn btn-danger">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pemasok
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form action="{{ route('suppliers.index') }}" method="GET" class="form-inline">
                <div class="input-group w-100">
                    <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Cari nama atau email...">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $index => $supplier)
                            <tr>
                                {{-- Menghitung nomor urut jika menggunakan pagination --}}
                                <td>{{ $suppliers->firstItem() + $index }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-warning">Ubah</a>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pemasok ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data pemasok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Navigasi Pagination --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $suppliers->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection