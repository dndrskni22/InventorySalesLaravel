<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Users
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->enum('role', ['admin', 'staff'])->default('staff');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Categories
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        // 3. Products
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained()->onDelete('restrict');
                $table->string('sku', 50)->unique();
                $table->string('name', 150);
                $table->decimal('purchase_price', 12, 2);
                $table->decimal('selling_price', 12, 2);
                $table->integer('stock')->default(0);
                $table->integer('minimum_stock')->default(5);
                $table->timestamps();
            });
        }

        // 4. Suppliers
        if (! Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone', 30);
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->timestamps();
            });
        }

        // 5. Purchases (Stock-In)
        if (! Schema::hasTable('purchases')) {
            Schema::create('purchases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
                $table->foreignId('user_id')->constrained()->onDelete('restrict');
                $table->string('purchase_number', 50)->unique();
                $table->decimal('total', 15, 2)->default(0);
                $table->enum('status', ['completed', 'cancelled'])->default('completed');
                $table->timestamp('purchased_at');
                $table->timestamps();
            });
        }

        // 6. Purchase Items
        if (! Schema::hasTable('purchase_items')) {
            Schema::create('purchase_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('restrict');
                $table->integer('quantity');
                $table->decimal('unit_cost', 12, 2);
                $table->decimal('subtotal', 15, 2);
                $table->timestamps();
            });
        }

        // 7. Sales (Stock-Out)
        if (! Schema::hasTable('sales')) {
            Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('restrict');
                $table->string('sale_number', 50)->unique();
                $table->decimal('total', 15, 2)->default(0);
                $table->enum('status', ['completed', 'cancelled'])->default('completed');
                $table->timestamp('sold_at');
                $table->timestamps();
            });
        }

        // 8. Sale Items
        if (! Schema::hasTable('sale_items')) {
            Schema::create('sale_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sale_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('restrict');
                $table->integer('quantity');
                $table->decimal('unit_price', 12, 2);
                $table->decimal('subtotal', 15, 2);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
    }
};