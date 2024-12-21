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
									{{$title}}
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

                        @if(isset($pages) && count($pages) > 0)
                            <div class="container">
                                
                                @foreach($pages as $page)
                                    <div class="mb-5" style="padding: 20px 25px 0px; border: 1px solid #e1e1e1; background-color: #f9f9f9; border-radius: 4px;">
                                        <h3 class="text-center mb-5 text-danger">{{$page->name}}</h3>
                                    
                                        @if(!empty($page->image))
                                            <div class="row">
                                                <img class="img-thumbnail avatar-sm" src="{{ URL::asset('front/assets/images/'.$page->image) }}" data-holder-rendered="true" style="width: 100%; height: 200px; object-fit: cover;">
                                            </div>
                                         @endif
                                         
                                        <div class="row mt-5">
                                            {!! $page->content !!}
                                        </div>
                                    </div>
                                @endforeach
                                
                            </div><!-- End .container -->
                        @endif

					</div>
				</div>
			</div>
		</main><!-- End .main -->

		
@endsection