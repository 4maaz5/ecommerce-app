<?php

namespace App\Http\Controllers\front;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rating;

class UserProductController extends Controller
{
    // Display a single product page with ratings and related products
    public function index($slug){
        // Fetch the product with ratings count, ratings sum, and images
        $product = Product::where('slug', $slug)
            ->withCount('productRatings')      // Count of ratings
            ->withSum('productRatings', 'rating') // Sum of ratings
            ->with('product_images', 'productRatings') // Load images and ratings
            ->first();

        // If product not found, show 404 page
        if ($product == null) {
            abort(404);
        }

        // Fetch related products if available
        $relatedProducts = [];
        if ($product->related_products != '') {
            $productArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productArray)
                ->where('status', 1)
                ->get();
        }

        // Calculate average rating and percentage for stars display
        $avgRating = '0.00';
        $avgRatingPer = 0;
        if ($product->product_ratings_count > 0) {
            $avgRating = number_format(($product->product_ratings_sum_rating / $product->product_ratings_count), 2);
            $avgRatingPer = ($avgRating * 100) / 5; // Convert to percentage
        }

        // Pass data to the product page view
        $data['product'] = $product;
        $data['relatedProducts'] = $relatedProducts;
        $data['avgRating'] = $avgRating;
        $data['avgRatingPer'] = $avgRatingPer;

        return view('front.product_page', $data);
    }
}
