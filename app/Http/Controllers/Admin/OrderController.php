<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Order_item;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request){
        $orders=Order::latest('orders.created_at')->select('orders.*','users.name','users.email');
        $orders=$orders->leftJoin('users','users.id','orders.user_id');

        if ($request->get('keyword')!='') {
         $orders=$orders->where('users.name','like','%'.$request->keyword.'%');
         $orders=$orders->orWhere('users.email','like','%'.$request->keyword.'%');
         $orders=$orders->orWhere('orders.id','like','%'.$request->keyword.'%');
        }
        $orders=$orders->paginate(6);
        return view('admin.Orders.list',['orders'=>$orders]);

    }
    public function Detail($id){
        $order=Order::select('orders.*','countries.name as countryName')->where('orders.id',$id)->leftJoin('countries','countries.id','orders.country_id')->first();

        $orderItem=Order_item::where('order_id',$id)->get();
        $data['order']=$order;
        $data['orderItem']=$orderItem;
       return view('admin.Orders.detail',$data);
    }
    public function changeOrderStatus(Request $request,$orderId){
       $order=Order::find($orderId);
       $order->status=$request->status;
       $order->shipped_date=$request->shipped_date;
       $order->save();
       session()->flash('success','Order status updated successfully.');
       return response()->json([
           'status'=>true,
           'message'=>'Order status updated successfully.'
       ]);
    }
}
