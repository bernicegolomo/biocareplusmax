

<div class="dropdown cart-dropdown">
	<a href="{{url('/dashboard')}}#" title="Cart" class="dropdown-toggle dropdown-arrow cart-toggle" role="button"
		data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
		<i class="minicart-icon"></i>
		<span class="cart-count badge-circle">
            @if(session()->has('cart') && count(session()->get('cart')) > 0)
                {{ count(session()->get('cart')) }}
            @else
                0
            @endif
        </span>
	</a>


	<!--<div class="cart-overlay"></div>-->

	<div class="dropdown-menu mobile-cart"  id="cart-content">
    
        @if(session()->has('cart') && count(session()->get('cart')) > 0)
            <a href="{{url('/dashboard')}}#" title="Close (Esc)" class="btn-close dropdown-toggle dropdown-arrow cart-toggle">×</a>

            <div class="dropdownmenu-wrapper custom-scrollbar">
                <div class="dropdown-cart-header">Shopping Cart</div>

                <div class="dropdown-cart-products">
                    @php $total = 0; @endphp
                    @foreach(session()->get('cart') as $id => $cartSession)
                        @php $total += $cartSession['quantity'] * $cartSession['price']; @endphp
                        <div class="product">
                            <div class="product-details">
                                <h4 class="product-title">
                                    <a href="">{{$cartSession['name']}}</a>
                                </h4>

                                <span class="cart-product-info">
                                    <span class="cart-product-qty">{{$cartSession['quantity']}}</span>
                                    × {{$cartSession['symbol'] }} {{number_format($cartSession['price'])}}
                                </span>
                            </div>

                            <figure class="product-image-container">
                                @if(isset($cartSession['image']))
                                    <a href="" class="product-image">
                                        <img src="{{asset('front/assets/images/products/'. $cartSession['image'])}}"  class="w-100" style="width: 80px; height: 80px; object-fit: cover;" alt="{{$cartSession['name']}}" width="80" height="80"/>
                                    </a>
                                @endif
                                <a href="" class="btn-remove" data-id="{{ $id }}" title="Remove Product"><span>×</span></a>
                            </figure>
                        </div>
                    @endforeach
                </div>

                <div class="dropdown-cart-total">
                    <span>TOTAL:</span>
                    <span class="cart-total-price float-right">{{$cartSession['symbol'] }}  {{ number_format($total) }}</span>
                </div>

                <div class="dropdown-cart-action">
                    <a href="{{ url('cart') }}" class="btn btn-gray btn-block view-cart">View Cart</a>
                    <a href="{{ url('cart') }}" class="btn btn-dark btn-block">Checkout</a>
                </div>
            </div>
        @endif
    </div><!-- End .dropdown-menu -->
</div><!-- End .dropdown -->
