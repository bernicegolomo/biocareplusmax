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
									{{$title}}
								</li>
							</ol>
						</div>
					</nav>

					<h1>My Downlines</h1>
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

                                    <!--
                                    <a href="{{url('/admin/newbanner')}}" class="btn btn-primary btn-sm">Add Banner</a>
                                    <hr class="mt-0 mb-3 pb-2" />
                                    -->

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
                                    
                                    <p class="text-danger"><strong>TOTAL DOWNLINES -</strong> {{$totalMembers}} </p>
									<div class="mb-4"></div>
                                    
                                    <a class="btn btn-danger btn-sm text-white mt-5" data-toggle="modal" data-target="#exampleModal">Quick Search</a>
                                    <hr class="mt-0 mb-3 pb-2" />

                                    <div class="row row-lg">
                                        <div class="col-12 col-md-12">
                                            @if($paginator->count())

                                                <table class="table table-bordered text-center p-2">
                                                    <thead>
                                                        <tr>
                                                            <th class="order-id">S/NO</th>
                                                            <th class="order-date">USERNAME</th>
                                                            <th class="order-date">ENTRY DATE</th>
                                                            <th class="order-date">ENTRY PACKAGE</th>
                                                            <th class="order-date">CURRENT PACKAGE</th>
                                                            <th class="order-date">RANK</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody style="font-size:13px;">
                                                        @php $x = 0; @endphp
                                                        @foreach ($paginator->items() as $memberId)
                                                            @php
                                                                // Retrieve member details
                                                                $member = $members[$memberId] ?? null;
                                                                
                                                                // Retrieve member packages
                                                                $memberPackages = $packages[$memberId] ?? collect();
                                                                $firstPackage = $memberPackages->first();
                                                                $secondPackage = $memberPackages->last();
                                                                // Retrieve member rank
                                                                $rank = $ranks[$memberId] ?? 'N/A';
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $member->username ?? 'N/A' }}</td>
                                                                <td>{{ $member->created_at ? \Carbon\Carbon::parse($member->created_at)->format('M. jS Y')  : 'N/A' }}</td>
                                                                <!-- Entry Package (first package) -->
                                                                <td>
                                                                    @if (isset($packages[$memberId]))
                                                                        {{ $packages[$memberId]->first()->package_name ?? 'N/A' }}
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </td>
                                                        
                                                                <!-- Current Package (last package, assuming they can upgrade) -->
                                                                <td>
                                                                    @if (isset($packages[$memberId]))
                                                                        {{ $packages[$memberId]->last()->package_name ?? 'N/A' }}
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </td>
                                                                <td>{{ $rank }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                
                                                <!-- Render pagination links -->
                                                {{ $paginator->links() }}

                                            @else
                                            <p class="text-center text-danger"> No downline found!</p>
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
                <form  action="{{url('/listdownlines')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Select Rank </label>
                                    <select name="rank" class="form-control">
                                        <option value="" selected=""></option>
                                        <option value="Elite">Elite</option>
                                        <option value="Royal Diamond">Royal Diamond</option>
                                        <option value="Crown Diamond">Crown Diamond</option>
                                        <option value="Sapphire">Sapphire</option>
                                        <option value="Emerald">Emerald</option>
                                        <option value="Ruby">Ruby</option>
                                        <option value="Diamond">Diamond</option>
                                        <option value="Platinum">Platinum </option>
                                        <option value="Gold"> Gold</option>
                                        <option value="Silver"> Silver</option>
                                        <option value="Bronze"> Bronze</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Select Package </label>
                                    <select name="package" class="form-control">
                                        <option value="" selected=""></option>
                                        @foreach($newpackages as $newpackage)
                                            <option value="{{$newpackage->id}}">{{$newpackage->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Entry Date </label>
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


                    
										
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection