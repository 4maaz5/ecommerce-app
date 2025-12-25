<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'price', 'compare_price',
        'category_id', 'sub_category_id', 'warehouse_id', 'room_id',
        'brand_id', 'is_featured', 'sku', 'barcode', 'track_qty', 'qty', 'status',
    ];

    public function product_images()
    {
        return $this->hasMany(Product_Image::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }
}
