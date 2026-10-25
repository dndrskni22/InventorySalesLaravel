# 📐 ERD — Sweet Glaze

Entity Relationship Diagram database toko donat & minuman **Sweet Glaze**
(SQLite: `database/database.sqlite`), sesuai file migration di
`database/migrations/`.

```mermaid
erDiagram
    USERS ||--o{ PURCHASES : "mencatat pembelian"
    USERS ||--o{ SALES : "mencatat penjualan"
    CATEGORIES ||--o{ PRODUCTS : "mengelompokkan"
    SUPPLIERS ||--o{ PURCHASES : "memasok"
    PURCHASES ||--o{ PURCHASE_ITEMS : "memuat rincian"
    PRODUCTS ||--o{ PURCHASE_ITEMS : "dibeli"
    CUSTOMERS |o--o{ SALES : "terhubung (opsional)"
    SALES ||--o{ SALE_ITEMS : "memuat rincian"
    PRODUCTS ||--o{ SALE_ITEMS : "terjual"

    USERS {
        int id PK
        string name
        string email UK
        string password
        string role "admin | staff (varchar)"
        timestamp email_verified_at
        timestamp remember_token
        timestamp created_at
        timestamp updated_at
    }
    CATEGORIES {
        int id PK
        string name UK
        timestamp created_at
        timestamp updated_at
    }
    PRODUCTS {
        int id PK
        string sku UK
        int category_id FK
        string name
        decimal purchase_price
        decimal selling_price
        int stock
        int minimum_stock
        timestamp created_at
        timestamp updated_at
    }
    SUPPLIERS {
        int id PK
        string name
        string address
        string phone
        timestamp created_at
        timestamp updated_at
    }
    PURCHASES {
        int id PK
        int supplier_id FK
        int user_id FK
        string purchase_number UK
        decimal total
        string status
        date purchased_at
        timestamp created_at
        timestamp updated_at
    }
    PURCHASE_ITEMS {
        int id PK
        int purchase_id FK
        int product_id FK
        int quantity
        decimal unit_cost
        decimal subtotal
    }
    CUSTOMERS {
        int id PK
        string name
        string phone
        string email
        string address
        timestamp created_at
        timestamp updated_at
    }
    SALES {
        int id PK
        int user_id FK "kasir"
        int customer_id FK "boleh kosong"
        string sale_number UK "prefix INV"
        decimal total
        string status
        string payment_method "default cash (cash/qris/transfer)"
        date sold_at
        timestamp created_at
        timestamp updated_at
    }
    SALE_ITEMS {
        int id PK
        int sale_id FK
        int product_id FK
        int quantity
        decimal unit_price
        decimal subtotal
    }
```

## Penjelasan Relasi

| Relasi | Kardinalitas | Keterangan |
|---|---|---|
| `categories` → `products` | 1 : N (nullable) | Setiap donat/minuman opsional punya satu kategori menu (`products.category_id`). |
| `users` → `sales` | 1 : N | Admin/staff yang melayani transaksi kasir (`sales.user_id`). |
| `customers` → `sales` | 0..1 : N | Pelanggan opsional; checkout halaman katalog boleh tanpa akun pelanggan (`sales.customer_id` nullable). |
| `sales` → `sale_items` | 1 : N | Satu struk pembelian berisi banyak baris menu (`sale_items.sale_id`). |
| `products` → `sale_items` | 1 : N | Item struk mengunci harga jual saat transaksi (`unit_price`). |
| `users` → `purchases` | 1 : N | Admin/staff mencatat pembelian stok (`purchases.user_id`). |
| `suppliers` → `purchases` | 1 : N | Bahan/menu dibeli dari supplier tertentu (`purchases.supplier_id`). |
| `purchases` → `purchase_items` | 1 : N | Rincian barang pembelian (`purchase_items.purchase_id`). |
| `products` → `purchase_items` | 1 : N | Item pembelian merujuk produk (`purchase_items.product_id`, `unit_cost`). |

## Catatan Skema

- **Tabel `sales` punya kolom `payment_method`** (migration `2026_10_25_000001_add_payment_method_to_sales_table.php`, default `cash`) — menandai transaksi tunai pelanggan. Ini pembeda dari skema versi lain.
- Role `users.role` bertipe varchar dengan nilai **admin / staff**; pembeli tidak memiliki akun pengguna (data disimpan di tabel `customers`).
- **Alur stok**: stok berkurang lewat `App\Services\SaleService` saat order tersimpan (validasi anti-oversell), dan bertambah lewat `App\Services\PurchaseService` saat pembelian selesai.
- Nomor struk unik `sales.sale_number` dengan prefix `INV` (`INV-YYYYMMDD-XXXXX`), dibuat otomatis oleh service.
- Tabel `purchases` & `purchase_items` tersedia untuk modul pembelian stok; data harga memakai `decimal(12,2)` dalam Rupiah.
- Kolom harga `unit_price` pada `sale_items` disalin dari `selling_price` produk agar riwayat struk tidak berubah ketika harga menu diperbarui.
