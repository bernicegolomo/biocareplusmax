<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<title>
        @isset($title)
            {{ $title }} | 
        @endisset
        {{ config('app.name') }}
    </title>

    <meta name="keywords" content="binary MLM, multi-level marketing, network marketing, direct selling, binary compensation plan, MLM software, binary tree structure, MLM business, passive income, referral marketing, business opportunity, binary system, MLM success, network marketing software, biocare, biocaremaxplus" />
    <meta name="description" content="Join our Binary Multi-Level Marketing (MLM) platform to maximize your earning potential. Our innovative MLM software supports binary tree structures, offering a seamless experience for network marketers and direct sellers. Start your journey to financial freedom today!" />
    <meta name="author" content="eLED GLOBAL SERVICES LIMITED">

	<!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('front/assets/images/icons/favicon.png')}}">


	<script>
		WebFontConfig = {
			google: { families: [ 'Open+Sans:300,400,600,700,800', 'Poppins:300,400,500,600,700', 'Shadows+Into+Light:400' ] }
		};
		( function ( d ) {
			var wf = d.createElement( 'script' ), s = d.scripts[ 0 ];
			wf.src = 'assets/js/webfont.js';
			wf.async = true;
			s.parentNode.insertBefore( wf, s );
		} )( document );
	</script>

    <!-- Plugins CSS File -->
	<link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">

	<!-- Main CSS File -->
	<link rel="stylesheet" href="{{asset('assets/css/style.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/fontawesome-free/css/all.min.css')}}">

    <!-- Plugins CSS File -->
    <link rel="stylesheet" href="{{asset('front/assets/css/bootstrap.min.css')}}">

    <!-- Main CSS File -->
    <link rel="stylesheet" href="{{asset('front/assets/css/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('front/assets/vendor/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('front/assets/vendor/simple-line-icons/css/simple-line-icons.min.css')}}">
	<!-- Include Select2 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">


</head>

