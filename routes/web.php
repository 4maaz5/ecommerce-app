<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\front\CartController;
use App\Http\Controllers\front\ShopController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\front\FrontController;
use App\Http\Controllers\front\LoginController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\front\RegisterController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\front\UserProductController;
use App\Http\Controllers\admin\DiscountCouponController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\ProductSubCategoryController;

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

Route::get('/', [FrontController::class, 'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}', [ShopController::class, 'index'])->name('front.shop');
Route::get('/products/{slug}', [UserProductController::class, 'index'])->name('front.products');
Route::get('/cart', [CartController::class, 'index'])->name('front.cart');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('front.addToCart');
Route::post('/updateCart', [CartController::class, 'updateCart'])->name('front.updateCart');
Route::post('/delete-item', [CartController::class, 'deleteItem'])->name('front.deleteItem.cart');
Route::get('/checkout', [CartController::class, 'checkout'])->name('front.checkout');
Route::post('/process-checkout', [CartController::class, 'processCheckout'])->name('front.processCheckout');
Route::get('/thanks/{orderId}', [CartController::class, 'thankyou'])->name('front.thankyou');
Route::post('/get-order-summary', [CartController::class, 'getOrderSummary'])->name('front.summary');
Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name('front.discount');
Route::post('/remove-discount', [CartController::class, 'removeCoupon'])->name('front.removeCoupon');

Route::group(['prefix' => 'account'], function () {
    Route::group(['middleware' => 'guest'], function () {
        Route::get('/login', [LoginController::class, 'index'])->name('front.login');
        Route::post('/login-user', [LoginController::class, 'authenticate'])->name('front.authenticate');
        Route::get('/register', [RegisterController::class, 'index'])->name('front.register');
        Route::post('/process-register', [RegisterController::class, 'processRegister'])->name('front.processRegister');
    });

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/profile', [LoginController::class, 'profile'])->name('account.profile');
        Route::get('/my-orders', [LoginController::class, 'orders'])->name('account.orders');
        Route::get('/order-detail/{orderId}', [LoginController::class, 'orderDetail'])->name('account.orderDetail');
        Route::get('/logout', [LoginController::class, 'logout'])->name('account.logout');

    });
});

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
        Route::get('sub-categories/create', [SubCategoryController::class, 'create'])->name('sub-category.create');
        Route::post('sub-categories/', [SubCategoryController::class, 'store'])->name('sub-category.store');
        Route::get('sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories.view');
        Route::get('sub-categories/{SubCategory}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories.edit');
        Route::put('sub-categories/{SubCategory}/update', [SubCategoryController::class, 'update'])->name('sub-categories.update');
        Route::delete('sub-categories/{SubCategory}/', [SubCategoryController::class, 'destroy'])->name('sub-categories.delete');


        // Brands routes
        Route::get('brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::get('brands/view', [BrandController::class, 'index'])->name('brands');
        Route::post('brands/', [BrandController::class, 'store'])->name('brands.store');
        Route::get('brands/{brandId}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('brands/{brandId}/update', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('brands/{brandId}/', [BrandController::class, 'destroy'])->name('brands.delete');


        // Products routes
        Route::get('product/create', [ProductController::class, 'create'])->name('products.create');
        Route::get('product/list', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products/store', [ProductController::class, 'store'])->name('products.store.test');
        Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/update/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::get('/products/delete/{id}', [ProductController::class, 'destroy'])->name('products.delete');
        Route::get('/get-products', [ProductController::class, 'getProducts'])->name('products.related');
        Route::get('product/sub-categories', [ProductSubCategoryController::class, 'index'])->name('product-subcategories.index');


        //Shipping routes
        Route::get('/shipping', [ShippingController::class, 'create'])->name('shipping.create');
        Route::post('/shipping-store', [ShippingController::class, 'store'])->name('shipping.store');
        Route::get('/shipping-edit/{id}', [ShippingController::class, 'edit'])->name('shipping.edit');
        Route::put('/shipping-update/{id}', [ShippingController::class, 'update'])->name('shipping.update');
        Route::delete('/shipping-delete/{id}', [ShippingController::class, 'destroy'])->name('shipping.delete');


         //Coupon code routes
         Route::get('/coupon', [DiscountCouponController::class, 'index'])->name('coupon.index');
         Route::get('/coupon-create', [DiscountCouponController::class, 'create'])->name('coupon.create');
         Route::post('/coupon-store', [DiscountCouponController::class, 'store'])->name('coupon.store');
         Route::get('/coupon-edit/{id}', [DiscountCouponController::class, 'edit'])->name('coupon.edit');
         Route::put('/coupon-update/{id}', [DiscountCouponController::class, 'update'])->name('coupon.update');
         Route::delete('/coupon-delete/{id}', [DiscountCouponController::class, 'destroy'])->name('coupon.delete');

         //Orders Routes
         Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
         Route::get('/orders/{id}', [OrderController::class, 'detail'])->name('orders.detail');
         Route::post('order/change-status{orderId}', [OrderController::class, 'changeOrderStatus'])->name('order.changeStatus');


        Route::get('getSlug', function (Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);
        })->name('getSlug');
    });
});
