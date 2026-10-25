<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function isLowStock()
    {
        return $this->stock <= 5;
    }

    public function scopeLowStock($query)
    {
        return $query->where('stock', '<=', 5);
    }

    public static function lowStock()
    {
        return self::where('stock', '<=', 5)->get();
    }
}