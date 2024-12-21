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
									Password Reset
								</li>
							</ol>
						</div>
					</nav>

					<h1>Password Reset</h1>
				</div>
			</div>

			<div class="container login-container">
				<div class="row">
					<div class="col-lg-12 mx-auto">

                        <div class="row">
                            <div class="col-xs-12 col-md-12">
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

						<div class="row">
							<div class="col-md-6">

								<form  action="{{url('passwordreset')}}" method="post" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">

                                        <div class="col-md-12 col-xs-12">
                                            <label for="login-email">
                                                Username
                                                <span class="required">*</span>
                                            </label>
                                            <input type="text" class="form-input form-wide" name="username"  required />
                                        </div>

                                    </div>

									<div class="form-footer">
										<div class="custom-control custom-checkbox mb-0">
										</div>

                                        <a href="{{url('/login')}}"
											class="forget-password text-dark form-footer-right">Back To Login</a>
									</div>
									<button type="submit" class="btn btn-dark btn-md w-100">
										RESET PASSWORD
									</button>

									<div class="form-footer">
										<label class="mb-0 text-danger" for="lost-password">Don't Have An Account? <a href="{{url('selectpackage')}}"><strong>Register Now!</strong></a></label>
										
									</div>
								</form>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</main><!-- End .main -->

		
		@endsection