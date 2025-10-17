<?php

use App\Models\Brand;
use App\Models\Order;
use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\Country;
use App\Models\Pages;
use App\Models\Product_Image;
use Illuminate\Support\Facades\Mail;

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
function orderEmail($orderId,$userType='customer'){
    $order=Order::where('id',$orderId)->with('items')->first();

    if ($userType=='customer') {
        $subject='Thanks for your Order.';
        $email=$order->email;
    }
    else{
        $subject='you have received an Order.';
        $email=env("ADMIN_EMAIL");
    }

    $mailData=[
        'subject'=>$subject,
        'order'=>$order,
        'userType'=>$userType
    ];


    Mail::to($email)->send(new OrderEmail($mailData));
    // dd($order);
}
function getCountry($id){
    return Country::where('id',$id)->first();
}
function staticPages(){
    $pages=Pages::orderBy('name','ASC')->get();
    return $pages;
}
?>
