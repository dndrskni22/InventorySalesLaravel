<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    /**
     * Simpan penjualan secara atomik: header, item, dan pengurangan stok
     * berada dalam satu database transaction. Harga selalu diambil dari
     * database, total dihitung di server.
     *
     * @param  array<int, array{product_id:int|string, quantity:int|string}>  $items
     */
    public function create(
        User $user,
        array $items,
        ?Carbon $soldAt = null,
        ?int $customerId = null,
        string $paymentMethod = 'cash'
    ): Sale
    {
        // Gabungkan baris produk yang sama, urutkan agar urutan lock konsisten.
        $lines = [];
        foreach ($items as $item) {
            $id = (int) $item['product_id'];
            $lines[$id] = ($lines[$id] ?? 0) + (int) $item['quantity'];
        }
        ksort($lines);

        return DB::transaction(function () use ($user, $lines, $soldAt, $customerId, $paymentMethod) {
            $sale = Sale::create([
                'user_id' => $user->id,
                'customer_id' => $customerId,
                'sale_number' => 'TMP-' . uniqid('', true),
                'total' => 0,
                'status' => Sale::STATUS_COMPLETED,
                'payment_method' => $paymentMethod,
                'sold_at' => $soldAt ?? now(),
            ]);

            // Nomor unik diturunkan dari id agar tidak bisa bentrok.
            $sale->update([
                'sale_number' => sprintf('INV-%s-%05d', $sale->sold_at->format('Ymd'), $sale->id),
            ]);

            $total = 0;

            foreach ($lines as $productId => $qty) {
                // lockForUpdate mencegah overselling pada transaksi bersamaan.
                $product = Product::lockForUpdate()->find($productId);

                if (! $product) {
                    throw ValidationException::withMessages(['items' => 'Produk tidak ditemukan.']);
                }

                if ($qty < 1 || $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup. Tersedia {$product->stock}, diminta {$qty}.",
                    ]);
                }

                $subtotal = round($product->selling_price * $qty, 2);

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->selling_price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $qty);
                $total += $subtotal;
            }

            $sale->update(['total' => $total]);

            return $sale;
        });
    }

    /**
     * Batalkan penjualan dan pulihkan stok secara konsisten.
     */
    public function cancel(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            $sale = Sale::with('items')->lockForUpdate()->findOrFail($sale->id);

            if (! $sale->isCompleted()) {
                throw ValidationException::withMessages(['status' => 'Transaksi ini sudah dibatalkan.']);
            }

            foreach ($sale->items->sortBy('product_id') as $item) {
                Product::lockForUpdate()->findOrFail($item->product_id)
                    ->increment('stock', $item->quantity);
            }

            $sale->update(['status' => Sale::STATUS_CANCELLED]);
        });
    }
}