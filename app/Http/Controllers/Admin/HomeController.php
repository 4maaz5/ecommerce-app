<?php

namespace App\Http\Controllers\admin;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(){
        $totalOrders=Order::where('status','!=','cancelled')->count();
        $totalProducts=Product::count();
        $totalUsers=User::where('role',1)->count();
        $totalSale=Order::where('status','!=','cancelled')->sum('grand_total');
        //this month reveniew
        $startOfMonth=Carbon::now()->startOfMonth()->format('y-m-d');
        $currentDate=Carbon::now()->format('y-m-d');
        $thisMonth=Order::where('status','!=','cancelled')->whereDate('created_at','>=',$startOfMonth)
        ->whereDate('created_at','<=',$currentDate)->sum('grand_total');

        //last month revenue
        $lastMonthStartDate=Carbon::now()->subMonth()->startOfMonth()->format('y-m-d');
        $lastMonthEndDate=Carbon::now()->subMonth()->endOfMonth()->format('y-m-d');
        $lastMonthName=Carbon::now()->subMonth()->startOfMonth()->format('M');
        $lastMonth=Order::where('status','!=','cancelled')->whereDate('created_at','>=',$lastMonthStartDate)
        ->whereDate('created_at','<=',$lastMonthEndDate)->sum('grand_total');

        //last 30 days sale
        $date=Carbon::now()->subDays(30)->format('y-m-d');
        $lastThirtyDays=Order::where('status','!=','cancelled')->whereDate('created_at','>=',$date)
        ->whereDate('created_at','<=',$currentDate)->sum('grand_total');

        return view('admin.dashboard',compact('totalOrders','totalProducts','totalUsers','totalSale','thisMonth','lastMonth','lastThirtyDays','lastMonthName'));
    }
    public function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
