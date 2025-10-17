<?php

namespace App\Http\Controllers\front;

use App\Models\Order;
use App\Models\Country;
use App\Models\Product;
use App\Models\Order_item;
use App\Models\DiscountCoupon;
use App\Models\shipping_charge;
use App\Models\Customer_Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class CartController extends Controller
{
    /**
     * Display the cart page with current cart content.
     */
    public function index()
    {
        $cartContent = Cart::content();
        $data['cartContent'] = $cartContent;
        return view('front.cart', $data);
    }

    /**
     * Add a product to the cart.
     */
    public function addToCart(Request $request)
    {
        $product = Product::with('product_images')->find($request->id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found!'
            ]);
        }

        $cartContent = Cart::content();
        $productAlreadyExist = $cartContent->contains(function ($item) use ($product) {
            return $item->id == $product->id;
        });

        if (!$productAlreadyExist) {
            Cart::add(
                $product->id,
                $product->title,
                1,
                $product->price,
                ['productImage' => (!empty($product->product_images)) ? $product->product_images->first()->first() : '']
            );
            $status = true;
            $message = '<strong>' . $product->title . '</strong> Added to your cart successfully.';
            session()->flash('success', $message);
        } else {
            $status = false;
            $message = $product->title . ' Already added in cart';
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * Update quantity of an item in the cart.
     */
    public function updateCart(Request $request)
    {
        $rowId = $request->rowId;
        $qty = $request->qty;
        $itemInfo = Cart::get($rowId);

        if (!$itemInfo) {
            return response()->json([
                'status' => false,
                'message' => 'Item not found in Cart.'
            ]);
        }

        $product = Product::find($itemInfo->id);

        if ($product->track_qty == 'Yes' && $qty > $product->qty) {
            $message = 'Requested quantity (' . $qty . ') not available in stock';
            session()->flash('error', $message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        Cart::update($rowId, $qty);
        $message = 'Cart updated successfully!';
        session()->flash('success', $message);

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    /**
     * Remove an item from the cart.
     */
    public function deleteItem(Request $request)
    {
        $itemInfo = Cart::get($request->rowId);

        if (!$itemInfo) {
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

    /**
     * Show checkout page with shipping, discount, and grand total calculation.
     */
    public function checkout()
    {
        if (Cart::count() == 0) {
            return view('front.cart'); // cart is empty
        }

        if (!Auth::check()) {
            session(['url.intended' => url()->current()]);
            return redirect()->route('front.login'); // user must login
        }

        $user = Auth::user();
        $customerAddress = Customer_Address::where('user_id', $user->id)->first();
        $countries = Country::orderBy('name', 'ASC')->get();

        $subTotal = Cart::subtotal(2, '.', '');
        $discount = 0;

        // Apply discount if exists in session
        if (session()->has('code')) {
            $code = session()->get('code');
            $discount = $code->type == 'percent' ? $code->discount_amount / 100 * $subTotal : $code->discount_amount;
        }

        $totalQty = 0;
        $totalShippingCharge = 0;
        $grandTotal = $subTotal - $discount;

        if ($customerAddress != null) {
            $userCountry = $customerAddress->country_id;
            $shippingInfo = shipping_charge::where('country_id', $userCountry)->first();

            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            $totalShippingCharge = $shippingInfo ? $totalQty * $shippingInfo->charge : 0;
            $grandTotal += $totalShippingCharge;
        }

        return view('front.checkout', [
            'countries' => $countries,
            'customerAddress' => $customerAddress,
            'totalShippingCharge' => $totalShippingCharge,
            'grandTotal' => $grandTotal,
            'discount' => $discount
        ]);
    }

    /**
     * Process checkout and store order + order items.
     */
    public function processCheckout(Request $request)
    {
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
                'message' => 'Please fix the errors',
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        // Save/update customer address
        $user = Auth::user();
        Customer_Address::updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['first_name','last_name','email','mobile','country','address','apartment','city','state','zip'])
        );

        if ($request->payment_method == 'cod') {
            return $this->storeCODOrder($request, $user);
        }
        // Other payment methods can be handled here
    }

    /**
     * Display thank you page after order completion.
     */
    public function thankyou($id)
    {
        return view('front.thankyou', ['id' => $id]);
    }

    /**
     * Apply discount coupon and return updated order summary.
     */
    public function applyDiscount(Request $request)
    {
        $code = DiscountCoupon::where('code', $request->code)->first();

        if (!$code) {
            return response()->json(['status' => false, 'message' => 'Invalid discount coupon.']);
        }

        $now = Carbon::now();
        if (($code->starts_at && $now->lt(Carbon::parse($code->starts_at))) ||
            ($code->expires_at && $now->gt(Carbon::parse($code->expires_at)))) {
            return response()->json(['status' => false, 'message' => 'Invalid discount coupon.']);
        }

        session()->put('code', $code);
        return $this->getOrderSummary($request);
    }

    /**
     * Remove discount coupon from session and update order summary.
     */
    public function removeCoupon(Request $request)
    {
        session()->forget('code');
        return $this->getOrderSummary($request);
    }
}
