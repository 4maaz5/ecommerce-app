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
    /**
     * Display the admin dashboard with various metrics.
     */
    public function index()
    {
        // Total orders excluding cancelled ones
        $totalOrders = Order::where('status', '!=', 'cancelled')->count();

        // Total number of products
        $totalProducts = Product::count();

        // Total number of users with role = 1 (normal users)
        $totalUsers = User::where('role', 1)->count();

        // Total sales excluding cancelled orders
        $totalSale = Order::where('status', '!=', 'cancelled')->sum('grand_total');

        // Revenue for the current month
        $startOfMonth = Carbon::now()->startOfMonth()->format('y-m-d');
        $currentDate = Carbon::now()->format('y-m-d');
        $thisMonth = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $startOfMonth)
            ->whereDate('created_at', '<=', $currentDate)
            ->sum('grand_total');

        // Revenue for the previous month
        $lastMonthStartDate = Carbon::now()->subMonth()->startOfMonth()->format('y-m-d');
        $lastMonthEndDate = Carbon::now()->subMonth()->endOfMonth()->format('y-m-d');
        $lastMonthName = Carbon::now()->subMonth()->startOfMonth()->format('M');
        $lastMonth = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $lastMonthStartDate)
            ->whereDate('created_at', '<=', $lastMonthEndDate)
            ->sum('grand_total');

        // Revenue for the last 30 days
        $date = Carbon::now()->subDays(30)->format('y-m-d');
        $lastThirtyDays = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $date)
            ->whereDate('created_at', '<=', $currentDate)
            ->sum('grand_total');

        // Return dashboard view with all metrics
        return view('admin.dashboard', compact(
            'totalOrders',
            'totalProducts',
            'totalUsers',
            'totalSale',
            'thisMonth',
            'lastMonth',
            'lastThirtyDays',
            'lastMonthName'
        ));
    }

    /**
     * Logout the authenticated admin and redirect to login page.
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
