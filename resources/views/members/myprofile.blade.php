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
									My Profile
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

                                <form  action="{{url('updatemyprofile')}}" method="post" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="row">
                                                
                                                <div class="col-md-6 col-xs-12 mt-3">
                                                    <label for="login-email">
                                                        Full Name
                                                        <span class="required">*</span>
                                                    </label>
                                                    <input type="text" class="form-input form-wide" name="name" value="{{ $user->name }}" required />
                                                </div>
                                                
                                                <div class="col-md-6 col-xs-12 mt-3">
                                                    <label for="login-email">
                                                        Phone
                                                        <span class="required">*</span>
                                                    </label>
                                                    <input type="number" class="form-input form-wide" name="phone" value="{{ $user->phone }}" required step="any"  />
                                                </div>
                                                
                                                <input type="hidden" class="form-input form-wide" name="id" value="{{ $user->id }}" required step="any"  />

                                                <div class="col-md-12 col-xs-12 mt-3">
                                                    <label for="login-email">
                                                        Email
                                                        <span class="required">*</span>
                                                    </label>
                                                    <input type="email" class="form-input form-wide" name="email" value="{{ $user->email }}" required />
                                                </div>
                                                
                                                <div class="col-md-12 col-xs-12 mt-3">
                                                    <label for="login-country">
                                                        Select Your Country 
                                                        <span class="required">*</span>
                                                    </label>
                                                    @if(isset($countries) && count($countries) > 0)
                                                    <select name="country" class="form-input form-wide" required>
                                                        <option value="">Select Country</option>
                                                        @foreach($countries as $country)
                                                        <option value="{{$country->id}}" @if(!empty($user->country) && $user->country == $country->id) selected @endif >{{$country->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @endif
                                                </div>
                                                
                                                <div class="col-md-12 col-xs-12 mt-3">
                                                    <label for="login-password">
                                                        Address
                                                        <span class="required">*</span>
                                                    </label>
                                                    <textarea name="address" class="form-input form-wide" id="login-password" required> {{$user->address}}</textarea>
                                                </div>
                                                
                                                <div class="col-md-6 col-xs-12 mt-3">
                                                    <label for="login-country">
                                                        Select Your Bank 
                                                        <span class="required">*</span>
                                                    </label>
                                                    @if(isset($banks) && count($banks) > 0)
                                                    <select name="bankname" class="form-input form-wide" required>
                                                        <option value="">Select Bank</option>
                                                        @foreach($banks as $bank)
                                                        <option value="{{$bank->id}}" @if(!empty($user->bankname) && $user->bankname == $bank->id) selected @endif >{{$bank->bankname}}</option>
                                                        @endforeach
                                                    </select>
                                                    @endif
                                                </div>
                                                
                                                <div class="col-md-6 col-xs-12 mt-3">
                                                    <label for="login-email">
                                                        Bank Account
                                                        <span class="required">*</span>
                                                    </label>
                                                    <input type="number" class="form-input form-wide" name="account" value="{{ $user->bankaccount }}" required step="any"  />
                                                </div>
                                                
                                                
                                                <div class="col-md-6 col-xs-12 mt-3">
                                                    <label for="login-password">
                                                        Password <small><em class="text-danger">use only if you want to update your password</em></small>
                                                    </label>
                                                    <input type="password" name="password" class="form-input form-wide" id="login-password"/>
                                                </div>
                                                
                                                <div class="col-md-6 col-xs-12 mt-3">
                                                    <label for="login-password">
                                                        Profile Picture
                                                    </label>
                                                    <input type="file" name="image" class="form-input form-wide" id="login-password"/>
                                                </div>
                                            </div>

                            

                                            <div class="row mt-5">

                                                <div class="col-md-12 col-xs-12">
                                                    <button type="submit" value="1" name="token" class="btn btn-dark btn-md w-100">
                                                        UPDATE PROFILE
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