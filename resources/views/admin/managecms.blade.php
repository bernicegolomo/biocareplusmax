@extends('admin.layout')

@section('content') 

        <main class="main">
			<div class="page-header">
				<div class="container d-flex flex-column align-items-center">
					<nav aria-label="breadcrumb" class="breadcrumb-nav">
						<div class="container">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{url('/admin/dashboard')}}">Home</a></li>
								<li class="breadcrumb-item"><a href="{{url('/admin/cms')}}">CMS</a></li>
								<li class="breadcrumb-item active" aria-current="page">
									Update CMS
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
                                        

                                        <div class="col-12 col-md-12 col-xs-12">
                                            @if(isset($content))
                                                <form  action="{{url('updatecms')}}" method="post" enctype="multipart/form-data">
                                            @else
                                                <form  action="{{url('createcms')}}" method="post" enctype="multipart/form-data">
                                            @endif
                                                @csrf
            
                                                <div class="row">
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="row">
                                                            
                                                            <div class="col-md-12 col-xs-12 mt-3">
                                                                <label class="form-label">Page <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-input form-wide" name="name" @if(isset($content)) value="{{$content->name}}" @endif required="">
                                                            </div>
                                                            
                                                            @if(isset($content))
                                                                <input type="hidden" class="form-control form-input form-wide" name="id" value="{{$content->id}}" required="">
                                                            @endif
                                                                    
                                                            <div class="col-md-12 col-xs-12 mt-3">
                                                                <label class="form-label">Content <span class="text-danger">*</span></label>
                                                                <textarea class="form-control form-input form-wide" id="editor1" name="content" required="" style="height:200px;"> @if(isset($content)) {{$content->content}} @endif</textarea>
                                                            </div>
            
                                                            <div class="col-md-10 col-xs-12 mt-3">
                                                                <label class="form-label">Image </label>
                                                                <input type="file" class="form-control" name="image">
                                                            </div>
                                                            
                                                            @if(isset($content) && !empty($content->image))
                                                                <div class="col-md-2 col-xs-12 mt-3">
                                                                     <img class="img-thumbnail avatar-sm" src="{{ URL::asset('img/'.$content->image) }}" data-holder-rendered="true">
                                                                </div>
                                                            @endif
                                                            
                                                        </div>
            
                                        
            
                                                        <div class="row mt-5">
            
                                                            <div class="col-md-12 col-xs-12">
                                                                <button type="submit" name="update" value="1" class="btn btn-dark btn-md w-100">
                                                                    @if(isset($content))
                                                                        UPDATE CONTENT
                                                                    @else
                                                                        NEW CONTENT
                                                                    @endif
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