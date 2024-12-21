                            @if(session()->has('cart') && count(session()->get('cart')) > 0)
								<section class="">
									<div class="row">
										

										<div class="col-md-12 col-xs-12">
                                            <h2 class="section-title ls-n-15 text-center pt-2 m-b-4">Shopping Cart</h2>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                        <th>Pv</th>
                                                        <th>Subtotal</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="cart-items">
                                                    @foreach(session()->get('cart') as $id => $cartitem)
                                                        <tr data-id="{{ $id }}">
                                                            <td>{{ $cartitem['name'] }}</td>
                                                            <td>
                                                                <button class="btn-quantity" data-action="decrease" data-id="{{ $id }}">-</button>
                                                                <span class="item-quantity" data-id="{{ $id }}">{{ $cartitem['quantity'] }}</span>
                                                                <button class="btn-quantity" data-action="increase" data-id="{{ $id }}">+</button>
                                                            </td>
                                                            <td>{{ $cartitem['symbol'] }} {{ number_format($cartitem['price']) }}</td>
                                                            <td>{{ $cartitem['pv'] * $cartitem['quantity'] }}</td>
                                                            <td class="item-subtotal cart-total-price" data-id="{{ $id }}">{{ $cartitem['symbol'] }} {{ number_format($cartitem['quantity'] * $cartitem['price']) }}</td>
                                                            <td>
                                                                <a href="#" class="btn-removes" data-id="{{ $id }}" title="Remove Product"><span>×</span></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>





                                            </table>
                                        </div>
                                        <!-- Spinner HTML (initially hidden) -->
                                        <div id="spinner" class="spinner" style="display: none;">
                                            <div class="loader"></div>
                                            <p>Your transaction is processing. Please do not refresh or close the page.</p>
                                        </div>
                                        
										<!--checkout section-->
										<div class="col-md-8 col-xs-12 mt-5" >
											<form  action="{{url('checkout')}}" method="post" enctype="multipart/form-data">
												@csrf
												
                                                <div id="cart-summary" style="background:#f4f4f4; padding:10px 20px;">
                                                    <h4 class="section-title ls-n-15 text-danger" style="font-size: 1.5rem; line-height:2.5rem;">
                                                        <b>Cart Sub-Total:</b>
                                                        <span class="cart-summary-total">{{ $cartitem['symbol'] }} {{ number_format($total) }}</span><br>
                                                        <b>Discount:</b>
                                                        <span class="cart-summary-discount">{{ $cartitem['symbol'] }} {{ number_format($discount) }}</span><br>
                                                        <b>Cart Total:</b>
                                                        <span class="cart-summary-final-total">{{ $cartitem['symbol'] }} {{ number_format($total - $discount) }}</span>
                                                    </h4>
                                                </div>


												@if(isset($store) && $store == "Entry Store")
													<p class="text-danger text-center"><em>If the product you have selected costs more than the total amount of your sign up package, we'll deduct the balance from your cash wallet. </em></p>
												@endif

												<p class="text-danger text-center mb-2 mt-2"><em>Select Your Pickup Location</em></p>
												<div class="row">
                                                    @if(isset($pickups) && count($pickups) > 0)
                                                        @foreach($pickups as $key => $pickup)
                                                            @if(App\Http\Controllers\MemberController::validPick($user->id, $pickup->id, $pickup->type))
                                                                <div class="col-md-6 col-lg-4 mt-2 mb-2">
                                                                    <div class="pickup-option p-3 border rounded shadow-sm">
                                                                        <input type="radio" name="pickup" class="pickup-radio" value="{{$pickup->id}}" id="pickup{{$key}}" />
                                                                        <label class="mb-0" for="pickup{{$key}}">
                                                                            @php 
                                                                                $location = App\Http\Controllers\MemberController::memberdetails($pickup->member_id); 
                                                                            @endphp
                                                                            @if($location && !empty($location))
                                                                                <div class="pickup-info">
                                                                                    <h5 class="pickup-name">{{ strtoupper($location->name) }}</h5>
                                                                                    <p class="pickup-details">
                                                                                        <span class="pickup-address">{{ $location->address }}</span><br>
                                                                                        <span class="pickup-phone">{{ $location->phone }}</span>
                                                                                    </p>
                                                                                </div>
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>


												<button type="submit" value="1" name="token" class="btn btn-danger btn-md w-100">
													PAY WITH VOUCHER
												</button>
												@php 
                    							    $Luser = Auth::guard('web')->user();  
                    							    $LisStokist = App\Http\Controllers\MemberController::isStockist($Luser->id); 
                    							@endphp
												@if($LisStokist && isset($cartitem['store']) && $cartitem['store'] != 1)
												    <hr class="mt-3 mb-3 pb-2" />
												    <button type="submit" value="2" name="token2" class="btn btn-dark btn-md w-100">
    													PAY WITH STOCKIST WALLET
    												</button>
												@endif

												<!--<button type="submit"name="card" value="1" class="btn btn-primary btn-md w-100">
													PAY WITH CARD
												</button>-->

												<hr>
											</form>
											
										</div>
									</div>
                                </section>
								@endif