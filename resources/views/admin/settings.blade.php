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
									General Settings
								</li>
							</ol>
						</div>
					</nav>

					<h1>Settings</h1>
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
                                        <form  action="{{url('updatesettings')}}" method="post" enctype="multipart/form-data">
                                            @csrf

                                            <div class="row">

                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label>Website Logo </label>
                                                        <input type="file" class="form-control" name="image"/>
                                                    </div>
                                                </div>

                                                @if(isset($data) && !empty($data[1]->content)) 
                                                    <div class="col-md-4 unit">
                                                        <img class="img-fluid img-radius" src="{{asset('front/assets/images/'.$data[1]->content)}}" style="height:80px;">
                                                    </div>
                                                @endif
                                                
                                            </div>


                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>Phone Number 1</strong></label>
                                                        <input type="text" name="phone1" class="form-control"  placeholder="Phone Number" @if(isset($data) && !empty($data[2]->content)) value="{{$data[2]->content}}" @endif onkeypress="return onlyNumberKey(event)" maxlength="15">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>Phone Number 2</strong></label>
                                                        <input type="text" name="phone2" class="form-control"  placeholder="Phone Number" @if(isset($data) && !empty($data[3]->content)) value="{{$data[3]->content}}" @endif onkeypress="return onlyNumberKey(event)" maxlength="15">
                                                    </div>
                                                </div>
                                            </div>

                                            

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>Email 1</strong></label>
                                                        <input type="text" name="email1" class="form-control" placeholder="Email" @if(isset($data) && !empty($data[4]->content)) value="{{$data[4]->content}}" @endif>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>Email 2</strong></label>
                                                        <input type="text" name="email2" class="form-control" placeholder="Email" @if(isset($data) && !empty($data[5]->content)) value="{{$data[5]->content}}" @endif>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label><strong>Address</strong></label>
                                                        <input type="text" name="address" class="form-control" placeholder="Address" @if(isset($data) && !empty($data[6]->content)) value="{{$data[6]->content}}" @endif>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    
                                                    <h4 class="text-center text-danger mt-3 mb-3" >Social Media Settings</h4>
                                                    
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>Facebook Link</strong></label>
                                                        <input type="text" name="fb" class="form-control"  placeholder="Facebook Link" @if(isset($data) && !empty($data[7]->content)) value="{{$data[7]->content}}" @endif>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>Twitter Link</strong></label>
                                                        <input type="text" name="tw" class="form-control" placeholder="Twitter Link" @if(isset($data) && !empty($data[8]->content)) value="{{$data[8]->content}}" @endif>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>LinkedIn Link</strong></label>
                                                        <input type="text" name="li" class="form-control" placeholder="LinkedIn Link" @if(isset($data) && !empty($data[9]->content)) value="{{$data[9]->content}}" @endif>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>Tiktok Link</strong></label>
                                                        <input type="text" name="yt" class="form-control" placeholder="Youtube Link" @if(isset($data) && !empty($data[10]->content)) value="{{$data[10]->content}}" @endif>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label><strong>Instagram Link</strong></label>
                                                        <input type="text" name="in" class="form-control"  placeholder="Instagram Link" @if(isset($data) && !empty($data[11]->content)) value="{{$data[11]->content}}" @endif>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-12">
                                                    
                                                    <h4 class="text-center text-danger mt-3 mb-3" >Other Bounses</h4>
                                                    
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>Discount Referrer Bonus</strong></label>
                                                        <input type="text" name="refBonus" class="form-control" placeholder="Discount Referrer Bonus" @if(isset($data) && !empty($data[12]->content)) value="{{$data[12]->content}}" @endif>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>Personal Discount Bonus</strong></label>
                                                        <input type="text" name="pbonus" class="form-control" placeholder="Personal Discount Bonus" @if(isset($data) && !empty($data[13]->content)) value="{{$data[13]->content}}" @endif>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>10% instant cash back</strong></label>
                                                        <input type="text" name="Bonus10" class="form-control" placeholder="10% instant cash back" @if(isset($data) && !empty($data[14]->content)) value="{{$data[14]->content}}" @endif>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>5% instant cash back</strong></label>
                                                        <input type="text" name="bonus5" class="form-control" placeholder="5% instant cash back" @if(isset($data) && !empty($data[15]->content)) value="{{$data[15]->content}}" @endif>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>Direct Referral Bonus</strong></label>
                                                        <input type="text" name="dBonus" class="form-control" placeholder="Direct Referral Bonus" @if(isset($data) && !empty($data[16]->content)) value="{{$data[16]->content}}" @endif>
                                                    </div>
                                                </div>
                                            </div>


                                            

                                            <div class="row">

                                                <div class="col-md-8">

                                                    <div class="form-footer mb-0">
                                                        <div class="form-footer-right">
                                                            <button type="submit" name="submit" value="1" class="btn btn-dark py-4">
                                                                Update Settings
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