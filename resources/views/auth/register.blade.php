@extends('members.layout')

@section('content') 

		<main class="main">
			<div class="page-header">
				<div class="container d-flex flex-column align-items-center">
					<nav aria-label="breadcrumb" class="breadcrumb-nav">
						<div class="container">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
								<li class="breadcrumb-item"><a href="{{url('/shop')}}">Shop</a></li>
								<li class="breadcrumb-item active" aria-current="page">
									My Account
								</li>
							</ol>
						</div>
					</nav>

					<h1>My Account</h1>
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

						<div class="row">
							<div class="col-md-12">
								<div class="heading mb-1">
									<h2 class="title">BCM MEMBER REGISTRATION</h2>
								</div>

								<form  action="{{url('register')}}" method="post" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-6 col-xs-12">
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
                                                        Referrer ID (optional)
                                                    </label>
                                                    <input type="text" class="form-input form-wide" name="referrer" value="{{ old('referrer') }}"/>
                                                </div>

                                                <div class="col-md-6 col-xs-12">
                                                    <label for="login-email">
                                                        Placement (optional)
                                                    </label>
                                                    <select name="placement" class="form-input form-wide">
                                                        <option value="">Select Placement</option>
                                                        <option value="left">Left</option>
                                                        <option value="right">Right</option>
                                                    </select>
                                                </div>

                                                
                                                <div class="col-md-12 col-xs-12">
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

                                        <div class="col-md-6 col-xs-12">

                                            
                                            <!--<h3 class="">PAYMENT INFORMATION</h3>-->

                                            

                                            <p class="text-danger">You are expected to pay the sum of: <span id="conversion-rate">&#8358; {{number_format($package->price)}}</span></p>

                                            <div class="col-md-12 col-xs-12">
                                                <label for="login-email">
                                                    Enter BCM Access Token
                                                </label>
                                                <input type="text" class="form-input form-wide" name="accesstoken" value="{{ old('token') }}"/>
                                            </div>

                                            <div class="form-footer">
                                                <div class="custom-control custom-checkbox mb-0">
                                                    <input type="checkbox" class="custom-control-input" id="lost-password" />
                                                    <label class="custom-control-label mb-0" for="lost-password">By Clicking, you accept BCM <a href="">terms and condition</a>.</label>
                                                </div>
                                            </div>

                                            <button type="submit" value="1" name="token" class="btn btn-dark btn-md w-100">
                                                PAY WITH ACCESS TOKEN
                                            </button>
                                            <hr class="mt-3 mb-3 pb-2" />

                                            <!--
                                            <button type="submit"name="card" value="1" class="btn btn-primary btn-md w-100">
                                                PAY WITH CARD
                                            </button>
                                            -->

                                            <div class="form-footer">
                                                <label class="mb-0 text-danger" for="lost-password">Already signed up? <a href="{{url('login')}}"><strong>Login Now!</strong></a></label>
                                                
                                            </div>

                                            <a href="{{url('selectpackage')}}"
                                                    class="forget-password text-dark form-footer-right">Not this package? Choose another package!</a>
                                            


                                            <input type="hidden" name="package" id="package" class="form-input form-wide" value="{{$id}}" required />
                                            <input type="hidden" name="total" id="total" class="form-input form-wide" value="{{$package->price}}" required />
                                            <input type="hidden" name="pv" id="pv" class="form-input form-wide" value="{{$package->actual_pv}}" required />
                                            <input type="hidden" name="voucher" id="voucher" class="form-input form-wide" value="{{$package->free_voucher}}" required />
                                            
                                            <input type="hidden" class="form-input form-wide" name="amount" value="{{$package->price}}" id="amount"/>
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