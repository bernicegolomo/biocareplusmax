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
									Manage Members Wallets
								</li>
							</ol>
						</div>
					</nav>

					<h1>Earnings | Debit</h1>
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

								<a href="{{url('/admin/creditmember')}}" class="btn btn-primary btn-sm">Credit Member</a>
								<a href="{{url('/admin/debitmember')}}" class="btn btn-primary btn-sm">Debit Member</a>
								<a class="btn btn-danger btn-sm text-white" data-toggle="modal" data-target="#exampleModal">Quick Search</a>
                                <hr class="mt-0 mb-3 pb-2" />
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
                                

								<div class="mb-4"></div>
                                <div class="row row-lg">
									<div class="col-6 col-md-3">
										<div class="feature-box text-center pb-4" style="border: 2px solid #08C;">
											<div class="feature-box-content">
                                                <h3 class="text-dark">CASH</h3>
												<h5 class="text-primary">Earnings : <em class="text-danger">Debit : </em><em class="text-success">Balance</em></h5>
												<h5>

                                                    <em class="text-primary">{{ number_format($totalEarningCash)  }}  :</em>
                                                    <em class="text-danger">{{ number_format($totalDebitCash) }}  :</em> 
                                                    <em class="text-success">{{ number_format($totalEarningCash - $totalDebitCash)}} </em> 
                                                </h5>
											</div>
										</div>
									</div>

                                    <div class="col-6 col-md-3">
										<div class="feature-box text-center pb-4" style="border: 2px solid #dc3545;">
											<div class="feature-box-content">
                                                <h3 class="text-dark">VOUCHER</h3>
												<h5 class="text-primary">Earnings : <em class="text-danger">Debit : </em><em class="text-success">Balance</em></h5>
												<h5>

                                                    <em class="text-primary">{{ number_format($totalEarningVoucher)  }}  :</em>
                                                    <em class="text-danger">{{ number_format($totalDebitVoucher) }}  :</em> 
                                                    <em class="text-success">{{ number_format($totalEarningVoucher - $totalDebitVoucher)}} </em> 
                                                </h5>
											</div>
										</div>
									</div>

                                    <div class="col-6 col-md-3">
										<div class="feature-box text-center pb-4" style="border: 2px solid #17a2b8;">
											<div class="feature-box-content">
                                                <h3 class="text-dark">FREE VOUCHER</h3>
												<h5 class="text-primary">Earnings : <em class="text-danger">Debit : </em><em class="text-success">Balance</em></h5>
												<h5>
                                                    <em class="text-primary">{{ number_format($totalEarningFreeVoucher)  }}  :</em>
                                                    <em class="text-danger">{{ number_format($totalDebitFreeVoucher) }}  :</em> 
                                                    <em class="text-success">{{ number_format($totalEarningFreeVoucher - $totalDebitFreeVoucher)}} </em> 
                                                </h5>
											</div>
										</div>
									</div>

                                    <div class="col-6 col-md-3">
										<div class="feature-box text-center pb-4" style="border: 2px solid #17a2b8;">
											<div class="feature-box-content">
                                                <h3 class="text-dark">POINTS</h3>
												<h5 class="text-primary">Left : <em class="text-danger">Right : </em><em class="text-success">Total</em></h5>
												<h5>
                                                    <em class="text-primary">{{ $totalLeftPoint }}  :</em>
                                                    <em class="text-danger">{{ $totalRightPoint }}  :</em> 
                                                    <em class="text-success">{{ $totalLeftPoint + $totalRightPoint}} </em> 
                                                </h5>
											</div>
										</div>
									</div>
                                </div>


								<div class="row row-lg">
									<div class="col-12 col-md-12">
                                        @if(isset($wallets) && count($wallets) > 0)

                                            <form action="{{ url('delete-multiple-wallets') }}" method="POST">
                                                @csrf
                                                
                                                
                                                <button type="submit" class="btn btn-danger mt-3 btn-small mb-3">Delete Selected</button>
                                                <table class="table table-bordered text-center p-2">
                                                    <thead>
                                                        <tr>
                                                            <th><input type="checkbox" id="select-all"></th>
                                                            <th class="order-id">S/NO</th>
                                                            <th class="order-id">MEMBER</th>
                                                            <th class="order-price">TRANSACTION TYPE</th>
                                                            <th class="order-date">WALLET TYPE</th>
                                                            <th class="order-date">VALUE</th>
                                                            <th class="order-date">DESCRIPTION</th>
                                                            <th class="order-status">DATE</th>
                                                            <th class="order-status">ACTION</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $x = 0; @endphp
                                                        @foreach($wallets as $wallet)
                                                            @php $x++; @endphp
                                                            <tr>
                                                                <td>
                                                                    <input type="checkbox" name="wallets[]" value="{{ $wallet->id }}:{{ $wallet->transaction_type }}" class="select-checkbox">
                                                                </td>
                                                                <td class="p-2">{{ $x }}</td>
                                                                <td class="p-2">
                                                                    @php $member = App\Http\Controllers\MemberController::memberdetails($wallet->member_id); @endphp
                                                                    @if(isset($member) && !empty($member))
                                                                        {{ $member->username }}
                                                                    @endif
                                                                </td>
                                                                <td class="p-2 text-center">{{ strtoupper($wallet->transaction_type) }}</td>
                                                                <td class="p-2 text-center">{{ $wallet->type }}</td>
                                                                <td class="p-2 text-center">
                                                                    @if($wallet->type == "Cash" || $wallet->type == "Voucher")
                                                                        {{ number_format($wallet->value) }}
                                                                    @else
                                                                        {{ $wallet->value }}
                                                                    @endif
                                                                </td>
                                                                <td class="p-2 text-center">{{ $wallet->description }}</td>
                                                                <td class="p-2 text-center">{{ $wallet->created_at }}</td>
                                                                <td class="p-2 text-center">
                                                                    <a href="{{ url('deletewallet', [Illuminate\Support\Facades\Crypt::encrypt($wallet->id), Illuminate\Support\Facades\Crypt::encrypt($wallet->transaction_type)]) }}" class="btn btn-danger btn-sm text-white">Delete</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            
                                            </form>



                                        @else
                                        <p class="text-center text-danger"> No product found!</p>
                                        @endif
									</div>

                                    <div class="mt-5">
                                        {{ $wallets->appends(request()->all())->links('pagination::bootstrap-5') }}
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
                <form  action="{{url('/admin/earnings')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Wallet Type </label>
                                    <select name="wallet" class="form-control">
                                        <option value="" selected=""></option>
                                        <option value="Points">Points</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Voucher">Voucher</option>
                                        <option value="Free Voucher">Free Voucher</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Transaction Type </label>
                                    <select name="type" class="form-control">
                                        <option value=""  selected=""></option>
                                        <option value="Earning">Earnings</option>
                                        <option value="Debit">Debit</option>
                                    </select> 
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Transaction Date </label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="date" class="form-control" name="from"/>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <input type="date" class="form-control" name="to"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                            

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Enter Username </label>
                                    <input type="text" class="form-control" name="username"/>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Enter Keywords </label>
                                    <input type="text" class="form-control" name="keywords"/>
                                </div>
                            </div>
                        </div>


                    
										
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="search">Search</button>
                        <button type="submit" class="btn btn-danger" name="export" value="1">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




@endsection