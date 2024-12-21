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
									My Downlines
								</li>
							</ol>
						</div>
					</nav>

					<h1>My Downlines</h1>
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
								
								@if(isset($packages) && count($packages) > 0)
                                    <div class="container cta">
                                        <div class="mt-6 mb-8">
                                            <h3 class="text-center mb-5">Select from the below packages to continue with your registration</h3>
                                            <div class="row">
                                                @php
                                                    $colors = ['', 'bg-primary', 'bg-dark', 'bg-danger', 'bg-secondary', 'bg-warning']; // Define your colors here
                                                    $textcolors = ['text-dark', 'text-white', 'text-white', 'text-white', 'text-white', 'text-white'];
                                                    $btncolors = ['btn-primary', 'btn-light', 'btn-light', 'btn-light', 'btn-light', 'btn-light'];
                                                    $btextcolors = ['text-white', 'text-primary', 'text-dark', 'text-danger', 'text-primary', 'text-primary'];
                                                @endphp
                                                @foreach($packages as $index => $package)
                                                    @php
                                                    $bgColor = $colors[$index % count($colors)];
                                                    $btnClass = $btncolors[$index % count($btncolors)];
                                                    $textClass = $textcolors[$index % count($textcolors)];
                                                    $btextClass = $btextcolors[$index % count($btextcolors)];
                                                    @endphp
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="cta-simple cta-border {{ $bgColor }} {{$textClass}}">
                                                            <h3 class="font-weight-normal {{$textClass}}"> 
                                                                <b>
                                                                    &#8358;  {{$package->price}}
                                                                </b> 
                                                            </h3>
                                                            <p class="{{$textClass}}"> {{ strtoupper($package->name) }} </p>
                                                            <p class="{{$textClass}}">{{ $package->content }}</p>
                                                            <div class="mt-2"><a href="{{ url('/registerdownline/'.$package->id) }}" class="btn btn-lg {{ $btnClass }} {{$btextClass}}" style="cursor:pointer;">Register Now!</a></div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div><!-- End .container -->
                                @endif

								
							</div>
						</div><!-- End .tab-pane -->

						
						
					</div><!-- End .tab-content -->
				</div><!-- End .row -->
			</div><!-- End .container -->

			<div class="mb-5"></div><!-- margin -->
		</main><!-- End .main -->



@endsection