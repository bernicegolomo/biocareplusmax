@extends('admin.layout')

@section('content') 

        <main class="main">
			<div class="page-header">
				<div class="container d-flex flex-column align-items-center">
					<nav aria-label="breadcrumb" class="breadcrumb-nav">
						<div class="container">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{url('/admin/dashboard')}}">Home</a></li>
								<li class="breadcrumb-item"><a href="{{url('/admin/products')}}">Products</a></li>
								<li class="breadcrumb-item active" aria-current="page">
									Add new product
								</li>
							</ol>
						</div>
					</nav>

					<h1>Add Product</h1>
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
                                        @if(isset($data) && !empty($data))
                                            <form  action="{{url('updateproduct')}}" method="post" enctype="multipart/form-data">
                                        @else
                                            <form  action="{{url('addproduct')}}" method="post" enctype="multipart/form-data">
                                        @endif
                                            @csrf

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Product Name <span class="required">*</span></label>
                                                        <input type="text" class="form-control" name="name" @if(isset($data) && !empty($data)) value="{{$data->name}}" @endif required />
                                                    </div>
                                                </div>
                                                <div class="@if(isset($data) && !empty($data->image)) col-md-4 @else col-md-6 @endif">
                                                    <div class="form-group">
                                                        <label>Product Image @if(!isset($data))<span class="required">*</span> @endif</label>
                                                        <input type="file" class="form-control" name="image" @if(!isset($data)) required @endif/>
                                                    </div>
                                                </div>

                                                @if(isset($data) && !empty($data->image))
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <img src="{{asset('front/assets/images/products/'.$data->image)}}" alt="{{$data->name}}" class="w-100 mb-1" style="width: 100%; height: 100px; object-fit: cover;"/>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Product Quantity </label>
                                                        <input type="text" class="form-control" name="qty" @if(isset($data) && !empty($data)) value="{{$data->qty}}" @endif/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Product Actual Price <span class="required">*</span></label>
                                                        <input type="text" class="form-control" name="price" @if(isset($data) && !empty($data)) value="{{$data->price}}" @endif required />
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Product PV <span class="required">*</span></label>
                                                        <input type="text" class="form-control" name="pv" @if(isset($data) && !empty($data)) value="{{$data->pv}}" @endif required />
                                                    </div>
                                                </div>
                                            </div>

                                            @if(isset($data) && !empty($data))
                                                <input type="hidden" class="form-control" name="id" value="{{$data->id}}" />
                                            @endif

                                            <hr class="mt-2 mb-3 pb-2" />

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Discount </label>
                                                        <input type="text" class="form-control" name="discount" @if(isset($data) && !empty($data->discount)) value="{{$data->discount}}" @endif/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Product Discounted Price </label>
                                                        <input type="text" class="form-control" name="oldprice" @if(isset($data) && !empty($data->oldprice)) value="{{$data->oldprice}}" @endif/>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="mt-2 mb-3 pb-2" />

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Select Category <span class="required">*</span></label>
                                                        @if(isset($data) && !empty($data))
                                                            @php $categoryid = $data->category_id; @endphp
                                                        @endif
                                                        <select name="category" class="form-control" required>
                                                            @if(isset($categories) && count($categories) > 0)
                                                                @foreach($categories as $category)
                                                                    <option value="{{ $category->id }}" @if(isset($categoryid)) {{ $category->id == $categoryid ? 'selected' : '' }} @endif>
                                                                        {{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>

                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        @if(isset($data) && !empty($data->store))
                                                            @php $storesid = $data->store; @endphp
                                                        @endif
                                                        <label>Select Stores <span class="required">*</span></label>
                                                        <select id="stores-select" name="stores[]" class="form-control select2" required multiple="">
                                                            @if(isset($stores) && count($stores) > 0)
                                                                @foreach($stores as $store)
                                                                    <option value="{{ $store->id }}">
                                                                        {{ $store->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>




                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Product Description <span class="required">*</span></label>
                                                        <textarea class="form-control" name="content" required> @if(isset($data) && !empty($data)) {{$data->content}} @endif</textarea>
                                                    </div>
                                                </div>
                                                
                                            </div>

                                            <div class="row">

                                                <div class="col-md-6">

                                                    <div class="form-footer mb-0">
                                                        <div class="form-footer-right">
                                                            <button type="submit" name="submit" value="1" class="btn btn-dark btn-sm py-4">
                                                                Submit
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </form>
										
									</div>

									

								</div><!-- End .row -->
							</div>
						</div><!-- End .tab-pane -->

						
			<div class="mb-5"></div><!-- margin -->
		</main><!-- End .main -->



@endsection