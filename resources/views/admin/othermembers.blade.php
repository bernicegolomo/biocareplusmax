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
                                            @if(isset($members) && count($members) > 0)

                                                <table class="table table-bordered text-center p-2">
                                                    <thead>
                                                        <tr>
                                                            <th class="order-id">S/NO</th>
                                                            <th class="order-date">FULL NAME</th>
                                                            <th class="order-date">USERNAME</th>
                                                            <th class="order-date">SPONSOR</th>
                                                            <th class="order-date">CURRENT PACKAGE</th>
                                                            <th class="order-date">RANK</th>
                                                            <th class="order-action">ACTION</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $x = 0; @endphp
                                                        @foreach($members as $member)
                                                            @php $x++; @endphp
                                                            <tr>
                                                                <td class="p-2" style="width: 80px;">
                                                                    <p class=" mt-2">
                                                                        <span>{{$x}}</span>
                                                                    </p>
                                                                </td>
                                                                
                                                                <td class="p-2 text-center">
                                                                    <p class="mt-2">
                                                                        {{$member->name}}
                                                                    </p>
                                                                </td>
                                                                <td class="p-2 text-center">
                                                                    <p class="mt-2">
                                                                        {{$member->username}}
                                                                    </p>
                                                                </td>
                                                                
                                                                <td class="p-2 text-center">
                                                                    <p class="mt-2">
                                                                        @php $sponsor = App\Http\Controllers\MemberController::memberdetails($member->referrer_id); @endphp
                                                                        @if(isset($sponsor) && !empty($sponsor))
                                                                            {{$sponsor->username}}
                                                                        @endif
                                                                    </p>
                                                                </td>
                                                                
                                                                <td class="p-2 text-center">
                                                                    <p class="mt-2">
                                                                        @php $package = App\Http\Controllers\MemberController::getMemberPackage($member->id); @endphp
                                                                        @if(isset($package) && !empty($package))
                                                                            {{$package->name}}
                                                                        @endif
                                                                    </p>
                                                                </td>
                                                                
                                                                <td class="p-2 text-center">
                                                                    <p class="mt-2">
                                                                        @php $rank = App\Http\Controllers\MemberController::calculateUserRank($member->id); @endphp
                                                                        {{$rank}}
                                                                    </p>
                                                                </td>


                                                                <td class="p-2" style="width: 80px;">
                                                                    <p class="mt-2">
                                                                        <div class="dropdown-primary dropdown open">
                                                                            <button class="btn btn-primary dropdown-toggle waves-effect waves-light btn-sm" type="button" id="dropdown-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Action</button>
                                                                            <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                                                <a class="dropdown-item waves-light waves-effect" href="{{url('viewmember', Illuminate\Support\Facades\Crypt::encrypt($member->id))}}">View</a>

                                                                                <div class="dropdown-divider"></div>
                                                                                    <a class="dropdown-item waves-light waves-effect" href="{{url('deletemember', Illuminate\Support\Facades\Crypt::encrypt($member->id))}}" onclick="return confirm(' Are you sure you want to delete this member? Deleting this will affect all records linked to this member.');">Delete</a>
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
                                            <p class="text-center text-danger"> No Member found!</p>
                                            @endif
                                        </div>

                                        <div class="mt-5">
                                            @if(isset($query))
                                                {{ $members->appends($query)->links('pagination::bootstrap-5') }}
                                            @else
                                                {{ $members->withQueryString()->links('pagination::bootstrap-5') }}
                                            @endif
                                        </div>

								</div><!-- End .row -->
							</div>
						</div><!-- End .tab-pane -->

						
			<div class="mb-5"></div><!-- margin -->
		</main><!-- End .main -->
		
		
		


@endsection