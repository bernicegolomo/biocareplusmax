@extends('admin.layout')

@section('content') 

        <main class="main">
			<div class="page-header">
				<div class="container d-flex flex-column align-items-center">
					<nav aria-label="breadcrumb" class="breadcrumb-nav">
						<div class="container">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{url('/admin/dashboard')}}">Home</a></li>
								<li class="breadcrumb-item"><a href="{{url('/admin/stockistpackages')}}">Stockist Packages</a></li>
								<li class="breadcrumb-item active" aria-current="page">
									Add stockist package
								</li>
							</ol>
						</div>
					</nav>

					<h1>Stockist Packages</h1>
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
                                


						<div class="row">
							<div class="col-md-12">

								<form  action="{{url('createpickup')}}" method="post" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row row-lg">
                                        <div class="col-md-6 col-xs-12">
                                            <div class="row">
                                                <div class="col-md-12 col-xs-12">
                                                    <label for="login-email">
                                                        Stockist Package
                                                        <span class="required">*</span>
                                                    </label>
                                                    @if(isset($data) && !empty($data))
                                                        @php $packageid = $data->type; @endphp
                                                    @endif
                                                    <select name="type" class="form-input form-wide" required>
                                                        @if(isset($packages) && count($packages) > 0)
                                                            @foreach($packages as $package)
                                                                <option value="{{ $package->id }}" @if(isset($packageid)) {{ $package->id == $packageid ? 'selected' : '' }} @endif>
                                                                    {{ $package->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>

                                                <div class="col-md-12 col-xs-12 mt-4">
                                                    <label for="login-email">
                                                        Pickup Location
                                                        <span class="required">*</span>
                                                    </label>
                                                    @if(isset($members) && count($members) > 0)
                                                    <select name="member" id="member" class="form-input form-wide" required>
                                                        <option value="">Select Location</option>
                                                        @foreach($members as $member)
                                                        <option value="{{$member->id}}">{{$member->username}} - {{$member->address}}</option>
                                                        @endforeach
                                                    </select>
                                                    @endif
                                                </div>

                                                
                                                
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-xs-12">
                                            <div class="row">
                                                <div class="col-md-4 col-xs-12 mt-4">
                                                    <button type="submit" value="1" name="token" class="btn btn-dark btn-md w-100">
                                                        SUBMIT
                                                    </button>
                                                </div>
                                            </div>
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