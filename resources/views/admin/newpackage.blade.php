@extends('admin.layout')

@section('content') 

        <main class="main">
			<div class="page-header">
				<div class="container d-flex flex-column align-items-center">
					<nav aria-label="breadcrumb" class="breadcrumb-nav">
						<div class="container">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{url('/admin/dashboard')}}">Home</a></li>
								<li class="breadcrumb-item"><a href="{{url('/admin/packages')}}">Packages</a></li>
								<li class="breadcrumb-item active" aria-current="page">
									{{$title}}
								</li>
							</ol>
						</div>
					</nav>

					<h1>{{$title}}</h1>
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
                                        <form  action=" @if(isset($data)) {{url('updatepackage')}} @else {{url('addpackage')}} @endif" method="post" enctype="multipart/form-data">
                                            @csrf

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Package Name <span class="required">*</span></label>
                                                        <input type="text" class="form-control" name="name" required @if(isset($data)) value="{{$data->name}}" @endif/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Package Price <span class="required">*</span></label>
                                                        <input type="text" class="form-control" name="price" required @if(isset($data)) value="{{$data->price}}" @endif/>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if(isset($data))
                                                <input type="hidden" class="form-control" name="id" required @if(isset($data)) value="{{$data->id}}" @endif/>
                                            @endif

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Actual PV <span class="required">*</span> </label>
                                                        <input type="text" class="form-control" name="actualpv" required @if(isset($data)) value="{{$data->actual_pv}}" @endif/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Bonus PV <span class="required">*</span></label>
                                                        <input type="text" class="form-control" name="bonuspv" required @if(isset($data)) value="{{$data->bonus_pv}}" @endif/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Free Voucher </label>
                                                        <input type="text" class="form-control" name="voucher" required @if(isset($data)) value="{{$data->free_voucher}}" @endif/>
                                                    </div>
                                                </div>

                                                
                                                
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Package Description </label>
                                                        <textarea class="form-control" name="content">@if(isset($data)) {{$data->content}} @endif</textarea>
                                                    </div>
                                                </div>
                                                
                                            </div>

                                            <div class="row">

                                                <div class="col-md-6">

                                                    <div class="form-footer mb-0">
                                                        <div class="form-footer-right">
                                                            <button type="submit" name="submit" value="1" class="btn btn-dark btn-sm py-4">
                                                                Submit Package
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