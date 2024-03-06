<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\front\CartController;
use App\Http\Controllers\front\FrontController;
use App\Http\Controllers\front\ShopController;
use App\Http\Controllers\front\UserProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [FrontController::class,'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}', [ShopController::class,'index'])->name('front.shop');
Route::get('/products/{slug}', [UserProductController::class,'index'])->name('front.products');
Route::get('/cart', [CartController::class,'index'])->name('front.cart');


Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');

    });


    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('logout', [HomeController::class, 'logout'])->name('admin.logout');


        //Category Routes
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.view');
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}/', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}/', [CategoryController::class, 'destroy'])->name('categories.delete');
        Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');


        //Sub Category routes
        Route::get('sub-categories/create',[SubCategoryController::class,'create'])->name('sub-category.create');
        Route::post('sub-categories/',[SubCategoryController::class,'store'])->name('sub-category.store');
        Route::get('sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories.view');
        Route::get('sub-categories/{SubCategory}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories.edit');
        Route::put('sub-categories/{SubCategory}/update', [SubCategoryController::class, 'update'])->name('sub-categories.update');
        Route::delete('sub-categories/{SubCategory}/', [SubCategoryController::class, 'destroy'])->name('sub-categories.delete');


        // Brands routes
        Route::get('brands/create',[BrandController::class,'create'])->name('brands.create');
        Route::get('brands/view',[BrandController::class,'index'])->name('brands');
        Route::post('brands/',[BrandController::class,'store'])->name('brands.store');
        Route::get('brands/{brandId}/edit',[BrandController::class,'edit'])->name('brands.edit');
        Route::put('brands/{brandId}/update',[BrandController::class,'update'])->name('brands.update');
        Route::delete('brands/{brandId}/', [BrandController::class, 'destroy'])->name('brands.delete');


        // Products routes
        Route::get('product/create',[ProductController::class,'create'])->name('products.create');
        Route::get('product/list',[ProductController::class,'index'])->name('products.index');
        Route::post('/products/store',[ProductController::class,'store'])->name('products.store.test');
        Route::get('/products/edit/{id}',[ProductController::class,'edit'])->name('products.edit');
        Route::put('/products/update/{id}',[ProductController::class,'update'])->name('products.update');
        Route::get('/products/delete/{id}',[ProductController::class,'destroy'])->name('products.delete');

        Route::get('product/sub-categories',[ProductSubCategoryController::class,'index'])->name('product-subcategories.index');

        Route::get('getSlug',function(Request $request){
          $slug='';
          if(!empty($request->title)){
            $slug=Str::slug($request->title);
          }
          return response()->json([
            'status'=>true,
            'slug'=>$slug
            ]);
        })->name('getSlug');

    });

});
