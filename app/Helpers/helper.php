<?php

use App\Models\Brand;
use App\Models\Category;

function getCategories()
{
    return Category::orderBy('name', 'ASC')
        ->with('sub_category')->where('show', '1')->where('status', '1')
        ->get();
}
function getBrands()
{
    return Brand::orderBy('name', 'DESC')
        ->where('status', '1')
        ->get();
}
