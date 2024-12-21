                    <div class="sidebar widget widget-dashboard mb-lg-0 mb-3 col-lg-3 order-0">
						<h2 class="text-uppercase">Navigation</h2>
						<ul class="nav nav-tabs list flex-column mb-0  menucss" role="tablist"> @php $user = Auth::guard('admin')->user(); @endphp
						    @if(isset($user) && isset($user->role) && ($user->role == "superadmin" OR $user->role == "manager"))
    							<li class="nav-item">
    								<a class="nav-link active" id="dashboard-tab" href="{{url('/admin/dashboard')}}"
    									role="tab" aria-controls="dashboard" aria-selected="true">Dashboard</a>
    							</li>
    
                                <li class="nav-item">
                                    <a class="nav-link" id="download-tab"  href="{{url('/admin/members')}}" role="tab"
                                        aria-controls="download" aria-selected="false">Members</a>
                                </li>
    
    							<li class="nav-item">
    								<a class="nav-link" id="download-tab"  href="{{url('/admin/stockist')}}" role="tab"
    									aria-controls="download" aria-selected="false">Stockist</a>
    							</li>
    							
    							<li class="nav-item">
                                    <a class="nav-link" id="download-tab"  href="{{url('/admin/admins')}}" role="tab"
                                        aria-controls="download" aria-selected="false">Admins</a>
                                </li>
                            @endif

                            @if(isset($user) && isset($user->role) && ($user->role == "superadmin" OR $user->role == "accountant"))
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/admin/earnings')}}">Members Earnings</a>
    							</li>
    
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/admin/orders')}}" role="tab"
    									aria-controls="order" aria-selected="true">Orders</a>
    							</li>
    
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/admin/withdrawals')}}" role="tab"
    									aria-controls="address" aria-selected="false">Withdrawals</a>
    							</li>
							@endif


                            @if(isset($user) && isset($user->role) && ($user->role == "superadmin" OR $user->role == "manager"))
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/admin/products')}}" role="tab"
    									aria-controls="edit" aria-selected="false">Manage Products</a>
    							</li>
    
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/admin/stores')}}" role="tab"
    									aria-controls="edit" aria-selected="false">Manage Stores</a>
    							</li>
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/admin/packages')}}" role="tab"
    									aria-controls="edit" aria-selected="false">Manage Packages</a>
    							</li>
    						@endif
    						
    						@if(isset($user) && isset($user->role) && ($user->role == "superadmin" OR $user->role == "accountant"))
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/admin/tokens')}}" role="tab"
    									aria-controls="edit" aria-selected="false">Manage Access Tokens</a>
    							</li>
							@endif
							
							@if(isset($user) && isset($user->role) && ($user->role == "superadmin" OR $user->role == "manager"))
    							<li class="nav-item">
    								<a class="nav-link"  href="{{url('/admin/banks')}}" role="tab"
    									aria-controls="address" aria-selected="false">Manage Banks</a>
    							</li>
    							
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/currency')}}" role="tab"
    									aria-controls="edit" aria-selected="false">Currency Converter</a>
    							</li>
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/admin/cms')}}">Content Management System</a>
    							</li>
                                
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/admin/banners')}}">Banners</a>
    							</li>
    							
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/admin/ranks')}}">Manage Ranks</a>
    							</li>
    							
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/admin/directreferralbonus')}}">In-Direct Referral Bonus</a>
    							</li>
    
                                
    							<li class="nav-item">
    								<a class="nav-link" href="{{url('/admin/settings')}}">Settings</a>
    							</li>
							@endif
							
							<li class="nav-item">
								<a class="nav-link" href="{{url('/admin/logout')}}">Logout</a>
							</li>
						</ul>
					</div>
					