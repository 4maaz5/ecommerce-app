<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    /**
     * Display a list of sub-categories with optional keyword search.
     */
    public function index(Request $request)
    {
        // Join sub_categories with categories to get category name
        $subCategories = SubCategory::select('sub_categories.*', 'categories.name as categoryName')
            ->latest('sub_categories.id')
            ->leftJoin('categories', 'categories.id', 'sub_categories.category_id');

        // Filter by keyword if provided
        if (! empty($request->get('keyword'))) {
            $subCategories = $subCategories->where('sub_categories.name', 'like', '%'.$request->get('keyword').'%');
            $subCategories = $subCategories->orwhere('categories.name', 'like', '%'.$request->get('keyword').'%');
        }

        // Paginate results
        $subCategories = $subCategories->paginate(10);

        // Return view with data
        return view('admin.sub_Category.list', compact('subCategories'));
    }

    /**
     * Show the form to create a new sub-category.
     */
    public function create()
    {
        // Get all categories for dropdown
        $categories = Category::orderBy('name', 'ASC')->get();
        $warehouses = Warehouse::all();

        return view('admin.sub_category.create', compact('categories', 'warehouses'));
    }

    /**
     * Store a new sub-category in the database.
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            // Create and save new sub-category
            $subCategory = new SubCategory;
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->show = $request->show;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            $request->session()->flash('success', 'Sub Category created Successfully.');

            return response([
                'status' => true,
                'message' => 'Sub Category created Successfully.',
            ]);
        } else {
            // Return validation errors
            return response([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    /**
     * Show the form to edit an existing sub-category.
     */
    public function edit($subCategoryId)
    {
        $subCategory = SubCategory::find($subCategoryId);
        if ($subCategory) {
            $categories = Category::all();

            return view('admin.sub_category.edit', compact('subCategory', 'categories'));
        } else {
            return redirect()->back();
        }
    }

    /**
     * Update an existing sub-category in the database.
     */
    public function update($id, Request $request)
    {
        $subCategory = SubCategory::find($id);

        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$subCategory->id.'id',
            'category' => 'required',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            // Update sub-category fields
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->show = $request->show;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            $request->session()->flash('success', 'Sub Category updated Successfully.');

            return response([
                'status' => true,
                'message' => 'Sub Category updated Successfully.',
            ]);
        } else {
            // Return validation errors
            return response([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    /**
     * Delete a sub-category from the database.
     */
    public function destroy($categoryId, Request $request)
    {
        $category = SubCategory::find($categoryId);

        if (empty($category)) {
            $request->session()->flash('error', 'Category not Found');

            return response()->json([
                'status' => true,
                'message' => 'Category not Found',
            ]);
        }

        // Delete sub-category
        $category->delete();
        $request->session()->flash('success', 'Sub Category Deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Sub php artisan make:migration Category Deleted Successfully',
        ]);
    }
}
