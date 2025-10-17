<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller 
{
    /**
     * Display a paginated list of categories with optional search.
     */
    public function index(Request $request){
        $categories = Category::latest();

        // Filter by keyword if provided
        if(!empty($request->get('keyword'))){
            $categories = $categories->where('name','like','%'.$request->get('keyword').'%');
        }

        $categories = $categories->paginate(10);

        return view('admin.Category.list', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(){
        return view('admin.Category.create');
    }

    /**
     * Store a newly created category in the database along with optional image.
     */
    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if ($validator->passes()) {
            // Create category object
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->show = $request->show;

            // Handle image if uploaded
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                if ($tempImage) {
                    $extArray = explode('.', $tempImage->name);
                    $ext = last($extArray);
                    $newImageName = uniqid().'_'.$category->slug.'.'.$ext;

                    $spath = public_path().'/temp/'.$tempImage->name;
                    $dpath = public_path().'/uploads/category/'.$newImageName;

                    File::copy($spath, $dpath);

                    $category->image = $newImageName;
                }
            }

            // Save category to database
            $category->save();

            $request->session()->flash('success', 'Category Added Successfully!');
            return response()->json([
                'status' => true,
                'message' => 'Category Added Successfully!'
            ]);
        } else {
            // Return validation errors
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Show the form for editing an existing category.
     */
    public function edit($categoryId){
        $category = Category::find($categoryId);

        if (empty($category)) {
            return redirect()->route('categories.view');
        }

        return view('admin.Category.edit', compact('category'));
    }

    /**
     * Update an existing category along with optional image.
     */
    public function update($categoryId, Request $request){
        $category = Category::find($categoryId);

        if (empty($category)) {
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category not Found!'
            ]);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id',
        ]);

        if ($validator->passes()) {
            // Update category fields
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->show = $request->show;
            $category->save();

            $oldImage = $category->image;

            // Handle new image if uploaded
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id.'-'.time().'.'.$ext;

                $spath = public_path().'/temp/'.$tempImage->name;
                $dpath = public_path().'/uploads/category/'.$newImageName;
                File::copy($spath, $dpath);

                // Assign new image and save
                $category->image = $newImageName;
                $category->save();

                // Delete old image
                File::delete(public_path().'/uploads/category/'.$oldImage);
            }

            $request->session()->flash('success','Category Updated Successfully!');
            return response()->json([
                'status' => true,
                'message' => 'Category updated Successfully!'
            ]);
        } else {
            // Return validation errors
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Delete a category along with its image.
     */
    public function destroy($categoryId, Request $request){
        $category = Category::find($categoryId);

        if(empty($category)){
            $request->session()->flash('error','Category not Found');
            return response()->json([
                'status' => true,
                'message' => 'Category not Found'
            ]);
        }

        // Delete category image
        File::delete(public_path().'/uploads/category/'.$category->image);

        // Delete category record
        $category->delete();

        $request->session()->flash('success','Category Deleted Successfully');
        return response()->json([
            'status' => true,
            'message' => 'Category Deleted Successfully'
        ]);
    }
}
