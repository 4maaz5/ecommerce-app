<?php

namespace App\Http\Controllers\front;

use App\Models\Order;
use App\Models\Country;
use App\Models\Product;
use App\Models\Order_item;
use Illuminate\Http\Request;
use App\Models\DiscountCoupon;
use Illuminate\Support\Carbon;
use App\Models\shipping_charge;
use App\Models\Customer_Address;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index()
    {
        $cartContent = Cart::content();
        $data['cartContent'] = $cartContent;
        return view('front.cart', $data);
    }
    public function addToCart(Request $request)
    {
        $product = Product::with('product_images')->find($request->id);
        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found!'
            ]);
        }
        if (Cart::count() > 0) {
            // echo 'product already in cart';
            $cartContent = Cart::content();
            $productAlreadyExist = false;
            foreach ($cartContent as $item) {
                if ($item->id == $product->id) {
                    $productAlreadyExist = true;
                }
            }
            if ($productAlreadyExist == false) {
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first()->first() : '']);
                $status = true;
                $message = '<strong>' . $product->title . '</strong>' . ' ' . ' Added in your cart Successfully.';
                session()->flash('success', $message);
            } else {
                $status = false;
                $message = $product->title .  ' Already added in cart';
            }
        } else {
            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first()->first() : '']);
            $status = true;
            $message = '<strong>' . $product->title . '</strong>' . ' ' . ' Added in your cart Successfully.';
            session()->flash('success', $message);
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
    public function updateCart(Request $request)
    {
        $rowId = $request->rowId;
        $qty = $request->qty;
        Cart::update($rowId, $qty);
        //check qty available in stock
        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);
        if ($product->track_qty == 'Yes') {
            if ($qty <= $product->qty) {
                Cart::update($rowId, $qty);
                $message = 'Cart updated Successfully!';
                $status = true;
                session()->flash('success', $message);
            } else {
                $message = 'Requested quantity(' . $qty . ') not available in stock';
                $status = false;
                session()->flash('error', $message);
            }
        } else {
            Cart::update($rowId, $qty);
            $message = 'Cart updated Successfully!';
            $status = true;
            session()->flash('success', $message);
        }

        $message = 'Cart updated Successfully!';
        session()->flash('success', $message);
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
    public function deleteItem(Request $request)
    {
        $itemInfo = Cart::get($request->rowId);
        if ($itemInfo == null) {
            $errorMessage = 'Item not found in Cart.';
            session()->flash('error', $errorMessage);
            return response()->json([
                'status' => false,
                'message' => $errorMessage
            ]);
        }
        Cart::remove($request->rowId);
        $message = 'Item removed from Cart successfully.';
        session()->flash('success', $message);
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
    public function checkout()
    {
        $discount = 0;
        //Incase cart is empty
        if (Cart::count() == 0) {
            return view('front.cart');
        }
        //Incase user is not loged in
        if (Auth::check() == false) {
            if (!session()->has('url.intended')) {
                session(['url.intended' => url()->current()]);
            }
            return redirect()->route('front.login');
        }
        $customerAddress = Customer_Address::where('user_id', Auth::user()->id)->first();
        session()->forget('url.intended');
        $countries = Country::orderBy('name', 'ASC')->get();

        $subTotal = Cart::subtotal(2, '.', '');
        //Apply discount
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
                $discount = $code->discount_amount / 100 * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }
        }

        $totalQty = 0;
        $totalShippingCharge = 0;
        $grandTotal = 0;
        if ($customerAddress != null) {
            $userCountry = $customerAddress->country_id;

            $shippingInfo = shipping_charge::where('country_id', $userCountry)->first();


            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }
            $qty = $shippingInfo->charge;

            $totalShippingCharge = $totalQty * $qty;


            $grandTotal = ($subTotal - $discount) + $totalShippingCharge;
        } else {
            $grandTotal = $subTotal - $discount;
            $totalShippingCharge = 0;
        }


        return view('front.checkout', ['countries' => $countries, 'customerAddress' => $customerAddress, 'totalShippingCharge' => $totalShippingCharge, 'grandTotal' => $grandTotal, 'discount' => $discount]);
    }
    public function processCheckout(Request $request)
    {
        //validation....
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:3',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Please Fix the Errors',
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        //save customer data
        $user = Auth::user();
        Customer_Address::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                // ''=>$request->,
            ]
        );
        //storing data in orders table
        if ($request->payment_method == 'cod') {
            $discountCodeId = NULL;
            $promoCode = '';
            $totalQty = 0;
            $shipping = 0;
            $discount = 0;
            $subTotal = Cart::subtotal(2, '.', '');

            //Apply discount
            if (session()->has('code')) {
                $code = session()->get('code');
                if ($code->type == 'percent') {
                    $discount = $code->discount_amount / 100 * $subTotal;
                } else {
                    $discount = $code->discount_amount;
                }
                $discountCodeId = $code->id;
                $promoCode = $code->code;
            }

            //calculate shipping
            $shippingInfo = Shipping_Charge::where('country_id', $request->country)->first();

            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if ($shippingInfo != null) {
                $shipping = $totalQty * $shippingInfo->charge;
                $grandTotal = ($subTotal - $discount) + $shipping;
            } else {
                $shippingInfo = Shipping_Charge::where('country_id', 'rest_of_world')->first();
                $shipping = $totalQty * $shippingInfo->charge;
                $grandTotal = ($subTotal - $discount) + $shipping;
            }

            $subTotal = Cart::subtotal(2, '.', '');
            $grandTotal = $subTotal + $shipping;



            $order = new Order();
            $order->sub_total = $subTotal;
            $order->shipping = $shipping;
            $order->grand_Total = $grandTotal;
            $order->discount = $discount;
            $order->coupon_code_id = $discountCodeId;
            $order->coupon_code = $promoCode;
            $order->payment_status = 'not paid';
            $order->status = 'pending';
            $order->user_id = $user->id;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->address = $request->address;
            $order->apartment = $request->apartment;
            $order->state = $request->state;
            $order->city = $request->city;
            $order->zip = $request->zip;
            $order->notes = $request->order_notes;
            $order->country_id = $request->country;
            $order->save();

            //storing order items in order_items table
            foreach (Cart::content() as $item) {
                $orderItem = new Order_item();
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price * $item->qty;
                $orderItem->save();
                //update product stock
                $productData=Product::find($item->id);
                if ($productData->track_qty=='Yes') {
                    $currentQty=$productData->qty;
                    $updateQty=$currentQty-$item->qty;
                    $productData->qty=$updateQty;
                    $productData->save();
                }

            }

            //send email
            // orderEmail($order->id, 'customer');


            session()->flash('success', 'You have Successfully Placed your Order.');
            Cart::destroy();
            session()->forget('code');

            return response()->json([
                'message' => 'Order Saved Successfully.',
                'orderId' => $order->id,
                'status' => true
            ]);
        } else {
        }
    }
    public function thankyou($id)
    {
        return view('front.thankyou', ['id' => $id]);
    }
    public function getOrderSummary(Request $request)
    {
        $grandTotal = 0;
        $subTotal = Cart::subtotal(2, '.', '');
        $discount = 0;
        $discountString = '';


        //Apply discount
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
                $discount = $code->discount_amount / 100 * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }
            $discountString = ' <div class="apply-coupan mt-4" id="discount-row">
            <strong>' . session()->get('code')->code . '</strong>
           <a class="btn btn-sm btn-danger" id="removeCoupon"><i class="fa fa-times"></i></a>
       </div>';
        }



        if ($request->country_id > 0) {

            $shippingInfo = Shipping_Charge::where('country_id', $request->country_id)->first();
            $totalQty = 0;
            $totalShippingCharge = 0;

            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if ($shippingInfo != null) {
                $shippingCharge = $totalQty * $shippingInfo->charge;
                $grandTotal = ($subTotal - $discount) + $shippingCharge;

                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal, 2),
                    'discount' => number_format($discount, 2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge, 2),
                ]);
            } else {
                $shippingInfo = Shipping_Charge::where('country_id', 'rest_of_world')->first();
                $shippingCharge = $totalQty * $shippingInfo->charge;
                $grandTotal = ($subTotal - $discount) + $shippingCharge;
                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format(($subTotal - $discount), 2),
                    'discount' => number_format($discount, 2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge, 2),
                ]);
            }
        } else {
            return response()->json([
                'status' => true,
                'grandTotal' => number_format(($subTotal - $discount), 2),
                'discount' => number_format($discount, 2),
                'discountString' => $discountString,
                'shippingCharge' => number_format(0, 2),
            ]);
        }
    }
    public function applyDiscount(Request $request)
    {
        $code = DiscountCoupon::where('code', $request->code)->first();

        if ($code == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid discount Coupon.'
            ]);
        }

        //check if coupon start date is valid or not
        $now = Carbon::now();

        if ($code->starts_at != '') {
            $startDate = Carbon::parse($code->starts_at);
            if ($now->lt($startDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount Coupon.'
                ]);
            }
        }


        if ($code->expires_at != '') {
            $endDate = Carbon::parse($code->expires_at);
            if ($now->gt($endDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount Coupon.'
                ]);
            }
        }

        //max uses check
        if ($code->max_uses > 0) {
            $couponUsed = Order::where('coupon_code_id', $code->id)->count();
            if ($couponUsed >= $code->max_uses) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount Coupon.'
                ]);
            }
        }


        //max users Check
        if ($code->max_uses_user > 0) {
            $couponUsedByUser = Order::where(['coupon_code_id' => $code->id, 'user_id' => Auth::user()->id])->count();
            if ($couponUsedByUser >= $code->max_uses_user) {
                return response()->json([
                    'status' => false,
                    'message' => 'You Already used this Coupon.'
                ]);
            }
        }

        $subTotal = Cart::subtotal(2, '.', '');
        //Min amount condition checked
        if ($code->min_amount > 0) {
            if ($subTotal < $code->min_amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your minimum amount must be $' . $code->min_amount . '.'
                ]);
            }
        }


        session()->put('code', $code);
        return $this->getOrderSummary($request);
    }
    public function removeCoupon(Request $request)
    {
        session()->forget('code');
        return $this->getOrderSummary($request);
    }
}
