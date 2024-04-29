<?php

namespace App\Http\Controllers\front;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rating;

class UserProductController extends Controller
{
    public function index($slug){
        $product=Product::where('slug',$slug)
        ->withCount('productRatings')
        ->withSum('productRatings','rating')
        ->with('product_images','productRatings')->first();

        if ($product==null) {
            abort(404);
        }
           //related products
    $relatedProducts=[];
    if ($product->related_products!='') {
        $productArray=explode(',',$product->related_products);
        $relatedProducts=Product::whereIn('id',$productArray)->where('status',1)->get();
    }

        //rating calculation   "product_ratings_count" => 1
    // "product_ratings_sum_rating
        $avgRating='0.00';
        $avgRatingPer=0;
        if ($product->product_ratings_count>0) {
            $avgRating=number_format(($product->product_ratings_sum_rating/$product->product_ratings_count),2);
            $avgRatingPer=($avgRating*100)/5;
        }
        $data['product']=$product;
        $data['relatedProducts']=$relatedProducts;
        $data['avgRating']=$avgRating;
        $data['avgRatingPer']=$avgRatingPer;
        return view('front.product_page',$data);
    }
}
