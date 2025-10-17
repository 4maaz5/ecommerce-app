<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ProductSubCategoryController extends Controller
{
    /**
     * Return a list of subcategories based on a given category ID.
     * If category_id is provided in the request, fetch related subcategories.
     * Otherwise, return an empty array.
     */
    public function index(Request $request)
    {
        // Check if a category_id is provided in the request
        if (!empty($request->category_id)) {
            // Fetch subcategories related to the provided category_id
            $subCategories = SubCategory::where('category_id', $request->category_id)
                                ->orderBy('name', 'ASC')
                                ->get();

            // Return JSON response with the list of subcategories
            return response([
                'status' => true,
                'subCategories' => $subCategories
            ]);
        } 
        else {
            // If no category_id is provided, return an empty subcategories array
            return response([
                'status' => true,
                'subCategories' => []
            ]);
        }
    }
}
