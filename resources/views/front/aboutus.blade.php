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
									ABOUT US
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

                        @if(isset($page) && !empty($page))
                            <div class="container">
                                <div>
                                    @if(!empty($page->image))
                                        <div class="row mt-5">
                                            <img class="img-thumbnail avatar-sm" src="{{ URL::asset('front/assets/images/'.$page->image) }}" data-holder-rendered="true" style="width: 100%; height: 200px; object-fit: cover;">
                                        </div>
                                     @endif
                                     
                                    <div class="row mt-5 mb-5">
                                        <div class="col-md-8 mx-auto">
                                        {!! $page->content !!}
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End .container -->
                        @endif

					</div>
				</div>
			</div>
		</main><!-- End .main -->

		
@endsection