<body>
	<div class="page-wrapper">
		

		@php 
			$productcategories = App\Http\Controllers\AdminController::getCategories(); 
			$settings = App\Http\Controllers\AdminController::getSettings();  
		@endphp
                    
		<header class="header">
			<div class="header-top">
				<div class="container">
					<div class="header-left d-none d-sm-block">
						<p class="top-message text-uppercase"><span class="text-primary">Biocaremaxplus </span> <span class="separator"></span> maximize your earning potential</p>
					</div><!-- End .header-left -->

					<div class="header-right header-dropdowns ml-0 ml-sm-auto w-sm-100">
						

						<span class="separator"></span>

						<div class="social-icons">
						    @if(!empty($settings[7]->content))
                    			<a href="{{$settings[7]->content}}" class="social-icon social-facebook icon-facebook" target="_blank"></a>
                    		@endif
                    							
                    		@if(!empty($settings[8]->content))
                    			<a href="{{$settings[8]->content}}" class="social-icon social-twitter icon-twitter" target="_blank"></a>
                    		@endif
                    							
                    		@if(!empty($settings[9]->content))
                    			<a href="{{$settings[9]->content}}" class="social-icon social-linkedin icon-linkedin" target="_blank"><i class="fab fa-linkedin"></i></a>
                    		@endif
                    							
                    		@if(!empty($settings[10]->content))
                    			<a href="{{$settings[10]->content}}" class="social-icon social-tiktok icon-tiktok" target="_blank"><i class="fab fa-tiktok"></i></a>
                    		@endif
                    							
                    		@if(!empty($settings[11]->content))
                    			<a href="{{$settings[11]->content}}" class="social-icon social-instagram icon-instagram" target="_blank"></a>
                    		@endif
							
						</div><!-- End .social-icons -->
					</div><!-- End .header-right -->
				</div><!-- End .container -->
			</div><!-- End .header-top -->

			<div class="header-middle sticky-header" data-sticky-options="{'mobile': true}">
				<div class="container">
					<div class="header-left col-lg-2 w-auto pl-0">
						<button class="mobile-menu-toggler text-primary mr-2" type="button">
							<i class="fas fa-bars"></i>
						</button>
                        @if(isset($settings[1]))
                            <a href="{{url('/')}}" class="logo">
                                <img src="{{asset('front/assets/images/'. $settings[1]->content)}}" width="111" height="44" alt="Biocaremaxplus Logo">
                            </a>
                        @endif
					</div><!-- End .header-left -->

					<div class="header-right w-lg-max">
						<div
							class="header-icon header-search header-search-inline header-search-category w-lg-max text-right mt-0">
							<a href="{{url('/dashboard')}}#" class="search-toggle" role="button"><i class="icon-search-3"></i></a>
							<form action="{{url('/store')}}" method="post" enctype="multipart/form-data">
							    @csrf
								<div class="header-search-wrapper">
									<input type="search" class="form-control" name="q" id="q" placeholder="Search..."
										required>

										@if(isset($productcategories) && count($productcategories) > 0)
                                                
											<div class="select-custom">
												<select id="cat" name="cat">
													<option value="">All Categories</option>
													@foreach($productcategories as $productcategory)
														<option value="{{$productcategory->id}}">{{$productcategory->name}}</option>
													@endforeach
														
												</select>
											</div><!-- End .select-custom -->
										@endif
									<button class="btn icon-magnifier p-0" type="submit"></button>
								</div><!-- End .header-search-wrapper -->
							</form>
						</div><!-- End .header-search -->

                        @if(isset($settings[2]) && !empty($settings[2]->content))
                            <div class="header-contact d-none d-lg-flex pl-4 pr-4">
                                <img alt="phone" src="{{asset('front/assets/images/phone.png')}}" width="30" height="30" class="pb-1">
                                <h6><span>Call us now</span><a class="text-dark font1"> {{$settings[2]->content}}</a></h6>
                            </div>
                        @endif

						
						@auth('web')
                            <a href="{{ url('/myprofile') }}" class="header-icon" title="My Profile">
                                <i class="icon-user-2 text-primary"></i>
                            </a>
                            @include('components.cartSession')
                        @else
                            <!-- Display login link or message for unauthenticated users -->
                            <a href="{{ url('/login') }}" class="header-icon" title="Login">
                                <i class="icon-user-2 text-primary"></i>
                            </a>
                        @endauth
					</div><!-- End .header-right -->
				</div><!-- End .container -->
			</div><!-- End .header-middle -->

			<div class="header-bottom sticky-header d-none d-lg-block" data-sticky-options="{'mobile': false}">
				<div class="container">
					<nav class="main-nav w-100">
						<ul class="menu">
							<li>
								<a href="{{url('/')}}">Home</a>
							</li>
							<li>
								<a href="#">Categories</a>
								<div class="megamenu megamenu-fixed-width megamenu-3cols">
									<div class="row">
										<div class="col-lg-12">
											@if(isset($productcategories) && count($productcategories) > 0)
                                                <ul class="submenu">
                                                    @foreach($productcategories as $productcategory)
                                                    <li><a href="{{ url('store', [Illuminate\Support\Facades\Crypt::encrypt("3"),Illuminate\Support\Facades\Crypt::encrypt($productcategory->id)]) }}">{{$productcategory->name}}</a></li>
                                                    @endforeach
                                                </ul>
                                            @endif
										</div>
										
									</div>
								</div><!-- End .megamenu -->
							</li>
							<li class="active"><a href="{{url('/aboutus')}}">About Us</a></li>
							@auth('web')
							<li><a href="{{url('/announcements')}}">Announcements</a></li>
							@endauth
							<li><a href="{{url('/contactus')}}">Contact Us</a></li>
						</ul>
					</nav>
				</div><!-- End .container -->
			</div><!-- End .header-bottom -->
		</header><!-- End .header -->




        @yield('content')



        <footer class="footer bg-dark">
			<div class="container">
				<div class="footer-bottom">
					<div class="container d-flex flex-column align-items-center text-center">
						<div class="row">
							<div class="col-md-12">
								<div class="widget">
									<div class="social-icons d-flex justify-content-center">
										@if(!empty($settings[7]->content))
                                			<a href="{{$settings[7]->content}}" class="social-icon social-facebook icon-facebook" target="_blank"></a>
                                		@endif
                                							
                                		@if(!empty($settings[8]->content))
                                			<a href="{{$settings[8]->content}}" class="social-icon social-twitter icon-twitter" target="_blank"></a>
                                		@endif
                                							
                                		@if(!empty($settings[9]->content))
                                			<a href="{{$settings[9]->content}}" class="social-icon social-linkedin icon-linkedin" target="_blank"><i class="fab fa-linkedin"></i></a>
                                		@endif
                                							
                                		@if(!empty($settings[10]->content))
                                			<a href="{{$settings[10]->content}}" class="social-icon social-tiktok icon-tiktok" target="_blank"><i class="fab fa-tiktok"></i></a>
                                		@endif
                                							
                                		@if(!empty($settings[11]->content))
                                			<a href="{{$settings[11]->content}}" class="social-icon social-instagram icon-instagram" target="_blank"></a>
                                		@endif
									</div><!-- End .social-icons -->
								</div><!-- End .widget -->
								<span class="footer-copyright">© Biocaremaxplus. @php echo date("Y"); @endphp. All Rights Reserved</span>
							</div>
						</div>
					</div>
				</div><!-- End .footer-bottom -->
			</div><!-- End .container -->

		</footer><!-- End .footer -->
	</div><!-- End .page-wrapper -->

	<div class="loading-overlay">
		<div class="bounce-loader">
			<div class="bounce1"></div>
			<div class="bounce2"></div>
			<div class="bounce3"></div>
		</div>
	</div>

	<div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

	<div class="mobile-menu-container">
		<div class="mobile-menu-wrapper">
			<span class="mobile-menu-close"><i class="fa fa-times"></i></span>
			<nav class="mobile-nav">
				<ul class="mobile-menu">
					<li><a href="{{url('admin')}}">Home</a></li>
					<li>
						<a href="">Categories</a>
						@if(isset($productcategories) && count($productcategories) > 0)
                            <ul>
                                @foreach($productcategories as $productcategory)
                                    <li><a href="{{url('allproducts', Illuminate\Support\Facades\Crypt::encrypt($productcategory->id))}}">{{$productcategory->name}}</a></li>
                                @endforeach
                            </ul>
                        @endif
					</li>
					<li><a href="{{url('/aboutus')}}">About Us</a></li>                      
                    
				</ul>

				

				<ul class="mobile-menu">
					@auth('web')
                        <li><a href="{{url('/myprofile')}}">My Account</a></li>
                        <li><a href="{{url('/cart')}}">Cart</a></li>
                    @endauth
                    <li><a href="{{url('/contactus')}}">Contact Us</a></li>
                    @guest('admin')
                        @guest('web')
                            <li><a href="{{url('/login')}}" class="login-link">Log In</a></li>
                        @endguest
                    @endguest
				</ul>
			</nav><!-- End .mobile-nav -->

			<form class="search-wrapper mb-2" action="#">
				<input type="text" class="form-control mb-0" placeholder="Search..." required />
				<button class="btn icon-search text-white bg-transparent p-0" type="submit"></button>
			</form>

			
		</div><!-- End .mobile-menu-wrapper -->
	</div><!-- End .mobile-menu-container -->

	<div class="sticky-navbar">
	<div class="sticky-info">
            <a href="{{url('/')}}">
                <i class="icon-home"></i>Home
            </a>
        </div>
        <div class="sticky-info">
            <a href="{{url('/allproducts')}}" class="">
                <i class="icon-bars"></i>Categories
            </a>
        </div>
        @auth('web')
        <div class="sticky-info">
            <a href="{{url('/myprofile')}}" class="">
                <i class="icon-user-2"></i>Account
            </a>
        </div>
        @endauth
        <!--
        <div class="sticky-info">
            <a href="cart.html" class="">
                <i class="icon-shopping-cart position-relative">
                    <span class="cart-count badge-circle">3</span>
                </i>Cart
            </a>
        </div>
        -->
	</div>



	<a id="scroll-top" href="#top" title="Top" role="button"><i class="icon-angle-up"></i></a>


    <!--<script data-cfasync="false" src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="assets/js/jquery.min.js"></script>-->
    

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{asset('front/assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('front/assets/js/plugins.min.js')}}"></script>
    <script src="{{asset('front/assets/js/jquery.appear.min.js')}}"></script>

    <!-- Main JS File -->
    <script src="{{asset('front/assets/js/main.min.js')}}"></script>
	<!-- Include Select2 JS -->
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	
	<script src="https://d3js.org/d3.v6.min.js"></script>



    <style>
        .tree {
            position: relative;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        
        .tree ul {
            padding-top: 20px;
            position: relative;
            transition: all 0.5s;
        }
        
        .tree li {
            float: left;
            text-align: center;
            list-style-type: none;
            position: relative;
            padding: 20px 10px 0 10px; /* Increased padding for spacing */
            transition: all 0.5s;
        }
        
        .tree li::before, .tree li::after {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            border-top: 2px solid #ccc;
            width: 50%;
            height: 20px;
        }
        
        .tree li::after {
            right: auto;
            left: 50%;
            border-left: 2px solid #ccc;
        }
        
        .tree li:only-child::after, .tree li:only-child::before {
            display: none;
        }
        
        .tree li:only-child {
            padding-top: 0;
        }
        
        .tree li:first-child::before, .tree li:last-child::after {
            border: 0 none;
        }
        
        .tree li:last-child::before {
            border-right: 2px solid #ccc;
            border-radius: 0 5px 0 0;
        }
        
        .tree li:first-child::after {
            border-radius: 5px 0 0 0;
        }
        
        .tree ul ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 2px solid #ccc;
            width: 0;
            height: 20px;
        }
        
        .tree li a {
            border: 2px solid #ccc;
            padding: 5px 10px;
            text-decoration: none;
            color: #666;
            font-family: Arial, Verdana, Tahoma;
            font-size: 11px;
            display: inline-block;
            border-radius: 5px;
            transition: all 0.5s;
            background-color: #fff;
        }
        
        .tree li a img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        
        .tree li a:hover, .tree li a:hover+ul li a {
            background: #c8e4f8;
            color: #000;
            border: 2px solid #94a0b4;
        }
        
        .tree li a:hover+ul li::after,
        .tree li a:hover+ul li::before,
        .tree li a:hover+ul::before,
        .tree li a:hover+ul ul::before {
            border-color: #94a0b4;
        }
        
        .tree li.no-member a {
            color: red;
            border-color: red;
            background-color: #fdd; /* Light red background */
            padding: 10px;
            border-style: dashed;
        }
        
        /* Hide deeper levels */
        .tree ul ul ul {
            display: none;
        }
        
        /* Show only up to 3 levels */
        .tree li ul {
            display: flex;
        }
        .tree li ul ul {
            display: flex;
        }





        .menucss{
            background: white;
            padding: 10px;
            box-shadow: 0 0.3em 0.35em rgba(128, 128, 128, 0.5); /* Increased shadow size and changed color to grey */
            border-radius: 10px !important;
        }

        .btn-remove1 {
            position: absolute;
            top: 0px !important;
            right: 0px !important;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            color: #474747;
            background-color: #fff;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.4);
            text-align: center;
            line-height: 2rem;
        }
        
        .spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            text-align: center;
        }
        
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 80px;
            height: 80px;
            animation: spin 2s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .spinner p {
            margin-top: 20px;
            font-size: 16px;
            color: #333;
        }
        
        

    </style>

	<script type="text/javascript">
		$(document).ready(function() {
			$('.select2').select2({
				allowClear: true
			});
		});
		
		document.querySelector('form').addEventListener('submit', function() {
            // Show spinner on form submission
            document.querySelector('form').addEventListener('submit', function(e) {
                document.getElementById('spinner').style.display = 'block';
            
                // Disable form resubmission by disabling all buttons
                document.querySelectorAll('button[type="submit"]').forEach(button => button.disabled = true);
            
                // Prevent page reload or closing the page
                window.onbeforeunload = function () {
                    return "Your transaction is processing. Are you sure you want to leave the page?";
                };
            });
        });
	</script>

	<script type="text/javascript">
		$(document).ready(function() {
			$('#country').on('change', function() {
				var countryId = $(this).val();
				var packageId = $('#total').val(); 

				if (countryId && packageId) {
					$.ajax({
						url: '{{ url("/get-conversion-rate") }}',
						type: 'GET',
						data: {
							country_id: countryId,
							package_id: packageId
						},
						dataType: 'json',
						success: function(data) {
							$('#conversion-rate').text(data.conversion);
							$('#amount').val(data.price); // Update the input value
						},
						error: function() {
							$('#conversion-rate').text('N/A');
							$('#amount').val(''); // Clear the input field on error
						}
					});
				} else {
					$('#conversion-rate').text('N/A');
					$('#amount').val(''); // Clear the input field if no country or package is selected
				}
			});

			$('#package').on('input', function() {
				$('#country').trigger('change');
			});
		});
	</script>

    <script>
          document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-add-cart').forEach(function (button) {
                button.addEventListener('click', function () {
                    var productId = this.getAttribute('data-id');
                    var productElement = this.closest('.product-default');
                    var quantity = productElement.querySelector('.product-quantity').value || 1;
                    var store = productElement.querySelector('.store').value;
                    var type = productElement.querySelector('.type').value;
        
                    //console.log('Product ID:', productId);
                    //console.log('Quantity:', quantity);
                    //console.log('Store:', store);
                    //console.log('Type:', type);
        
                    fetch('{{ url('/addtocart') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: productId,
                            quantity: quantity,
                            store: store,
                            type: type
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        //console.log('Response:', data);
                        if (data.success) {
                            alert('Item added to cart');
                            updateCart();
                        } else {
                            alert('Failed to add item to cart');
                        }
                    })
                    .catch(error => {
                        //console.error('Error:', error);
                        alert('An error occurred');
                    });
                });
            });
        
            // Function to update cart content
            function updateCart() {
                fetch('{{ url('/getcart') }}')
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.querySelector('.cart-count');
                    const cartContent = document.getElementById('cart-content');
        
                    // Update cart count
                    cartCount.textContent = data.count;
        
                    // Update cart content
                    cartContent.innerHTML = data.html;
                })
                .catch(error => console.error('Error:', error));
            }
        });

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cartContent = document.getElementById('cart-content');
        
            // Handle remove item click
            cartContent.addEventListener('click', function (e) {
                if (e.target.classList.contains('btn-remove') || e.target.parentElement.classList.contains('btn-remove')) {
                    e.preventDefault();
        
                    const productId = e.target.closest('.btn-remove').getAttribute('data-id');
        
                    fetch('{{ url('removefromcart') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: productId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateCart(data);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        
            // Handle close button click
            cartContent.addEventListener('click', function (e) {
                if (e.target.classList.contains('btn-close')) {
                    e.preventDefault();
                    cartContent.classList.remove('show');  // Assuming 'show' is the class that makes the cart dropdown visible
                }
            });
        
            function updateCart(data) {
                // Update the cart count
                document.querySelector('.cart-count').textContent = data.cart_count;
        
                // Update the cart total price
                document.querySelector('.cart-total-price').textContent = `${data.symbol} ${new Intl.NumberFormat().format(data.total)}`;
        
                // Re-render the cart items
                let cartProductsHTML = '';
                for (const [id, cartSession] of Object.entries(data.cart)) {
                    cartProductsHTML += `
                        <div class="product">
                            <div class="product-details">
                                <h4 class="product-title">
                                    <a href="#">${cartSession.name}</a>
                                </h4>
                                <span class="cart-product-info">
                                    <span class="cart-product-qty">${cartSession.quantity}</span>
                                    × ${cartSession.symbol} ${new Intl.NumberFormat().format(cartSession.price)}
                                </span>
                            </div>
                            <figure class="product-image-container">
                                <a href="#" class="product-image">
                                    <img src="{{asset('front/assets/images/products/${cartSession.image}')}}" class="w-100" style="width: 80px; height: 80px; object-fit: cover;" alt="${cartSession.name}" width="80" height="80"/>
                                </a>
                                <a href="#" class="btn-remove" data-id="${id}" title="Remove Product"><span>×</span></a>
                            </figure>
                        </div>
                    `;
                }
        
                // Update the cart product list
                document.querySelector('.dropdown-cart-products').innerHTML = cartProductsHTML;
        
                // Optionally, if the cart is empty, you can hide the cart dropdown or show a message
                if (data.cart_count === 0) {
                    cartContent.innerHTML = '<p>Your cart is empty.</p>';
                }
            }
        });

    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners for buttons
            document.querySelectorAll('.btn-quantity').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    const action = event.target.getAttribute('data-action');
                    const productId = event.target.getAttribute('data-id');
                    let quantityElement = document.querySelector(`.item-quantity[data-id="${productId}"]`);
                    let currentQuantity = parseInt(quantityElement.innerText, 10);
        
                    // Adjust quantity based on button action
                    if (action === 'increase') {
                        currentQuantity += 1;
                    } else if (action === 'decrease' && currentQuantity > 1) {
                        currentQuantity -= 1;
                    }
        
                    // Update UI immediately (optional, for faster feedback)
                    quantityElement.innerText = currentQuantity;
        
                    // Call updateCart function to handle the backend update
                    updateCart(productId, currentQuantity);
                });
            });
        });
        
        function updateCart(productId, action) {
            const quantityElement = document.querySelector('.item-quantity[data-id="' + productId + '"]');
            let newQuantity = parseInt(quantityElement.innerText);
        
            // Adjust the quantity based on the action
            if (action === 'increase') {
                newQuantity++;
            } else if (action === 'decrease' && newQuantity > 1) {
                newQuantity--;
            }
        
            // Send the updated quantity to the server
            fetch('/updatecart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    id: productId,
                    quantity: newQuantity,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the quantity and subtotal on the front end
                    quantityElement.innerText = newQuantity;
        
                    // Update the subtotal for the updated item
                    const subtotalElement = document.querySelector('.item-subtotal[data-id="' + productId + '"]');
                    const cartItem = data.cartItems[productId];
                    const price = cartItem.price;
                    const subtotal = price * newQuantity;
                    subtotalElement.innerText = cartItem.symbol + " " + subtotal.toLocaleString();
        
                    // Recalculate and update the cart summary
                    let total = 0;
                    let discount = 0;
        
                    // Loop through all cart items and recalculate total and discount
                    document.querySelectorAll('.item-subtotal').forEach(subtotalElement => {
                        const subtotalText = subtotalElement.innerText.split(' ')[1].replace(',', ''); 
                        total += parseFloat(subtotalText);
                    });
        
                    document.querySelectorAll('.item-quantity').forEach(quantityElement => {
                        const itemId = quantityElement.getAttribute('data-id');
                        const cartItem = data.cartItems[itemId]; 
                        discount += cartItem.quantity * cartItem.discount;
                    });
        
                    // Update the cart summary
                    const totalElement = document.querySelector('.cart-summary-total');
                    const discountElement = document.querySelector('.cart-summary-discount');
                    const finalTotalElement = document.querySelector('.cart-summary-final-total');
        
                    if (totalElement && discountElement && finalTotalElement) {
                        totalElement.innerText = cartItem.symbol + " " + total.toLocaleString();
                        discountElement.innerText = cartItem.symbol + " " + discount.toLocaleString();
                        finalTotalElement.innerText = cartItem.symbol + " " + (total - discount).toLocaleString();
                    }
                } else {
                    console.error('Failed to update cart');
                }
            })
            .catch(error => console.error('Error updating cart:', error));
        }



