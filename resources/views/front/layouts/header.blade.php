<div class="bg-light top-header">
	<div class="container">
		<div class="row align-items-center py-3 d-none d-lg-flex justify-content-between">
			<div class="col-lg-4 logo">
				<a href="{{ route('front.home') }}" class="text-decoration-none">
					<span class="h1 text-uppercase text-primary bg-dark px-2">Online</span>
					<span class="h1 text-uppercase text-dark bg-primary px-2 ml-n1">SHOP</span>
				</a>
			</div>
			<div class="col-lg-6 col-6 text-left  d-flex justify-content-end align-items-center">
                @if(Auth::check())
                                    <a href="{{ route('account.profile') }}" class="nav-link text-dark">My Account</a>
                @else
                <a href="{{ route('front.login') }}" class="nav-link text-dark">Login/Register</a>

                                    @endif

                                    <form action="{{ route('front.shop') }}" method="get">
					<div class="input-group">
						<input value="{{ Request::get('search') }}" type="text" id="search" placeholder="Search For Products" class="form-control" name="search">
						<button type="submit" class="input-group-text">
							<i class="fa fa-search"></i>
					  	</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<header class="bg-dark">
	<div class="container">
		<nav class="navbar navbar-expand-xl" id="navbar">
			<a href="index.php" class="text-decoration-none mobile-logo">
				<span class="h2 text-uppercase text-primary bg-dark">Online</span>
				<span class="h2 text-uppercase text-white px-2">SHOP</span>
			</a>
			<button class="navbar-toggler menu-btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      			<!-- <span class="navbar-toggler-icon icon-menu"></span> -->
				  <i class="navbar-toggler-icon fas fa-bars"></i>
    		</button>
    		<div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        @if(getCategories()->isNotEmpty())
            @foreach(getCategories() as $category)
                <li class="nav-item dropdown">
                    <button class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ $category->name }}
                    </button>

                    @if ($category->sub_category->isNotEmpty())
                        <ul class="dropdown-menu dropdown-menu-dark">
                            @foreach($category->sub_category as $sub_Category)
                                <li>
                                    <a class="dropdown-item nav-link"
                                       href="{{ url('shop/'.$category->slug.'/'.$sub_Category->slug) }}">
                                        {{ $sub_Category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        @endif

    </ul>
</div>

			<div class="right-nav py-0">
				<a href="{{ route('front.cart') }}" class="ml-3 d-flex pt-2">
					<i class="fas fa-shopping-cart text-primary"></i>
				</a>
			</div>
      	</nav>
  	</div>
</header>
