@extends('admin.layout')

@section('content') 

        <main class="main">
			<div class="page-header">
				<div class="container d-flex flex-column align-items-center">
					<nav aria-label="breadcrumb" class="breadcrumb-nav">
						<div class="container">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{url('/admin/dashboard')}}">Home</a></li>
								<li class="breadcrumb-item"><a href="{{url('/admin/banners')}}">Manage Banner</a></li>
								<li class="breadcrumb-item active" aria-current="page">
									Add Banner
								</li>
							</ol>
						</div>
					</nav>

					<h1>New Banner</h1>
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
                                        <form  action="{{url('createbanner')}}" method="post" enctype="multipart/form-data">
                                            @csrf

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Title <span class="required">*</span></label>
                                                        <input type="text" class="form-control" name="title" required />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Banner Image <span class="required">*</span></label>
                                                        <input type="file" class="form-control" name="image" required />
                                                    </div>
                                                </div>
                                                
                                            </div>

                                            <div class="row">

                                                <div class="col-md-6">

                                                    <div class="form-footer mb-0">
                                                        <div class="form-footer-right">
                                                            <button type="submit" name="submit" value="1" class="btn btn-dark py-4">
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