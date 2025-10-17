<?php

namespace App\Http\Controllers\admin;

use App\Models\Brand;
use App\Models\Rating;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\Product_Image;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a paginated list of all products.
     * Supports search by product title.
     */
    public function index(Request $request)
    {
        $products = Product::latest('id')->with('product_images');

        if($request->get('keyword') != ""){
            $products = $products->where('title','like','%'.$request->keyword.'%');
        }

        $products = $products->paginate(8);
        $data['products'] = $products;

        return view('admin.products.list', $data);
    }

    /**
     * Show the form for creating a new product.
     * Loads all categories and brands for selection.
     */
    public function create()
    {
        $data = [];
        $categories = Category::orderBy('name','ASC')->get();
        $brands = Brand::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;

        return view('admin.products.create', $data);
    }

    /**
     * Store a newly created product in storage.
     * Handles product details, images, and related products.
     */
    public function store(Request $request)
    {
        // Validate required fields
        $request->validate([
            'title'=>'required',
            'slug'=>'required|unique:products',
            'price'=>'required|numeric',
            'sku'=>'required|unique:products',
            'track_qty'=>'required|in:Yes,No',
            'category'=>'required|numeric',
            'is_featured'=>'required|in:Yes,No',
        ]);

        // Create new product
        $product = new Product();
        $product->title = $request->title;
        $product->slug = $request->slug;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->sku = $request->sku;
        $product->barcode = $request->barcode;
        $product->track_qty = $request->track_qty;
        $product->qty = $request->qty;
        $product->status = $request->status;
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category;
        $product->brand_id = $request->brand;
        $product->is_featured = $request->is_featured;
        $product->short_description = $request->short_description;
        $product->shiping_returns = $request->shiping_returns;
        $product->related_products = (!empty($request->related_products)) ? implode(',', $request->related_products) : '';
        $product->save();

        // Handle product images
        $image = array();
        if ($files = $request->file('image')) {
            foreach ($files as $file) {
                $image_name = md5(rand(1000,10000));
                $ext = strToLower($file->getClientOriginalExtension());
                $image_full_name = $image_name.'.'.$ext;
                $upload_path = 'public/temp/thumb/';
                $image_url = $upload_path.$image_full_name;
                $file->move($upload_path,$image_full_name);
                $image[] = $image_url;
            }
        }

        // Save images to Product_Image model
        $product_images = new Product_Image();
        $product_images->product_id = $product->id;
        $product_images->image = implode('|', $image);
        $product_images->save();

        // Flash success message
        $request->session()->flash('success','Product Added Successfully');
        return redirect()->route('products.index');
    }

    /**
     * Show the form for editing an existing product.
     * Loads categories, subcategories, brands, and related products.
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $categories = Category::where('id',$product->category_id)->get();
        $subCategories = SubCategory::all();
        $brands = Brand::all();

        // Load related products if any
        $relatedProducts = [];
        if ($product->related_products != '') {
            $productArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productArray)->get();
        }

        $data = [];
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['product'] = $product;
        $data['subCategories'] = $subCategories;
        $data['relatedProducts'] = $relatedProducts;

        return view('admin.products.edit', $data);
    }

    /**
     * Update an existing product in storage.
     * Handles product details, images, and related products.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'title'=>'required',
            'slug'=>'required|unique:products',
            'price'=>'required|numeric',
            'sku'=>'required',
            'track_qty'=>'required|in:Yes,No',
            'category'=>'required|numeric',
            'is_featured'=>'required|in:Yes,No',
        ];

        // Require quantity if track_qty is Yes
        if (!empty($request->track_qty) && $request->track_qty=="Yes") {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        // Update product details
        $product = Product::find($id);
        $product->title = $request->title;
        $product->slug = $request->slug;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->sku = $request->sku;
        $product->barcode = $request->barcode;
        $product->track_qty = $request->track_qty;
        $product->qty = $request->qty;
        $product->status = $request->status;
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category;
        $product->brand_id = $request->brand;
        $product->is_featured = $request->is_featured;
        $product->short_description = $request->short_description;
        $product->shiping_returns = $request->shiping_returns;
        $product->related_products = (!empty($request->related_products)) ? implode(',', $request->related_products) : '';
        $product->save();

        // Update product images
        $image = array();
        if ($files = $request->file('image')) {
            foreach ($files as $file) {
                $image_name = md5(rand(1000,10000));
                $ext = strToLower($file->getClientOriginalExtension());
                $image_full_name = $image_name.'.'.$ext;
                $upload_path = 'public/temp/thumb/';
                $image_url = $upload_path.$image_full_name;
                $file->move($upload_path,$image_full_name);
                $image[] = $image_url;
            }
        }

        $id = Product_image::where('product_id', $id)->first();
        $idd = $id->id;
        $product_images = Product_Image::find($idd);
        $product_images->product_id = $product->id;
        $product_images->image = implode('|', $image);
        $product_images->save();

        $request->session()->flash('success','Product Updated Successfully');
        return redirect()->route('products.index');
    }

    /**
     * Delete a product along with its images.
     */
    public function destroy($id)
    {
        $productDelete = Product::find($id);
        $productImages = Product_Image::where('product_id', $id);

        $productDelete->delete();
        $productImages->delete();

        return redirect()->back()->with('danger','Product deleted Successfully!');
    }

    /**
     * Fetch products for autocomplete search.
     */
    public function getProducts(Request $request)
    {
        $tempProduct = [];
        if ($request->term != "") {
            $products = Product::where('title','like','%'.$request->term)->get();
            if ($products != null) {
                foreach ($products as $product) {
                    $tempProduct[] = ['id' => $product->id, 'text' => $product->title];
                }
            }
        }
        return response()->json([
            'tags' => $tempProduct,
            'status' => true
        ]);
    }

    /**
     * Display product ratings with pagination.
     * Supports search by product title or user name.
     */
    public function productRatings(Request $request)
    {
        $ratings = Rating::select('ratings.*','products.title as productTitle')
                    ->orderBy('ratings.created_at','DESC')
                    ->leftJoin('products','products.id','ratings.product_id');

        if (!empty($request->get('keyword'))) {
            $ratings = $ratings->orWhere('products.title','like','%'.$request->get('keyword').'%');
            $ratings = $ratings->orWhere('ratings.user_name','like','%'.$request->get('keyword').'%');
        }

        $ratings = $ratings->paginate(10);
        $data['ratings'] = $ratings;

        return view('admin.products.ratings', $data);
    }

    /**
     * Change the status of a product rating (active/inactive).
     */
    public function changeRatingStatus(Request $request)
    {
        $productRating = Rating::find($request->id);
        $productRating->status = $request->status;
        $productRating->save();

        session()->flash('success','Status Changed Successfully!');
        return response()->json([
            'status' => true,
        ]);
    }
}
