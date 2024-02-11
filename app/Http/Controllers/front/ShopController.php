<?php

namespace App\Http\Controllers\front;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;

class ShopController extends Controller
{
    public function index($categorySlug = null, $subcategorySlug = null)
    {

        $categories = Category::orderBy('name', 'ASC')
            ->with('sub_category')
            ->where('status', '1')
            ->get();

        $brands = Brand::orderBy('name', 'ASC')
            ->where('status', '1')
            ->get();
        $products = Product::where('status', '1');


        //Apply filters here
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            $products = $products->where('category_id', $category->id);
        }
        $products = $products->orderBy('id', 'DESC');
        $products = $products->get();
        $data['categories'] = $categories;
        $data['products'] = $products;
        $data['brands'] = $brands;
        return view('front.shop', $data);
    }
}
