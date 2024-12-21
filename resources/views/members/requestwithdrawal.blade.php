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
									Request Withdrawal
								</li>
							</ol>
						</div>
					</nav>

					<h1>{{$title}}</h1>
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
                                
								<hr class="mt-0 mb-3 pb-2" />
                                <p class="text-danger text-center"><em>Your minimum withdrawal fee is 5,000 Naira. You will be charged 5% Admin Charges.</em></p>


                                <form  action="{{url('processwithdrawal')}}" method="post" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="row">
                                                
                                                <div class="col-md-6 col-xs-12 mt-3">
                                                    <label for="login-email">
                                                        Amount
                                                        <span class="required">*</span>
                                                    </label>
                                                    <input type="number" class="form-input form-wide" name="amount" required step="any"  />
                                                </div>
                                                
                                                
                                            </div>

                            

                                            <div class="row mt-5">

                                                <div class="col-md-12 col-xs-12">
                                                    <button type="submit" value="1" name="token" class="btn btn-dark btn-md w-100">
                                                        PROCEED TO WITHDRAW
                                                    </button>
                                                </div>

                                                

                                                
                                                
                                            </div>
                                        </div>

                                        
                                    </div>
								</form>
								
								
							</div>
						</div><!-- End .tab-pane -->

						
						
					</div><!-- End .tab-content -->
				</div><!-- End .row -->
			</div><!-- End .container -->

			<div class="mb-5"></div><!-- margin -->
		</main><!-- End .main -->



@endsection