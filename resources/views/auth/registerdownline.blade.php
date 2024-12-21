@extends('members.layout')

@section('content') 

		<main class="main">
            <div class="page-header">
				<div class="container d-flex flex-column align-items-center">
					<nav aria-label="breadcrumb" class="breadcrumb-nav">
						<div class="container">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Home</a></li>
								<li class="breadcrumb-item"><a href="{{url('/mydonlines')}}">Downlines</a></li>
								<li class="breadcrumb-item active" aria-current="page">
									Register Downlines
								</li>
							</ol>
						</div>
					</nav>

					<h1>New Downline</h1>
				</div>
			</div>

			<div class="container login-container">
				<div class="row">
					<div class="col-lg-12 mx-auto">

                        <div class="row">
                            <div class="col-xs-12">
                                @include('partials.errors')
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
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

								<div class="row row-lg">
			

								<form  action="{{url('registerdownline')}}" method="post" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="row">
                                                <div class="col-md-6 col-xs-12">
                                                    <label for="login-email">
                                                        Full Name
                                                        <span class="required">*</span>
                                                    </label>
                                                    <input type="text" class="form-input form-wide" name="name" value="{{ old('name') }}" required />
                                                </div>

                                                <div class="col-md-6 col-xs-12">
                                                    <label for="login-email">
                                                        Username
                                                        <span class="required">*</span>
                                                    </label>
                                                    <input type="text" class="form-input form-wide" name="username" value="{{ old('username') }}" required />
                                                </div>

                                                <div class="col-md-12 col-xs-12">
                                                    <label for="login-email">
                                                        Email
                                                        <span class="required">*</span>
                                                    </label>
                                                    <input type="email" class="form-input form-wide" name="email" value="{{ old('email') }}" required />
                                                </div>

                                                
                                                <div class="col-md-6 col-xs-12">
                                                    <label for="login-password">
                                                        Password
                                                        <span class="required">*</span>
                                                    </label>
                                                    <input type="password" name="password" class="form-input form-wide" id="login-password" required />
                                                </div>
                                                
                                                <div class="col-md-6 col-xs-12">
                                                    <label for="login-password">
                                                        Confirm Password
                                                        <span class="required">*</span>
                                                    </label>
                                                    <input type="password" name="password_confirmation" class="form-input form-wide" id="login-password" required />
                                                </div>
                                                
                                                <div class="col-md-6 col-xs-12">
                                                    <label for="login-email">
                                                        Referrer ID 
                                                        <span class="required">*</span>
                                                    </label>
                                                    
                                                    <input type="text" class="form-input form-wide" name="referrer" value="{{ old('referrer_id') }}"  required=""/>
                                                    <!--@if(isset($usernames) && count($usernames) > 0)
                                                    <select name="referrer" id="referrer" class="form-input form-wide" required>
                                                        @foreach($usernames as $username)
                                                        <option value="{{$username}}">{{$username}}</option>
                                                        @endforeach
                                                    </select>
                                                    @endif-->
                                                </div>

                                                <div class="col-md-6 col-xs-12">
                                                    <label for="login-email">
                                                        Placement 
                                                        <span class="required">*</span>
                                                    </label>
                                                    <select name="placement" class="form-input form-wide"  required="">
                                                        <option value="">Select Placement</option>
                                                        <option value="left">Left</option>
                                                        <option value="right">Right</option>
                                                    </select>
                                                </div>


                                                <div class="col-md-6 col-xs-12">
                                                    <label for="login-email">
                                                        Binary Position 
                                                        <span class="required">*</span>
                                                    </label>
                                                    <input type="text" class="form-input form-wide" name="binary" value="{{ old('referrer_id') }}" required=""/>
                                                    
                                                    <!--@if(isset($usernames) && count($usernames) > 0)
                                                    <select name="binary" id="binary" class="form-input form-wide">
                                                        <option value="">-- Select Binary Placement -- </option>
                                                        @foreach($usernames as $username)
                                                        <option value="{{$username}}">{{$username}}</option>
                                                        @endforeach
                                                    </select>
                                                    @endif-->
                                                </div>
                                                
                                                <div class="col-md-6 col-xs-12" style="padding-left: 15px;">
                                                    <label for="login-country">
                                                        Select Your Country 
                                                        <span class="required">*</span>
                                                    </label>
                                                    @if(isset($countries) && count($countries) > 0)
                                                    <select name="country" id="countryz" class="form-input form-wide" required>
                                                        <option value="">Select Country</option>
                                                        @foreach($countries as $country)
                                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-xs-12">

                                            
                                            <!--<h3 class="">PAYMENT INFORMATION</h3>-->

                                            

                                            <p class="text-danger">You are expected to pay the sum of: <span id="conversion-rate">&#8358; {{number_format($package->price)}}</span></p>

                                            <div class="form-footer">
                                                <div class="custom-control custom-checkbox mb-0">
                                                    <input type="checkbox" class="custom-control-input" id="lost-password" />
                                                    <label class="custom-control-label mb-0" for="lost-password">By Clicking, you accept BCM <a href="">terms and condition</a>.</label>
                                                </div>
                                            </div>

                                            <button type="submit" value="1" name="token" class="btn btn-dark btn-md w-100">
                                                PAY WITH VOUCHER
                                            </button>


                                            <a href="{{url('newdownline')}}"
                                                    class="forget-password text-dark form-footer-right">Not this package? Choose another package!</a>
                                            


                                            <input type="hidden" name="package" id="package" class="form-input form-wide" value="{{$id}}" required />
                                            <input type="hidden" name="total" id="total" class="form-input form-wide" value="{{$package->price}}" required />
                                            <input type="hidden" name="pv" id="pv" class="form-input form-wide" value="{{$package->actual_pv}}" required />
                                            <input type="hidden" name="voucher" id="voucher" class="form-input form-wide" value="{{$package->free_voucher}}" required />
                                            
                                            <input type="hidden" class="form-input form-wide" name="amount" id="amount" value="{{$package->price}}"/>
                                        </div>

                                        <div class="col-md-6 col-xs-12">

                                            

                                            

                                            
                                        </div>
                                    </div>
								</form>
							</div>

                            
							
						</div>
					</div>
				</div>
			</div>
		</main><!-- End .main -->

		
@endsection