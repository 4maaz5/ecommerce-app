<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index(){
        $categories=Category::all();
        $subCategories=SubCategory::all();
        $data['categories']=$categories;
        $data['subCategories']=$subCategories;
        return view('front.home',$data);
    }
}
