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
									Content Management
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
                                

                        <a href="{{url('/addcms')}}" class="btn btn-primary btn-sm">Add Announcements</a>
                        <a href="{{url('/sendemails')}}" class="btn btn-danger btn-sm">Send Emails</a>
                        <hr class="mt-0 mb-3 pb-2" />

						<div class="row">
							<div class="col-md-12">

								<table class="table table-bordered text-center p-2">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Page|Content Title</th>
                                            <th>Content</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($cms) && count($cms))
                                            @php  $x = 0; @endphp
                                            @foreach($cms as $content)
                                                @php $x++; @endphp
                                                <tr>
                                                    <td>
                                                        @if(!empty($content->image))
                                                            <img class="img-thumbnail avatar-sm" src="{{ URL::asset('front/assets/images/'.$content->image) }}" data-holder-rendered="true" style="width: 50px; height: 50px; object-fit: cover;">
                                                        @endif
                                                    </td>
                                                    <td><a href="" class="text-dark"> {{$content->name}}</a></td>
                                                    <td>{!! $content->content !!}</td>
                                                    <td class="p-2" style="width: 80px;">
                                                        
                                                            <div class="dropdown-primary dropdown open">
                                                                <button class="btn btn-primary dropdown-toggle waves-effect waves-light btn-sm" type="button" id="dropdown-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Action</button>
                                                                <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                                    <a class="dropdown-item edit-list"  href="{{url('/editcms', Illuminate\Support\Facades\Crypt::encrypt($content->id))}}">
                                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                                    </a>
                                                                </div>
                                                            </div>      
                                                       
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                                                            
                                    </tbody>
                                </table>
							</div>

                            
							
						</div>
					</div>
				</div>
			</div>
		</main><!-- End .main -->

		
@endsection