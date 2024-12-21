@extends('members.layout')

@section('content') 

		<main class="main">
			<div class="page-header">
				<div class="container d-flex flex-column align-items-center">
					<nav aria-label="breadcrumb" class="breadcrumb-nav">
						<div class="container">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">
									BCM Packages
								</li>
							</ol>
						</div>
					</nav>

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
                                            <div class="col-lg-3 mb-2">
                                                <div class="cta-simple cta-border {{ $bgColor }} {{$textClass}}">
                                                    <h3 class="font-weight-normal {{$textClass}}"> <b>&#8358; {{ number_format($package->price) }}</b> </h3>
                                                    <p class="{{$textClass}}"> {{ strtoupper($package->name) }} </p>
                                                    <p class="{{$textClass}}">{{ $package->content }}</p>
                                                    <div class="mt-2"><a href="{{ url('/register/'.$package->id) }}" class="btn btn-lg {{ $btnClass }} {{$btextClass}}" style="cursor:pointer;">Register Now!</a></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div><!-- End .container -->
                        @endif

					</div>
				</div>
			</div>
		</main><!-- End .main -->

		
@endsection