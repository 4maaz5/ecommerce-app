<?php

namespace App\Http\Controllers\front;

use App\Models\User;
use App\Models\Pages;
use App\Models\Product;
use App\Models\Category;
use App\Models\WishList;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ContactEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
    public function index(){
        $products=Product::where('is_featured','Yes')->where('status','1')->get();
        $data['products']=$products;

        $latestProducts=Product::orderBy('id','DESC')->where('status','1')->take(8)->get();
        $data['latestProducts']=$latestProducts;
        return view('front.home',$data);
    }
    public function addToWishList(Request $request){

         if (Auth::check()==false) {
            session(['url.intended'=>url()->previous()]);
            return response()->json([
'status'=>false
            ]);
         }

         $product=Product::where('id',$request->id)->first();
         if ($product==null) {
            return response()->jsno([
            'status'=>true,
            'message'=>'<div class="alert alert-danger">Product not found.</div>'
            ]);
         }
         $wishList= WishList::updateOrCreate(
            [
                'user_id'=>Auth::user()->id,
                'product_id'=>$request->id
            ],
            [
                'user_id'=>Auth::user()->id,
                'product_id'=>$request->id
            ]
         );

         return response()->json([
            'status'=>true,
            'message'=>'<div class="alert alert-success"><strong> "'.$product->title.'"</strong> added in your WishList</div>'
                        ]);
    }
    public function page($slug){

         $page=Pages::where('slug',$slug)->first();
         if ($page==null) {
            abort(404);
        }
         return view('front.page',compact('page'));
    }
    public function sendContactEmail(Request $request){
       $validator=Validator::make($request->all(),[
        'name'=>'required',
        'email'=>'required|email',
        'subject'=>'required|min:10',
       ]);
       if ($validator->passes()) {
       //send email here
       $mailData=[
        'name'=>$request->name,
        'email'=>$request->email,
        'subject'=>$request->subject,
        'message'=>$request->message,
        'mail_subject'=>'You have received a contact email'
       ];
       $admin=User::where('id',1)->first();
       Mail::to($admin->email)->send(new ContactEmail($mailData));
       session()->flash('success','Thanks for contacting us, we will get back to you soon!');
       return response()->json([
        'status'=>true
       ]);
       }else{
        return response()->json([
        'status'=>false,
        'errors'=>$validator->errors()
        ]);
       }
    }
}
