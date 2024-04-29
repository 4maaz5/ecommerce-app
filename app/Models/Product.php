<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    public function product_images(){
        return $this->hasMany(Product_Image::class);
    }
    public function productRatings(){
        return $this->hasMany(Rating::class)->where('status',1);
    }
}
