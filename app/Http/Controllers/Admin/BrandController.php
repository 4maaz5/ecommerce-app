<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a paginated list of brands with optional search by keyword.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request){
        $brands = Brand::orderBy('id');

        // Filter brands by keyword if provided
        if($request->get('keyword')){
            $brands = $brands->where('name','like','%'.$request->get('keyword').'%');
        }

        $brands = $brands->paginate(5);

        return view('admin.brands.list', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     *
     * @return \Illuminate\View\View
     */
    public function create(){
        return view('admin.brands.create');
    }

    /**
     * Store a newly created brand in the database.
     */
    public function store(Request $request){
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required'
        ]);

        if($validator->passes()){
            // Create and save new brand
            $brands = new Brand();
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;
            $brands->save();

            return response([
                'status' => true,
                'message' => 'Brand added Successfully'
            ]);
        }
        else{
            // Return validation errors
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Show the form for editing the specified brand.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id){
        $brand = Brand::find($id);
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand in the database.
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request){
        $brands = Brand::find($id);

        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required'
        ]);

        if($validator->passes()){
            // Update brand details
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;
            $brands->save();

            return response([
                'status' => true,
                'message' => 'Brand updated Successfully'
            ]);
        }
        else{
            // Return validation errors
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Remove the specified brand from the database.
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request){
        $brand = Brand::find($id);

        if(empty($brand)){
           // Brand not found
           $request->session()->flash('error','Brand not Found');
           return response()->json([
               'status' => true,
               'message' => 'Brand not Found'
           ]);
        }

        // Delete brand
        $brand->delete();
        $request->session()->flash('success','Brand Deleted Successfully');

        return response()->json([
           'status' => true,
           'message' => 'Brand Deleted Successfully'
        ]);
   }
}
