<?php

namespace App\Http\Controllers\front;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserProductController extends Controller
{
    public function index($slug){
        $product=Product::where('slug',$slug)->with('product_images')->first();
        $data['product']=$product;
        return view('front.product_page',$data);
    }
}
