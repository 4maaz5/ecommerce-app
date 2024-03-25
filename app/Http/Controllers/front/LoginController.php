<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Order_item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index(){
        return view('front.login');
    }
    public function authenticate(Request $request){
        $validator=Validator::make($request->all(),[
        'email'=>'required|email',
        'password'=>'required'
        ]);
        if ($validator->passes()) {
           if (Auth::attempt(['email'=>$request->email,'password'=>$request->password],$request->get('remembered'))) {
            if (session()->has('url.intended')) {
                // return redirect(session()->get('url.intended'));
                return redirect()->route('account.profile');
            }
            return redirect()->route('account.profile');
           }else{
            session()->flash('error','Either email or password is incorrect');
            return redirect()->route('front.login')
            ->withInput($request->only('email'));
           }
        }else{
            return redirect()->route('front.login')->withErrors($validator)->withInput($request->only('email'));
        }
    }
    public function profile(){
return view('front.account.profile');
    }
    public function logout(){
        Auth::logout();
        return redirect()->route('front.login')->with('success','You have been Successfully Loged Out');
    }
    function orders(){
        $user=Auth::user();
        $orders=Order::where('user_id',$user->id)->orderBy('created_at','DESC')->get();

     return view('front.account.orders',['orders'=>$orders]);
    }
    function orderDetail($orderId){
        $data=[];
        $user=Auth::user();
        $order=Order::where('user_id',$user->id)->where('id',$orderId)->first();

        $orderItems=Order_item::where('order_id',$orderId)->get();
        $orderItemsCount=Order_item::where('order_id',$orderId)->count();

        $data['order']=$order;
        $data['orderItems']=$orderItems;
        $data['orderItemsCount']=$orderItemsCount;
    return view('front.account.orderDetail',$data);
    }
}