document.querySelectorAll('.btn-removes').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();  // Prevent the default action (link behavior)
        const productId = this.getAttribute('data-id');
        
        // Send a request to remove the product from the cart
        fetch('/removecartitem', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                id: productId,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the item from the DOM
                const cartItemRow = document.querySelector(`tr[data-id="${productId}"]`);
                if (cartItemRow) {
                    cartItemRow.remove();
                }

                // Update the cart summary with new totals
                const totalElement = document.querySelector('.cart-summary-total');
                const discountElement = document.querySelector('.cart-summary-discount');
                const finalTotalElement = document.querySelector('.cart-summary-final-total');

                if (totalElement && discountElement && finalTotalElement) {
                    totalElement.innerText = data.cartSummary.symbol + " " + data.cartSummary.total.toLocaleString();
                    discountElement.innerText = data.cartSummary.symbol + " " + data.cartSummary.discount.toLocaleString();
                    finalTotalElement.innerText = data.cartSummary.symbol + " " + data.cartSummary.cartTotal.toLocaleString();
                }
            } else {
                console.error('Failed to remove item from cart');
            }
        })
        .catch(error => console.error('Error removing cart item:', error));
    });
});


    
        
    </script>
    
    
        
    <script>
        


        document.addEventListener('DOMContentLoaded', function () {
            const currentUrl = window.location.pathname;
            const baseUrl = '{{ url("/") }}';
            const targetUrls = [`${baseUrl}/mydownlines`, `${baseUrl}/memberdownlines`];
        
            if (targetUrls.some(url => currentUrl.includes(url.replace(baseUrl, '')))) {
                let id = '{{ isset($id) ? $id : '' }}';
                let fetchUrl = id ? `{{ url('/getdownlines') }}/${id}` : '{{ url('/getdownlines') }}';
        
                fetch(fetchUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const treeContainer = document.getElementById('downline-tree');
                        treeContainer.innerHTML = generateTreeHtml(data, 0);
                    })
                    .catch(error => console.error('Error fetching data:', error));
        
                function generateTreeHtml(node, level) {
                    const maxDepth = 3;  // Show up to 3 levels
        
                    if (level > maxDepth) {
                        return '';
                    }
        
                    const profilePicture = node && node.profile_picture ? node.profile_picture : '{{ asset('path/to/default/image.png') }}';
        
                    let html = `
                        <li class="${node ? '' : 'no-member'}">
                            <a href="${node ? `{{ url('/mydownlines') }}/${node.id}` : '#'}">
                                ${node ? `
                                    <img src="${profilePicture}" alt="${node.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                    <br>
                                    ${node.username}
                                ` : 'No Member'}
                            </a>
                    `;
        
                    if (node && (node.left || node.right)) {
                        html += '<ul>';
                        html += generateTreeHtml(node.left, level + 1);
                        html += generateTreeHtml(node.right, level + 1);
                        html += '</ul>';
                    }
                    html += '</li>';
        
                    return html;
                }
            } else {
                console.log('URL does not match target URLs');
            }
        });


    </script>



           
</body>

</html>