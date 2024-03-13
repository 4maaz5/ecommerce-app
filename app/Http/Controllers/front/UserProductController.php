<?php

namespace App\Http\Controllers\front;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserProductController extends Controller
{
    public function index($slug){
        $product=Product::where('slug',$slug)->with('product_images')->first();
        if ($product==null) {
            abort(404);
        }
           //related products
    $relatedProducts=[];
    // dd($product->related_products);
    if ($product->related_products!='') {
        $productArray=explode(',',$product->related_products);
        $relatedProducts=Product::whereIn('id',$productArray)->get();

    }
        $data['product']=$product;
        $data['relatedProducts']=$relatedProducts;
        return view('front.product_page',$data);
    }
}
