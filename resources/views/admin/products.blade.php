@extends('admin.layout')

@section('content') 

        <main class="main">
			<div class="page-header">
				<div class="container d-flex flex-column align-items-center">
					<nav aria-label="breadcrumb" class="breadcrumb-nav">
						<div class="container">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{url('/admin/dashboard')}}">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">
									Manage Products
								</li>
							</ol>
						</div>
					</nav>

					<h1>Products</h1>
				</div>
			</div>

			<div class="container account-container custom-account-container">
				<div class="row">
                    <x-menu />
					<div class="col-lg-9 order-lg-last order-1 tab-content">
						<div class="tab-pane fade show active" id="dashboard" role="tabpanel">
							<div class="dashboard-content">
								<p>
									Hello <strong class="text-dark">{{$user->name}}</strong> (not
									<strong class="text-dark">{{$user->name}}</strong>?
									<a href="{{url('/admin/logout')}}" class="btn btn-link ">Log out</a>)
								</p>

								

								<div class="mb-4"></div>

								<a href="{{url('/admin/newproduct')}}" class="btn btn-primary btn-sm">Add Product</a>
                                <a href="{{url('/admin/categories')}}" class="btn btn-danger btn-sm">Manage Product Categories</a>
                                <hr class="mt-0 mb-3 pb-2" />
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
                                

								<div class="row row-lg">
									<div class="col-12 col-md-12">
                                        @if(isset($products) && count($products) > 0)

                                            <table class="table table-bordered text-center p-2">
                                                <thead>
                                                    <tr>
                                                        <th class="order-id">S/NO</th>
                                                        <th class="order-date">PRODUCT</th>
                                                        <th class="order-date">CATEGORY</th>
                                                        <th class="order-status">STORES</th>
                                                        <th class="order-date">QUANTITY</th>
                                                        <th class="order-price">AMOUNT (&#8358;)</th>
                                                        <th class="order-status">PV</th>
                                                        <th class="order-action">ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $x = 0; @endphp
                                                    @foreach($products as $product)
                                                        @php $x++; @endphp
                                                        <tr>
                                                            <td class="p-2">
                                                                <p class=" mt-2">
                                                                    <span>{{$x}}</span>
                                                                </p>
                                                            </td>

                                                            <td class="p-2 text-center">
                                                                <p>
                                                                    <img src="{{asset('front/assets/images/products/'.$product->image)}}" alt="{{$product->name}}" class="w-100 mb-1" style="width: 100%; height: 50px; object-fit: cover;"/>
                                                                    {{$product->name}}
                                                                </p>
                                                            </td>

                                                            <td class="p-2 text-center">
                                                                <p>
                                                                    @php $category = App\Http\Controllers\AdminController::getcategory($product->category_id);  @endphp
                                                                    @if(isset($category) && !empty($category))
                                                                        <label class="badge badge-primary text-white mt-2 text-center"> {{strtoupper($category->name)}}</label>
                                                                    @endif
                                                                </p>
                                                            </td>

                                                            <td class="p-2 text-center" style="width:30px;">
                                                                <p>
                                                                    @if(!empty($product->store))
                                                                        @php $stores = json_decode($product->store, true);   @endphp
                                                                    @endif

                                                                    @if(isset($stores) && count($stores) > 0)
                                                                        @foreach($stores as $store)
                                                                            @php $storez = App\Http\Controllers\AdminController::getstore($store);  @endphp
                                                                            @if(isset($storez) && !empty($storez))
                                                                                <label class="badge badge-danger text-white mt-1 text-center"> {{strtoupper($storez->name)}}</label>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif 
                                                                </p>
                                                            </td>

                                                            <td class="p-2">
                                                                <p class=" mt-2">
                                                                    {{$product->qty}}
                                                                </p>
                                                            </td>

                                                            <td class="p-2">
                                                                <p class=" mt-2">
                                                                    {{$product->price}}
                                                                </p>
                                                            </td>

                                                            <td class="p-2">
                                                                <p class=" mt-2">
                                                                    {{$product->pv}}
                                                                </p>
                                                            </td>

                                                            <td class="p-2">
                                                                <p>
                                                                    <div class="dropdown-primary dropdown open">
                                                                        <button class="btn btn-primary dropdown-toggle waves-effect waves-light btn-sm" type="button" id="dropdown-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Action</button>
                                                                        <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                                            <a class="dropdown-item waves-light waves-effect" href="{{url('admin/newproduct', Illuminate\Support\Facades\Crypt::encrypt($product->id))}}">Edit</a>

                                                                            <div class="dropdown-divider"></div>
                                                                                <a class="dropdown-item waves-light waves-effect" href="{{url('deleteproduct', Illuminate\Support\Facades\Crypt::encrypt($product->id))}}" onclick="return confirm(' Are you sure you want to delete this product?.');">Delete</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            


                                        @else
                                        <p class="text-center text-danger"> No product found!</p>
                                        @endif
									</div>

									

								</div><!-- End .row -->
							</div>
						</div><!-- End .tab-pane -->

						
			<div class="mb-5"></div><!-- margin -->
		</main><!-- End .main -->



@endsection