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

                                    <!--
                                    <a href="{{url('/admin/newbanner')}}" class="btn btn-primary btn-sm">Add Banner</a>
                                    <hr class="mt-0 mb-3 pb-2" />
                                    -->

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
                                        <div class="col-xs-12 col-md-6 mt-5">
                                            @if(isset($mypackages) && count($mypackages) > 0)

                                                <table class="table table-bordered text-center p-2">
                                                    <thead>
                                                        <tr>
                                                            <th class="order-id">S/NO</th>
                                                            <th class="order-date">PACKAGE</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $x = 0; @endphp
                                                        @foreach($mypackages as $mypackage)
                                                            @php $x++; @endphp
                                                            <tr>
                                                                <td class="p-2" style="width: 80px;">
                                                                    <p class=" mt-2">
                                                                        <span>{{$x}}</span>
                                                                    </p>
                                                                </td>
                                                                
                                                                <td class="p-2 text-center">
                                                                    <p class="mt-2">
                                                                        {{$mypackage->name}}
                                                                    </p>
                                                                </td>
                                                                
                                                                
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                


                                            @else
                                            <p class="text-center text-danger"> No package found!</p>
                                            @endif
                                        </div>

                                        <div class="col-xs-12 col-md-6" style="background: #f4f4f4; padding: 20px; box-shadow: 0 0.3em 0.35em rgba(128, 128, 128, 0.5);">
                                            <h4 class="text-center text-danger">UPGRADE YOUR PACKAGE</h4>
                                            <p class="text-center text-danger"><em>The cash difference between your previous package and selected package will be charged in cash.</em></p>

                                            <form  action="{{url('upgradepackage')}}" method="post" enctype="multipart/form-data">
                                                @csrf

                                                <div class="row">
                                                    <div class="col-md-12 col-xs-12">
                                                        <label for="login-country">
                                                            Select Package For Upgrade
                                                            <span class="required">*</span>
                                                        </label>
                                                        @if(isset($packages) && count($packages) > 0)
                                                        <select name="package" id="package" class="form-input form-wide" required>
                                                            <option value="">Select Package</option>
                                                            @foreach($packages as $package)
                                                            <option value="{{$package->id}}">{{$package->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @endif
                                                    </div>

                                                    <input type="hidden" name="prePackage" id="prePackage" class="form-input form-wide" value="{{$prevPackage->package_id}}" required />
                                            

                                                    <hr class="mt-3 mb-3 pb-2" />

                                                    <div class="col-md-12 col-xs-12 mt-2">
                                                        <button type="submit"name="caSH" value="1" class="btn btn-primary btn-md w-100">
                                                            PAY WITH CASH
                                                        </button>
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