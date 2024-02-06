<?php

use App\Models\Category;

function getCategories(){
    return Category::with('sub_category')->orderBy('name','ASC')->where('show','1')->where('status','1')->get();
}
?>
