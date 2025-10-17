<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Order_item;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a paginated list of orders with user details.
     * Allows keyword search on user name, email, or order ID.
     */
    public function index(Request $request)
    {
        // Get orders with user info, ordered by creation date
        $orders = Order::latest('orders.created_at')
            ->select('orders.*', 'users.name', 'users.email')
            ->leftJoin('users', 'users.id', 'orders.user_id');

        // Apply search filter if keyword is provided
        if ($request->get('keyword') != '') {
            $orders = $orders->where('users.name', 'like', '%' . $request->keyword . '%')
                ->orWhere('users.email', 'like', '%' . $request->keyword . '%')
                ->orWhere('orders.id', 'like', '%' . $request->keyword . '%');
        }

        // Paginate results
        $orders = $orders->paginate(6);

        // Return orders list view
        return view('admin.Orders.list', ['orders' => $orders]);
    }

    /**
     * Display details of a single order, including items and country info.
     */
    public function Detail($id)
    {
        // Get order details with country name
        $order = Order::select('orders.*', 'countries.name as countryName')
            ->where('orders.id', $id)
            ->leftJoin('countries', 'countries.id', 'orders.country_id')
            ->first();

        // Get all items related to the order
        $orderItem = Order_item::where('order_id', $id)->get();

        // Prepare data to pass to view
        $data['order'] = $order;
        $data['orderItem'] = $orderItem;

        // Return order detail view
        return view('admin.Orders.detail', $data);
    }

    /**
     * Change the status of an order and optionally set the shipped date.
     */
    public function changeOrderStatus(Request $request, $orderId)
    {
        // Find the order by ID
        $order = Order::find($orderId);

        // Update status and shipped date
        $order->status = $request->status;
        $order->shipped_date = $request->shipped_date;
        $order->save();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Order Status updated successfully.');
    }

    /**
     * Send the invoice email for a specific order.
     */
    public function sendInvoiceEmail(Request $request, $orderId)
    {
        // Call helper function to send order email
        orderEmail($orderId, $request->userType);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Order email sent Successfully.');
    }
}
