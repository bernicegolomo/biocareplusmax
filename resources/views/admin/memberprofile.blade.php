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
									Members
								</li>
							</ol>
						</div>
					</nav>

					<h1>All Members</h1>
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

                                    
                                    <a href="{{url('/admin/membertree/'.$data->id)}}" class="btn btn-primary btn-sm">View Member Genealogy</a>
                                    <a href="{{url('admin/listdownlines/' . $data->id)}}" class="btn btn-danger btn-sm">List View</a>
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
                                        <div class="col-4 col-md-4 col-xs-12" style="display: flex; justify-content: center; align-items: center;">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @if(!empty($data->profile_picture))
                                                        <img src="{{ asset('front/assets/images/profiles/' . $data->profile_picture) }}" class="w-100 img-circle" style="width: 100px; height: 100px; object-fit: cover;" />
                                                    @else    
                                                        <span class="icon-circle"><i class="icon-user-2" ></i></span>
                                                    @endif
                                                </div>
                                                
                                                <div class="col-md-12">
                                                    <div class="mt-3 text-center">
                                                        @php $sponsor = App\Http\Controllers\MemberController::memberdetails($data->referrer_id); @endphp
                                                        @if(isset($sponsor) && !empty($sponsor))
                                                            <strong>Sponsor: </strong>{{$sponsor->username}}
                                                        @endif
                										<div class="feature-box text-center pb-0 bg-info">
                											<div class="feature-box-content">
                												<h3>CURRENT PACKAGE</h3>
                												@php $currentPackage =  App\Http\Controllers\MemberController::getMemberPackage($data->id) ; @endphp
                												<h3 class="text-white">{{$currentPackage->name}}</h3><br/>
                											</div>
                										</div>
                									</div>
                									
                									<div class="">
                										<div class="feature-box text-center pb-0 bg-secondary">
                											<div class="feature-box-content p-0">
                												<h3>Rank</h3>
                												@php $rank =  App\Http\Controllers\MemberController::calculateUserRank($data->id) ; @endphp
                												<h3 class="text-white">{{$rank}}</h3><br/>
                											</div>
                										</div>
                									</div>
                									
                									<div class="">
                										<div class="feature-box text-center pb-0 bg-danger">
                											<div class=" feature-box-content">
                												<h3>DOWNLINES</h3>
                												<h3 class="text-white">{{$data->countLeftDescendants()}} Left  :: {{$data->countRightDescendants()}} Right</h3><br/>
                											</div>
                										</div>
                									</div>
    
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-8 col-md-8 col-xs-12">
                                            <form  action="{{url('updatememberprofile')}}" method="post" enctype="multipart/form-data">
                                                @csrf
            
                                                <div class="row">
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="row">
                                                            
                                                            <div class="col-md-6 col-xs-12 mt-3">
                                                                <label for="login-email">
                                                                    Full Name
                                                                    <span class="required">*</span>
                                                                </label>
                                                                <input type="text" class="form-input form-wide" name="name" value="{{ $data->name }}" required />
                                                            </div>
                                                            
                                                            <div class="col-md-6 col-xs-12 mt-3">
                                                                <label for="login-email">
                                                                    Phone
                                                                    <span class="required">*</span>
                                                                </label>
                                                                <input type="number" class="form-input form-wide" name="phone" value="{{ $data->phone }}" required step="any"  />
                                                            </div>
                                                            
                                                            <input type="hidden" class="form-input form-wide" name="id" value="{{ $data->id }}" required step="any"  />
            
                                                            <div class="col-md-6 col-xs-12 mt-3">
                                                                <label for="login-email">
                                                                    Username
                                                                    <span class="required">*</span>
                                                                </label>
                                                                <input type="text" class="form-input form-wide" name="username" value="{{ $data->username }}" required />
                                                            </div>
                                                            
                                                            <div class="col-md-6 col-xs-12 mt-3">
                                                                <label for="login-email">
                                                                    Sponsor
                                                                    <span class="required">*</span>
                                                                </label>
                                                                
                                                                <input type="text" class="form-input form-wide" name="sponsor" @if(!empty($sponsor->username)) value="{{ $sponsor->username }}" @endif required />
                                                            </div>
                                                            
                                                            <div class="col-md-12 col-xs-12 mt-3">
                                                                <label for="login-email">
                                                                    Email
                                                                    <span class="required">*</span>
                                                                </label>
                                                                <input type="email" class="form-input form-wide" name="email" value="{{ $data->email }}" required />
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
                                                                    <option value="{{$country->id}}" @if(!empty($data->country) && $data->country == $country->id) selected @endif >{{$country->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="col-md-12 col-xs-12 mt-3">
                                                                <label for="login-password">
                                                                    Address
                                                                    <span class="required">*</span>
                                                                </label>
                                                                <textarea name="address" class="form-input form-wide" id="" required> {{$data->address}}</textarea>
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

								</div><!-- End .row -->
							</div>
						</div><!-- End .tab-pane -->

						
			<div class="mb-5"></div><!-- margin -->
		</main><!-- End .main -->



@endsection