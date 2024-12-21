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
									Manage Stockist Packages
								</li>
							</ol>
						</div>
					</nav>

					<h1>Packages</h1>
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

								<a href="{{url('/admin/newstokistpackage')}}" class="btn btn-primary btn-sm">Add Package</a>
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
									<div class="col-12 col-md-12">
                                        @if(isset($packages) && count($packages) > 0)

                                            <table class="table table-bordered text-center p-2">
                                                <thead>
                                                    <tr>
                                                        <th class="order-id">S/NO</th>
                                                        <th class="order-date">PACKAGE NAME</th>
                                                        <th class="order-status">REGISTRATION COMMISSION</th>
                                                        <th class="order-status">RESTOCK COMMISSION</th>
                                                        <th class="order-status">RESTOCK COMMISSION (STOCKIST)</th>
                                                        <th class="order-status">STOCKIST SPONSOR COMMISSION</th>
                                                        <th class="order-status">POSITION</th>
                                                        <th class="order-action">ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $x = 0; @endphp
                                                    @foreach($packages as $package)
                                                        @php $x++; @endphp
                                                        <tr>
                                                            <td class="p-2">
                                                                <p class=" mt-2">
                                                                    <span>{{$x}}</span>
                                                                </p>
                                                            </td>

                                                            <td class="p-2 text-center">
                                                                <p class=" mt-2">   
                                                                    <p>{{$package->name}}</p>
                                                                </p>
                                                            </td>
                                                            
                                                            <td class="p-2 text-center">
                                                                <p class=" mt-2">   
                                                                    <p>{{$package->commission}}</p>
                                                                </p>
                                                            </td>
                                                            
                                                            <td class="p-2 text-center">
                                                                <p class=" mt-2">   
                                                                    <p>{{$package->restock_commission}}</p>
                                                                </p>
                                                            </td>
                                                            
                                                            <td class="p-2 text-center">
                                                                <p class=" mt-2">   
                                                                    <p>{{$package->pickup_restock_commission}}</p>
                                                                </p>
                                                            </td>
                                                            
                                                            <td class="p-2 text-center">
                                                                <p class=" mt-2">   
                                                                    <p>{{$package->sponsor_commission}}</p>
                                                                </p>
                                                            </td>

                                                            <td class="p-2 text-center">
                                                                <p class=" mt-2">   
                                                                    <p>{{$package->position}}</p>
                                                                </p>
                                                            </td>

                                                            <td class="p-2">
                                                                <p>
                                                                    <div class="dropdown-primary dropdown open">
                                                                        <button class="btn btn-primary dropdown-toggle waves-effect waves-light btn-sm" type="button" id="dropdown-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Action</button>
                                                                        <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                                            <a class="dropdown-item waves-light waves-effect" href="{{url('admin/newstokistpackage', Illuminate\Support\Facades\Crypt::encrypt($package->id))}}">Edit</a>

                                                                            <div class="dropdown-divider"></div>
                                                                                <a class="dropdown-item waves-light waves-effect" href="{{url('deletestockistpackage', Illuminate\Support\Facades\Crypt::encrypt($package->id))}}" onclick="return confirm(' Are you sure you want to delete this package? Deleting this will affect all records linked to this package.');">Delete</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            


                                        @else
                                        <p class="text-center text-danger"> No product found!</p>
                                        @endif
									</div>

									

								</div><!-- End .row -->
							</div>
						</div><!-- End .tab-pane -->

						
			<div class="mb-5"></div><!-- margin -->
		</main><!-- End .main -->



@endsection