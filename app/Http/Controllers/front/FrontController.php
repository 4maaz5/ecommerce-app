<?php

namespace App\Http\Controllers\front;

use App\Models\User;
use App\Models\Pages;
use App\Models\Product;
use App\Models\WishList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ContactEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
    /**
     * Display the home page with featured and latest products
     */
    public function index()
    {
        $products = Product::where('is_featured', 'Yes')
            ->where('status', '1')
            ->get();

        $latestProducts = Product::where('status', '1')
            ->orderBy('id', 'DESC')
            ->take(8)
            ->get();

        return view('front.home', [
            'products' => $products,
            'latestProducts' => $latestProducts
        ]);
    }

    /**
     * Add a product to the user's wishlist
     */
    public function addToWishList(Request $request)
    {
        if (!Auth::check()) {
            session(['url.intended' => url()->previous()]);
            return response()->json([
                'status' => false,
                'message' => 'You must be logged in to add items to your wishlist.'
            ]);
        }

        $product = Product::find($request->id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => '<div class="alert alert-danger">Product not found.</div>'
            ]);
        }

        // Add or update wishlist
        WishList::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $request->id
            ],
            [
                'user_id' => Auth::id(),
                'product_id' => $request->id
            ]
        );

        return response()->json([
            'status' => true,
            'message' => '<div class="alert alert-success"><strong>"' . $product->title . '"</strong> added to your wishlist.</div>'
        ]);
    }

    /**
     * Display a CMS page by slug
     */
    public function page($slug)
    {
        $page = Pages::where('slug', $slug)->first();

        if (!$page) {
            abort(404);
        }

        return view('front.page', compact('page'));
    }

    /**
     * Handle contact form submission
     */
    public function sendContactEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required|min:10',
            'message' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $mailData = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'mail_subject' => 'You have received a contact email'
        ];

        $admin = User::find(1); // make sure admin exists

        if ($admin) {
            Mail::to($admin->email)->send(new ContactEmail($mailData));
        }

        session()->flash('success', 'Thanks for contacting us, we will get back to you soon!');

        return response()->json([
            'status' => true
        ]);
    }
}
