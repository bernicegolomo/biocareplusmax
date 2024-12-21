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
									Withdrawals
								</li>
							</ol>
						</div>
					</nav>

					<h1>My Wallet</h1>
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
								
                                <div class="row row-lg">
									<div class="col-6 col-md-3">
										<div class="feature-box text-center pb-4" style="border: 2px solid #08C;">
											<div class="feature-box-content">
                                                <h3 class="text-dark">Total</h3>
												<h5>{{$totalValue}}</h5>
											</div>
										</div>
									</div>

                                    
                                </div>

                                <a href="{{url('/requestwithdrawal')}}" class="btn btn-primary btn-sm mt-5">Withdrawal Request</a>
								<a class="btn btn-danger btn-sm text-white mt-5" data-toggle="modal" data-target="#exampleModal">Quick Search</a>
                                <hr class="mt-0 mb-3 pb-2" />
                                <p class="text-danger text-center"><em>To submit a withdrawal request, you need to complete your first binary level and update your bank account details in your profile settings.</em></p>

								<div class="row row-lg">
									<div class="col-12 col-md-12">
                                        @if(isset($wallets) && count($wallets) > 0)

                                            <table class="table table-bordered text-center p-2">
                                                <thead>
                                                    <tr>
                                                        <th class="order-id">S/NO</th>
                                                        <th class="order-date">VALUE</th>
                                                        <th class="order-date">CHARGES</th>
                                                        <th class="order-date">DESCRIPTION</th>
                                                        <th class="order-status">DATE</th>
                                                        <th class="order-status">STATUS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $x = 0; @endphp
                                                    @foreach($wallets as $wallet)
                                                        @php $x++; @endphp
                                                        <tr>
                                                            <td class="p-2">
                                                                <p class=" mt-2">
                                                                    <span>{{$x}}</span>
                                                                </p>
                                                            </td>

                                                            <td class="p-2 text-center">
                                                                <p class=" mt-2">
                                                                    {{number_format($wallet->value,2)}}
                                                                </p>
                                                            </td>
                                                            @php $info = App\Http\Controllers\MemberController::withdrawdetails($wallet->transaction_id,$user->id);  //dd($info[1]); @endphp
                                                            
                                                            
                                                            <td class="p-2 text-center">
                                                                <p class=" mt-2">
                                                                    @if(isset($info) && isset($info[1]) && !empty($info[1]))
                                                                    {{number_format($info[1],2)}}
                                                                    @endif
                                                                </p>
                                                            </td>
                                                            
                                                            <td class="p-2 text-center">
                                                                <p class=" mt-2">
                                                                    {{$wallet->description}}
                                                                </p>
                                                            </td>

                                                            <td class="p-2 text-center">
                                                                <p class=" mt-2">
                                                                    {{$wallet->created_at}}
                                                                </p>
                                                            </td>

                                                            <td class="p-2 text-center">
                                                                @if($wallet->processed == 1 && !empty($wallet->processed))
                                                                    <span class="badge badge-success">Processed</span>
                                                                @else
                                                                    <span class="badge badge-warning">Pending</span>
                                                                @endif
                                                            </td>
                                                            
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            


                                        @else
                                        <p class="text-center text-danger"> No request found!</p>
                                        @endif
									</div>

                                    <div class="mt-5">
                                        @if(isset($query))
                                            {{ $wallets->appends($query)->links('pagination::bootstrap-5') }}
                                        @else
                                            {{ $wallets->withQueryString()->links('pagination::bootstrap-5') }}
                                        @endif
                                    </div>

									

								</div><!-- End .row -->

								
								
							</div>
						</div><!-- End .tab-pane -->

						
						
					</div><!-- End .tab-content -->
				</div><!-- End .row -->
			</div><!-- End .container -->

			<div class="mb-5"></div><!-- margin -->
		</main><!-- End .main -->
		
		
		<!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Quick Search (Members Earnings)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form  action="{{url('/withdrawals')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Transaction Date </label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="date" class="form-control" name="from" required/>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <input type="date" class="form-control" name="to" required/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                 
                        <input type="hidden" class="form-control" value="{{$user->username}}" name="username"/>

                    
										
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




@endsection