@extends('front.layouts.app')
@section('content')
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="#">Shop</a></li>
                    <li class="breadcrumb-item">{{ $product->title }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-7 pt-3 mb-3">
        <div class="container">
            <div class="row ">
                @if(session()->has('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session()->has('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
                <div class="col-md-5">
                        @php
                        $image=DB::table('product__images')->where('product_id',$product->id)->first();
                        if (!empty($image)) {
                            $images=explode('|',$image->image);
                        $count=0;
                       foreach ($images as $image) {
                        $count++;
                        if ($count==1) {
                            @endphp
                        <img src="{{ URL::to($image) }}" alt="Image" style="width: 450px; margin-left:50px;height:350px;">
                        @php
                        }
                        else {
                            @endphp
                        <img src="{{ URL::to($image) }}" alt="Image" style="width: 160px;margin-left:10px;height:130px;margin-top:10px;">
                        @php
                        }
                        @endphp
                        @php

                       }
                    }
                   @endphp


                </div>
                <div class="col-md-7">
                    <div class="bg-light right">
                        <h1>{{ $product->title }}</h1>
                        <div class="d-flex mb-3">
                            {{-- <div class="text-primary mr-2">
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star-half-alt"></small>
                                <small class="far fa-star"></small>
                            </div> --}}
                            <div class="star-rating mt-2" title="">
                                <div class="back-stars">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>

                                    <div class="front-stars" style="width: {{ $avgRatingPer }}%">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                            <small class="pt-2 ps-2"> ({{ ($product->product_ratings_count>1)?$product->product_ratings_count. 'Reviews':$product->product_ratings_count. 'Review' }} )</small>
                        </div>
                        <h2 class="price text-secondary"><del>${{ $product->compare_price }}</del></h2>
                        <h2 class="price ">${{ $product->price }}</h2>

                        <p>{!! $product->short_description !!}</p>
                        @if ($product->track_qty == 'Yes')
                        @if ($product->qty > 0)
                            <a class="btn btn-dark" href="javascript:void(0)"
                                onclick="addToCart({{ $product->id }})">
                                <i class="fa fa-shopping-cart"></i>&nbsp Add To Cart
                            </a>
                        @else
                            <a class="btn btn-dark" href="javascript:void(0)">
                                <i class="fa fa-shopping-cart"></i>&nbsp Out of Stock
                            </a>
                        @endif
                    @else
                        <a class="btn btn-dark" href="javascript:void(0)"
                            onclick="addToCart({{ $product->id }})">
                            <i class="fa fa-shopping-cart"></i>&nbsp Add To Cart
                        </a>
                    @endif                    </div>
                </div>

                <div class="col-md-12 mt-5">
                    <div class="bg-light">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Description</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping" aria-selected="false">Shipping & Returns</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                                <p>
{!! $product->description !!}                                </p>
                            </div>
                            <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                                {!! $product->shiping_returns !!}
                            </div>

                            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                <div class="col-md-8">
                                    <div class="row">
                                        <form action="" method="post" name="ratingForm" id="ratingForm">
                                        <h3 class="h4 pb-3">Write a Review</h3>
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                                       <p></p>
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" name="email" id="email" placeholder="Email">
                                        <p></p>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="rating">Rating</label>
                                            <br>
                                            <div class="rating" style="width: 10rem">
                                                <input id="rating-5" type="radio" name="rating" value="5"/><label for="rating-5"><i class="fas fa-3x fa-star"></i></label>
                                                <input id="rating-4" type="radio" name="rating" value="4"  /><label for="rating-4"><i class="fas fa-3x fa-star"></i></label>
                                                <input id="rating-3" type="radio" name="rating" value="3"/><label for="rating-3"><i class="fas fa-3x fa-star"></i></label>
                                                <input id="rating-2" type="radio" name="rating" value="2"/><label for="rating-2"><i class="fas fa-3x fa-star"></i></label>
                                                <input id="rating-1" type="radio" name="rating" value="1"/><label for="rating-1"><i class="fas fa-3x fa-star"></i></label>

                                        </div>
                                    <p class="product-rating-error text-danger"></p>
                                    </div>
                                        <div class="form-group mb-3">
                                            <label for="">How was your overall experience?</label>
                                            <textarea name="comment"  id="comment" class="form-control" cols="30" rows="10" placeholder="How was your overall experience?"></textarea>
                                      <p></p>
                                        </div>
                                        <div>
                                            <button class="btn btn-dark">Submit</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-5">
                                    <div class="overall-rating mb-3">
                                        <div class="d-flex">
                                            <h1 class="h3 pe-3">{{ $avgRating }}</h1>
                                            <div class="star-rating mt-2" title="">
                                                <div class="back-stars">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>

                                                    <div class="front-stars" style="width: {{ $avgRatingPer }}%">
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                        <i class="fa fa-star" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="pt-2 ps-2">({{ ($product->product_ratings_count>1)?$product->product_ratings_count. 'Reviews':$product->product_ratings_count. 'Review' }} )</div>
                                        </div>

                                    </div>
                                    @if($product->productRatings->isNotEmpty())
                                    @foreach($product->productRatings as $rating)
                                    @php
                                        $ratingPer=($rating->rating*100)/5;
                                    @endphp
                                    <div class="rating-group mb-4">
                                       <span> <strong>{{ $rating?->user_name }}</strong></span>
                                        <div class="star-rating mt-2" title="">
                                            <div class="back-stars">
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>

                                                <div class="front-stars" style="width: {{ $ratingPer }}%">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="my-3">
                                            <p>{{ $rating?->comment }}

                                        </p>
                                        </div>
                                    </div>
                                    @endforeach

                                </div>

                              @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if(!empty($relatedProducts))
    <section class="pt-5 section-8">
        <div class="container">
            <div class="section-title">
                <h2>Related Products</h2>
            </div>
            <div class="col-md-12">
                <div id="related-products" class="carousel">

                        @foreach($relatedProducts as $relproduct)
                    <div class="card product-card">
                        <div class="product-image position-relative">
                            @php
                            $image=DB::table('product__images')->where('product_id',$relproduct->id)->first();
                            if (!empty($image)) {
                                $images=explode('|',$image->image);

                           foreach ($images as $image) {

                           }
                        }
                        @endphp
                        <a href="{{ route('front.products',$relproduct->slug) }}" class="product-img">
                        <img class="card-img-top" src="{{ URL::to($image) }}" alt="Loading" style="height: 270px">
                        </a>
                        <a onclick="addToWishList({{ $product->id }})" class="whishlist" href="javascript:void(0);"><i class="far fa-heart"></i></a>

                            <div class="product-action">
                                @if ($relproduct->track_qty == 'Yes')
                                @if ($relproduct->qty > 0)
                                    <a class="btn btn-dark" href="javascript:void(0)"
                                        onclick="addToCart({{ $relproduct->id }})">
                                        <i class="fa fa-shopping-cart"></i>&nbsp Add To Cart
                                    </a>
                                @else
                                    <a class="btn btn-dark" href="javascript:void(0)">
                                        <i class="fa fa-shopping-cart"></i> &nbsp Out of Stock
                                    </a>
                                @endif
                            @else
                                <a class="btn btn-dark" href="javascript:void(0)"
                                    onclick="addToCart({{ $relproduct->id }})">
                                    <i class="fa fa-shopping-cart"></i>&nbsp Add To Cart
                                </a>
                            @endif
                            </div>
                        </div>
                        <div class="card-body text-center mt-3">
                            <a class="h6 link" href="">{{ $relproduct->title }}</a>
                            <div class="price mt-2">
                                <span class="h5"><strong>${{ $relproduct->price }}</strong></span>
                                @if($relproduct->compare_price>0)
                                <span class="h6 text-underline"><del>${{ $relproduct->compare_price }}</del></span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif
</main>
@endsection
@section('customJs')
<script type="text/javascript">
function addToCart(id) {
    $.ajax({
        url: '{{ route("front.addToCart") }}',
        type: 'POST',
        data: {
            id: id,
            _token: '{{ csrf_token() }}' // Include CSRF token
        },
        dataType: 'json',
        success: function(response) {
            if (response.status==true) {
                window.location.href="{{ route('front.cart') }}";
            }else{
                alert(response.message);
            }
        }
    });
}
$("#ratingForm").submit(function(e){
    e.preventDefault();
    $.ajax({
       url:'{{ route("front.saveRating",$product->id) }}',
       type:'post',
       data:$(this).serializeArray(),
       dataType:'json',
       success:function(response){
        var errors=response.error;
        if (response.status==false) {
         if (errors.name) {
            $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.name);
         }else{
            $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
         }
         if (errors.email) {
            $("#email").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.email);
         }else{
            $("#email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
         }
         if (errors.comment) {
            $("#comment").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.comment);
         }else{
            $("#comment").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
         }
         if (errors.rating) {
          $(".product-rating-error").html(errors.rating);
         }else{
            $(".product-rating-error").html('');
         }
        }else{
          window.location.href="{{ route('front.products',$product->slug) }}";
        }

       }
    });
});
    </script>
@endsection
