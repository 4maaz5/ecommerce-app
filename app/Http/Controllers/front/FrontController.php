<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index(){
        $products=Product::where('is_featured','Yes')->where('status','1')->get();
        $data['products']=$products;

        $latestProducts=Product::orderBy('id','DESC')->where('status','1')->take(8)->get();
        $data['latestProducts']=$latestProducts;
        return view('front.home',$data);
    }
}
