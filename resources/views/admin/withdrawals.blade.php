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
									Withdrawal
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
                                <a class="btn btn-danger btn-sm text-white mt-5" data-toggle="modal" data-target="#exampleModal">Quick Search</a>
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
                                

								<div class="row row-lg">
									<div class="col-12 col-md-12">
                                        @if(isset($wallets) && count($wallets) > 0)

                                            <form action="{{ url('processrequest') }}" method="POST">
                                                @csrf
                                                
                                                <button type="submit" class="btn btn-primary mt-3 btn-small mb-3">Process Request</button>
                                                <table class="table table-bordered text-center p-2">
                                                    <thead>
                                                        <tr>
                                                            <th><input type="checkbox" id="select-all"></th>
                                                            <th class="order-id">S/NO</th>
                                                            <th class="order-id">USERNAME</th>
                                                            <th class="order-date">VALUE</th>
                                                            <th class="order-date">CHARGES</th>
                                                            <th class="order-date">DESCRIPTION</th>
                                                            <th class="order-status">DATE</th>
                                                            <th class="order-status">STATUS</th>
                                                            <th class="order-status">ACTION</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $x = 0; @endphp
                                                        @foreach($wallets as $wallet)
                                                            @php $x++; @endphp
                                                            <tr>
                                                                <td>
                                                                    <input type="checkbox" name="wallets[]" value="{{ $wallet->id }}:{{ $wallet->transaction_id }}" class="select-checkbox">
                                                                </td>
                                                                
                                                                <td class="p-2">
                                                                    <span>{{$x}} {{$wallet->id}}</span>
                                                                </td>
    
                                                                <td class="p-2">
                                                                    @php $member = App\Http\Controllers\MemberController::memberdetails($wallet->member_id); @endphp
                                                                    @if(isset($member) && !empty($member))
                                                                        {{ $member->username }}
                                                                    @endif
                                                                </td>
                                                                    
                                                                <td class="p-2 text-center">
                                                                    {{number_format($wallet->value,2)}}
                                                                </td>
                                                                @php $info = App\Http\Controllers\MemberController::withdrawdetails($wallet->transaction_id,$user->id);  @endphp
                                                                
                                                                
                                                                <td class="p-2 text-center">
                                                                    @if(!empty($info[1]) && ($info[1] > 1))
                                                                        {{number_format($info[1],2)}}
                                                                    @endif
                                                                </td>
                                                                
                                                                <td class="p-2 text-center">
                                                                    {{$wallet->description}}
                                                                </td>
    
                                                                <td class="p-2 text-center">
                                                                    {{$wallet->created_at}}
                                                                </td>
                                                                
                                                                <td class="p-2 text-center">
                                                                    @if($wallet->processed == 1 && !empty($wallet->processed))
                                                                        <span class="badge badge-success">Processed</span>
                                                                    @else
                                                                        <span class="badge badge-warning">Pending</span>
                                                                    @endif
                                                                </td>
    
                                                                <td class="p-2" style="width: 80px;">
                                                                    <div class="dropdown-primary dropdown open">
                                                                        <button class="btn btn-primary dropdown-toggle waves-effect waves-light btn-sm" type="button" id="dropdown-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Action</button>
                                                                        <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                                            <div class="dropdown-divider"></div>
                                                                                <a class="dropdown-item waves-light waves-effect" href="{{url('deletewithdrawal', Illuminate\Support\Facades\Crypt::encrypt($wallet->id))}}" onclick="return confirm(' Are you sure you want to delete this member? Deleting this will affect all records linked to this member.');">Delete</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>    
                                                                </td>
                                                                
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </form>
                                            


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
                    <form  action="{{url('/admin/withdrawals')}}" method="post" enctype="multipart/form-data">
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
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Enter Username </label>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="text" class="form-control" name="username" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
    										
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    


@endsection