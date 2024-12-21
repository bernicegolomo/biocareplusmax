@extends('admin.layout')

@section('content') 

        <main class="main">
			<div class="page-header">
				<div class="container d-flex flex-column align-items-center">
					<nav aria-label="breadcrumb" class="breadcrumb-nav">
						<div class="container">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{url('/admin/dashboard')}}">Home</a></li>
								<li class="breadcrumb-item"><a href="{{url('/admin/ranks')}}">Ranks</a></li>
								<li class="breadcrumb-item active" aria-current="page">
									Add new rank
								</li>
							</ol>
						</div>
					</nav>

					<h1>Add Rank</h1>
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
                                        @if(isset($data) && !empty($data))
                                            <form  action="{{url('updaterank')}}" method="post" enctype="multipart/form-data">
                                        @else
                                            <form  action="{{url('createrank')}}" method="post" enctype="multipart/form-data">
                                        @endif
                                            @csrf

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Rank Name <span class="required">*</span></label>
                                                        <input type="text" class="form-control" name="name" @if(isset($data) && !empty($data)) value="{{$data->rank}}" @endif required />
                                                    </div>
                                                </div>
                                                
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Left Points Threshold </label>
                                                        <input type="text" class="form-control" name="left" @if(isset($data) && !empty($data)) value="{{$data->left_points_threshold}}" @endif/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Right  Points Threshold </label>
                                                        <input type="text" class="form-control" name="right" @if(isset($data) && !empty($data)) value="{{$data->right_points_threshold}}" @endif  />
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label> PV Requirement </label>
                                                        <input type="text" class="form-control" name="pv" @if(isset($data) && !empty($data)) value="{{$data->pv_requirement}}" @endif />
                                                    </div>
                                                </div>
                                            </div>

                                            @if(isset($data) && !empty($data))
                                                <input type="hidden" class="form-control" name="id" value="{{$data->id}}" />
                                            @endif

                                            <hr class="mt-2 mb-3 pb-2" />

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Level From </label>
                                                        <input type="text" class="form-control" name="from" @if(isset($data) && !empty($data->level_from)) value="{{$data->level_from}}" @endif/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Level To </label>
                                                        <input type="text" class="form-control" name="to" @if(isset($data) && !empty($data->level_to)) value="{{$data->level_to}}" @endif/>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">

                                                <div class="col-md-6">

                                                    <div class="form-footer mb-0">
                                                        <div class="form-footer-right">
                                                            <button type="submit" name="submit" value="1" class="btn btn-dark btn-sm py-4">
                                                                Submit
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