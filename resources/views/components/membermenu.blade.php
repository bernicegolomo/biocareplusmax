						<ul class="nav nav-tabs list flex-column mb-0 menucss" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" href="{{url('/dashboard')}}"
									role="tab" aria-controls="dashboard" aria-selected="true">Dashboard</a>
							</li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{url('/mydownlines')}}" role="tab"
                                    aria-controls="download" aria-selected="false">Downlines</a>
                            </li>

							<li class="nav-item">
								<a class="nav-link" href="{{url('/stores')}}" role="tab"
									aria-controls="download" aria-selected="false">Store</a>
							</li>
							@php 
							    $Luser = Auth::guard('web')->user(); 
							    $LisStokist = App\Http\Controllers\MemberController::isStockist($Luser->id);
							@endphp
							@if($LisStokist)
							    <li class="nav-item">
    								<a class="nav-link" href="{{url('/stockistBackOffice')}}" role="tab"
    									aria-controls="download" aria-selected="false">Stockist Back Office</a>
    							</li>
                    		@endif

							<li class="nav-item">
								<a class="nav-link" href="{{url('/myorders')}}" role="tab"
									aria-controls="order" aria-selected="true">Orders</a>
							</li>
							
							<li class="nav-item">
								<a class="nav-link" href="{{url('/wallets')}}">My Wallet</a>
							</li>

							<li class="nav-item">
								<a class="nav-link" id="address-tab" href="{{url('/withdrawals')}}" role="tab"
									aria-controls="address" aria-selected="false">Withdrawals</a>
							</li>

                            <!--
							<li class="nav-item">
								<a class="nav-link" href="{{url('/mytransactions')}}" role="tab"
									aria-controls="edit" aria-selected="false">Transactions</a>
							</li>
							-->
							<li class="nav-item">
								<a class="nav-link" id="shop-address-tab" href="{{url('/mypackages')}}" role="tab"
									aria-controls="edit" aria-selected="false">My Package</a>
							</li>

							<li class="nav-item">
								<a class="nav-link" href="{{url('/logout')}}">Logout</a>
							</li>
						</ul>