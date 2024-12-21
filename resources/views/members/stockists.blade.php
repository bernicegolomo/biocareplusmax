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
									Stockist Dashboard
								</li>
							</ol>
						</div>
					</nav>

					<h1>Stockist Dashboard</h1>
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
    										<div class="feature-box text-center pb-4  bg-primary">
    											<a href="https://biocaremaxplus.com/stockistBackOffice" class="link-to-tab"><i class="sicon-social-dropbox"></i></a>
    											<div class="feature-box-content">
    												<h3>TOTAL ORDERS</h3>
    												<h3 class="text-white">{{$totalOrdersCount}}</h3><br>
    											</div>
    										</div>
    									</div>
    
    									<div class="col-6 col-md-3">
    										<div class="feature-box text-center pb-4 bg-danger">
    											<a href="https://biocaremaxplus.com/stockistBackOffice" class="link-to-tab"><i class="sicon-cloud-download"></i></a>
    											<div class=" feature-box-content">
    												<h3>PENDING ORDERS</h3>
    												<h3 class="text-white">{{$totalPendingOrdersCount}}</h3><br>
    											</div>
    										</div>
    									</div>
    
    									<div class="col-6 col-md-3">
    										<div class="feature-box text-center pb-4 bg-info">
    											<a href="https://biocaremaxplus.com/stockistBackOffice" class="link-to-tab"><i class="sicon-briefcase text-white"></i></a>
    											<div class="feature-box-content">
    												<h3>STOCKIST PACKAGE</h3>
    												<h3 class="text-white">{{strtoupper($packagetype->name)}}</h3><br>
    											</div>
    										</div>
    									</div>
    
    
    									<div class="col-6 col-md-3">
    										<div class="feature-box text-center pb-4 bg-warning">
    											<a href="https://biocaremaxplus.com/stockistBackOffice"><i class="sicon-credit-card text-dark"></i></a>
    											<div class="feature-box-content">
    												<h3>STOCKIST WALLET</h3>
    												<h3 class="text-white">{{$user->getBalance("Stockist Voucher")}} Voucher</h3>
    												<h3 class="text-white">{{$user->getBalance("Stockist Free Voucher")}} Free Voucher</h3>
    											</div>
    										</div>
    									</div>
    								</div>

                                    <div class="row row-lg">
                                        <div class="col-12 col-md-12">
                                            @if(isset($orders) && count($orders) > 0)

                                                <table class="table table-bordered text-center p-2">
                                                    <thead>
                                                        <tr>
                                                            <th class="order-id">S/NO</th>
                                                            <th class="order-date">MEMBER</th>
                                                            <th class="order-date">STORE</th>
                                                            <th class="order-date">ITEMS</th>
                                                            <th class="order-date">TOTAL</th>
                                                            <th class="order-date">STATUS</th>
                                                            <th class="order-date">ACTION</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $x = 0; @endphp
                                                        @foreach($orders as $order)
                                                            @php $x++; @endphp
                                                            <tr>
                                                                <td class="p-2" style="width: 80px;">
                                                                    <p class=" mt-2">
                                                                        <span>{{$x}}</span>
                                                                    </p>
                                                                </td>
                                                                
                                                                <td class="p-2 text-center">
                                                                    <p class="mt-2">
                                                                        @php $member = App\Http\Controllers\MemberController::memberdetails($order->memberid); @endphp
                                                                        @if(isset($member) && !empty($member))
                                                                            {{$member->name}} </br>
                                                                            {{$member->username}}
                                                                        @endif   
                                                                    </p>
                                                                </td>
                                                                
                                                                <td class="p-2 text-center">
                                                                    <p class="mt-2">
                                                                        @php $store = App\Http\Controllers\MemberController::storedetails($order->store); @endphp
                                                                        @if(isset($store) && !empty($store))
                                                                            {{$store->name}}
                                                                            
                                                                        @endif   
                                                                    </p>
                                                                </td>
                                                                
                                                                <td class="p-2 text-center">
                                                                    <p class="mt-2">
                                                                    @php $items = json_decode($order->items, true); @endphp

                                                                    <ul>
                                                                        @foreach($items as $item)
                                                                            <li>
                                                                                Name: {{ $item['name'] }} <br>
                                                                                Quantity: {{ $item['quantity'] }} <br>
                                                                                Price: {{ number_format($item['price']) }}
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>

                                                                    </p>
                                                                </td>
                                                                <td class="p-2 text-center">
                                                                    <p class="mt-2"> @php //dd($orders); @endphp
                                                                        {{number_format($order->total)}} 
                                                                    </p>
                                                                </td>

                                                                <td class="p-2 text-center">
                                                                    <p class="mt-2">
                                                                        @if($order->order_status == 1)
                                                                            <label class="badge badge-primary text-white mt-2 text-center"> Payment Successful</label>
                                                                        @elseif($order->order_status == 2)
                                                                            <label class="badge badge-secondary text-white mt-2 text-center"> Order Processing</label>
                                                                        @elseif($order->order_status == 3)
                                                                            <label class="badge badge-success text-white mt-2 text-center"> Order Completed</label>
                                                                        @endif
                                                                    </p>
                                                                </td>

                                                                <td class="p-2" style="width: 80px;">
                                                                    <p class="mt-2">
                                                                        <div class="dropdown-primary dropdown open">
                                                                            <button class="btn btn-primary dropdown-toggle waves-effect waves-light btn-sm" type="button" id="dropdown-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Action</button>
                                                                            <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                                                @if($order->order_status == 1)
                                                                                    <a class="dropdown-item waves-light waves-effect" href="{{url('stockistprocessorder', Illuminate\Support\Facades\Crypt::encrypt($order->order_id))}}">Process Order</a>
                                                                                @elseif($order->order_status == 2)
                                                                                    <a class="dropdown-item waves-light waves-effect" href="{{url('stockistconfirmorder', Illuminate\Support\Facades\Crypt::encrypt($order->order_id))}}">Complete Order</a>
                                                                                @endif
                                                                            </div>
                                                                        </div>      
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                


                                            @else
                                            <p class="text-center text-danger"> No order found!</p>
                                            @endif
                                        </div>

                                        <div class="mt-5">
                                            @if(isset($query))
                                                {{ $orders->appends($query)->links('pagination::bootstrap-5') }}
                                            @else
                                                {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}
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



@endsection