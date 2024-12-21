@extends('members.layout')

@section('content') 

        <main class="main">
			<div class="page-header">
				<div class="container d-flex flex-column align-items-center">
					<nav aria-label="breadcrumb" class="breadcrumb-nav">
						<div class="container">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">
									Entry Store
								</li>
							</ol>
						</div>
					</nav>

					<h1>Entry Store</h1>
				</div>
			</div>

			<div class="container account-container custom-account-container">
				<div class="row">
					<div class="sidebar widget widget-dashboard mb-lg-0 mb-3 col-lg-3 order-0">
						<h2 class="text-uppercase">Navigation</h2>
						<x-membermenu />
					</div>
					<div class="col-lg-9 order-lg-last order-1 tab-content">
						<div class="tab-pane fade show active" id="dashboard" role="tabpanel">
							<div class="dashboard-content">
								<p>
									Hello <strong class="text-dark">{{$user->name}}</strong> (not
									<strong class="text-dark">{{$user->name}}</strong>?
									<a href="{{url('/admin/logout')}}" class="btn btn-link ">Log out</a>)
								</p>

								

								<div class="mb-4"></div>

								@if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                
                                @if ($errors->any())
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
								
								@if(isset($products) && count($products) > 0)
                                    <section class="container pb-3 mb-1">
                                        <h2 class="section-title ls-n-15 text-center pt-2 m-b-2">Pick Your First Products</h2>
									    <p class="text-center pt-2 m-b-5 text-primary"><em>Welcome to our store! We're excited to offer you a special deal on your first purchase. The total amount of the package you signed up with will be fully subsidized. If the product you choose costs more than this amount, don't worry! We'll simply deduct the balance from your cash wallet. Enjoy your shopping and make the most of this fantastic offer!</em></p>

                                        <div class="row py-4">
                                            @foreach($products as $featuredProduct) 
                                            <div class="col-6 col-sm-4 col-md-4 col-xl-4 appear-animate" data-animation-name="fadeIn"
                                                data-animation-delay="300" data-animation-duration="1000">
                                                <div class="product-default inner-quickview inner-icon">
                                                    <figure>
                                                        <a href="#">
                                                            <img src="{{asset('front/assets/images/products/'.$featuredProduct->image)}}"  
                                                                 class="w-100" 
                                                                 style="width: 273px; height: 203px; object-fit: cover;"
                                                                 alt="{{$featuredProduct->name}}" />
                                                        </a>
                                                        <div class="label-group">
                                                            <div class="product-label label-hot">{{$featuredProduct->pv}} PV</div>
                                                            @if(!empty($featuredProduct->discount))
                                                                <div class="product-label label-sale">- {{$featuredProduct->discount}}</div>
                                                            @endif
                                                        </div>
                                                        <div class="btn-icon-group">
                                                            <button type="button" data-id="{{ $featuredProduct->id }}" 
                                                                    class="btn-icon btn-add-cart product-type-simple">
                                                                <i class="icon-shopping-cart"></i>
                                                            </button>
                                                        </div>
                                                        <button type="button" data-id="{{ $featuredProduct->id }}" 
                                                                class="btn-quickview btn-add-cart" title="Add To Cart" style="cursor:pointer;">
                                                            Add To Cart
                                                        </button>
                                                    </figure>
                                                    <div class="product-details">
                                                        <div class="category-wrap"> 
                                                            <div class="category-list">
                                                                <a href="" class="product-category">
                                                                    @php $category = App\Http\Controllers\AdminController::getcategory($featuredProduct->category_id);  @endphp
                                                                    @if(isset($category) && !empty($category))
                                                                         {{strtoupper($category->name)}}
                                                                    @endif
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <h3 class="product-title">
                                                            <a href="#">{{$featuredProduct->name}}</a>
                                                        </h3>
                                                        <div class="price-box">
                                                            @if(isset($id) && $id == 3)
                                                                <span class="old-price">
                                                                    <span class="text-danger">&#8358; {{number_format($featuredProduct->price)}}</span>
                                                                </span>
                                                            @endif
                                                            <span class="product-price">
                                                                &#8358; {{ number_format($featuredProduct->price) }}
                                                            </span>
                                                            <div class="product-single-qty">
                                                                <button type="button" class="btn btn-outline-secondary decrease-qty" style="display: none;">-</button>
                                                                <input class="horizontal-quantity form-control product-quantity" type="text" value="1" readonly>
                                                                <button type="button" class="btn btn-outline-secondary increase-qty" style="display: none;">+</button>
                                                            </div>
                                                            
                                                            
                                                            <input type="hidden" id="store" class="store" value="1">
                                                            <input type="hidden" id="type" name="type" class="type" value="{{$type}}">

                                                            
                                                        </div><!-- End .price-box -->
                                                    </div><!-- End .product-details -->
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        
                                        <div class="mt-5">
                                            @if(isset($query))
                                                {{ $products->appends($query)->links('pagination::bootstrap-5') }}
                                            @else
                                                {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
                                            @endif
                                        </div>
                                    </section>

                                @endif



								

							</div>
						</div><!-- End .tab-pane -->

						
					</div><!-- End .tab-content -->
				</div><!-- End .row -->
			</div><!-- End .container -->

			<div class="mb-5"></div><!-- margin -->
		</main><!-- End .main -->



@endsection