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
									Contact Us
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

                        
                            <div class="container cta">
                                <div class="mt-6 mb-8">
                                    <div class="row">
                                        <div class="col-lg-8 mb-2">
                                            <h3 class="text-center mb-5">Send us a message!</h3>
                                            <form  action="{{url('sendmessage')}}" method="post" enctype="multipart/form-data">
                                                @csrf
            
                                                <div class="row">
            
                                                    <div class="col-md-12 col-xs-12">
                                                        <label for="login-email">
                                                            FULLNAME
                                                            <span class="required">*</span>
                                                        </label>
                                                        <input type="text" class="form-input form-wide" name="name"  required />
                                                    </div>
                                                    
                                                    <div class="col-md-12 col-xs-12">
                                                        <label for="login-email">
                                                            EMAIL
                                                            <span class="required">*</span>
                                                        </label>
                                                        <input type="email" class="form-input form-wide" name="email"  required />
                                                    </div>
                                                    
                                                    <div class="col-md-12 col-xs-12">
                                                        <label for="login-email">
                                                            PHONE NUMBER
                                                            <span class="required">*</span>
                                                        </label>
                                                        <input type="tel" class="form-input form-wide" name="phone" required/>
                                                    </div>
            
                                                    
                                                    <div class="col-md-12 col-xs-12">
                                                        <label for="login-password">
                                                            SUBJECT
                                                            <span class="required">*</span>
                                                        </label>
                                                        <input type="text" name="subject" class="form-input form-wide" id="login-password" required />
                                                    </div>
                                                    
                                                    <div class="col-md-12 col-xs-12">
                                                        <label for="login-password">
                                                            MESSAGE
                                                            <span class="required">*</span>
                                                        </label>
                                                        <textarea name="message" required class="form-input form-wide"></textarea>
                                                    </div>
                                                    
                                                   
                                                </div>
            									<button type="submit" class="btn btn-dark btn-md w-100">
            										SEND MESSAGE
            									</button>
        
            								</form>
                                        </div>
                                        
                                        <div class="col-lg-4 mb-2 mt-10">
                                            @if(!empty($settings[4]->content) OR !empty($settings[5]->content))
                                                <div class="cta-simple cta-border bg-primary text-white" style="word-wrap: break-word; overflow-wrap: break-word;">
                                                    <h3 class="font-weight-normal text-dark"> <b><i class="icon-envelop"></i> Email:</b> </h3>
                                                    <p class="text-white "> {{ $settings[4]->content }} </p>
                                                    <p class="text-white"> {{ $settings[5]->content }} </p>
                                                </div>
                                            @endif
                                            
                                            @if(!empty($settings[2]->content) OR !empty($settings[3]->content))
                                                <div class="cta-simple cta-border bg-danger text-white mt-3" style="word-wrap: break-word; overflow-wrap: break-word;">
                                                    <h3 class="font-weight-normal text-dark"> <b><i class="icon-mobile"></i> Phone:</b> </h3>
                                                    <p class="text-white "> {{ $settings[2]->content }} </p>
                                                    <p class="text-white"> {{ $settings[3]->content }} </p>
                                                </div>
                                            @endif
                                            
                                            @if(!empty($settings[6]->content))
                                                <div class="cta-simple cta-border bg-secondary text-white mt-3" style="word-wrap: break-word; overflow-wrap: break-word;">
                                                    <h3 class="font-weight-normal text-dark"> <b><i class="icon-map"></i> Address:</b> </h3>
                                                    <p class="text-white "> {{ $settings[6]->content }} </p>
                                                </div>
                                            @endif
                                            
                                            
                                            <span class="separator"></span>

                    						<div class="social-icons mt-5">
                    						    @if(!empty($settings[7]->content))
                    							<a href="{{$settings[7]->content}}" class="social-icon social-facebook icon-facebook" target="_blank"></a>
                    							@endif
                    							
                    							@if(!empty($settings[8]->content))
                    							<a href="{{$settings[8]->content}}" class="social-icon social-twitter icon-twitter" target="_blank"></a>
                    							@endif
                    							
                    							@if(!empty($settings[9]->content))
                    							<a href="{{$settings[9]->content}}" class="social-icon social-linkedin icon-linkedin" target="_blank"><i class="fab fa-linkedin"></i></a>
                    							@endif
                    							
                    							@if(!empty($settings[10]->content))
                    							<a href="{{$settings[10]->content}}" class="social-icon social-tiktok icon-tiktok" target="_blank"><i class="fab fa-tiktok"></i></a>
                    							@endif
                    							
                    							@if(!empty($settings[11]->content))
                    							<a href="{{$settings[11]->content}}" class="social-icon social-instagram icon-instagram" target="_blank"></a>
                    							@endif
                    							
                    						</div><!-- End .social-icons -->
                                        </div>
                                        
                                        
                                        
                                        
                            
                                       
                                        
                                        
                                    </div>
                                </div>
                            </div><!-- End .container -->
                       

					</div>
				</div>
			</div>
		</main><!-- End .main -->

		
@endsection