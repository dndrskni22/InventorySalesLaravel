<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StorefrontController extends Controller
{
    // Kunci session untuk keranjang yang menunggu konfirmasi.
    private const CART_KEY = 'sg_checkout_items';

    public function index()
    {
        $products = Product::with('category')->where('stock', '>', 0)->get();
        $categories = Category::orderBy('name')->get();

        return view('storefront.index', compact('products', 'categories'));
    }

    /**
     * Halaman konfirmasi pesanan.
     * GET  -> menampilkan ringkasan keranjang (harga dievaluasi ulang dari database).
     * POST -> menerima keranjang dari halaman menu, simpan di session, lalu tampilkan versi GET.
     */
    public function checkout(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'items'              => ['required', 'array', 'min:1'],
                'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
                'items.*.quantity'   => ['required', 'integer', 'min:1'],
            ], [
                'items.required'         => 'Keranjang masih kosong. Pilih menu terlebih dahulu.',
                'items.*.product_id.*'   => 'Menu yang dipilih tidak valid.',
                'items.*.quantity.min'   => 'Jumlah minimal 1.',
            ]);

            session([self::CART_KEY => $validated['items']]);

            return redirect()->route('storefront.checkout');
        }

        $items = session(self::CART_KEY);

        if (! $items) {
            return redirect()->route('storefront.index')
                ->with('error', 'Keranjang masih kosong. Pilih menu terlebih dahulu.');
        }

        // Harga & stok selalu diambil ulang dari database, bukan dari JavaScript.
        $products = Product::with('category')
            ->whereIn('id', collect($items)->pluck('product_id'))
            ->get()
            ->keyBy('id');

        $lines = [];
        $total = 0;

        foreach ($items as $item) {
            $product = $products[$item['product_id']] ?? null;

            if (! $product || $product->stock < 1) {
                continue;
            }

            // Jangan pernah melebihi stok yang tersedia saat ini.
            $qty = min((int) $item['quantity'], $product->stock);

            if ($qty < 1) {
                continue;
            }

            $subtotal = $product->selling_price * $qty;
            $total += $subtotal;

            $lines[] = [
                'product'  => $product,
                'quantity' => $qty,
                'subtotal' => $subtotal,
            ];
        }

        if (! $lines) {
            session()->forget(self::CART_KEY);

            return redirect()->route('storefront.index')
                ->with('error', 'Stok menu pilihanmu sudah habis. Silakan pilih menu lain.');
        }

        return view('storefront.checkout', compact('lines', 'total'));
    }

    /**
     * Simpan pesanan pelanggan menjadi transaksi penjualan (status completed,
     * dibayar tunai di kasir). Menggunakan SaleService agar nomor transaksi,
     * perhitungan total, dan pengurangan stok konsisten dengan modul kasir.
     */
    public function storeOrder(Request $request, SaleService $service)
    {
        $validated = $request->validate([
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ], [
            'items.required'       => 'Keranjang masih kosong.',
            'items.*.quantity.min' => 'Jumlah minimal 1.',
        ]);

        $items = session(self::CART_KEY) ?? $validated['items'];

        try {
            $sale = $service->create(
                $request->user(),
                $items,
                null,     // sold_at = sekarang
                null,     // pelanggan walk-in (belum dipilih)
                'cash'    // pembayaran tunai di kasir
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        session()->forget(self::CART_KEY);

        return redirect()->route('storefront.receipt', $sale)
            ->with('success', 'Pesanan berhasil dikonfirmasi!');
    }

    /**
     * Struk / bukti pesanan setelah pembayaran.
     */
    public function receipt(Sale $sale)
    {
        $sale->loadMissing(['items.product', 'customer']);

        return view('storefront.receipt', compact('sale'));
    }
}
