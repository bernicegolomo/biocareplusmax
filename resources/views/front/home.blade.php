<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

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
            google: { families: [ 'Open+Sans:300,400,600,700,800', 'Poppins:300,400,500,600,700' ] }
        };
        ( function ( d ) {
            var wf = d.createElement( 'script' ), s = d.scripts[ 0 ];
            wf.src = 'front/assets/js/webfont.js';
            wf.async = true;
            s.parentNode.insertBefore( wf, s );
        } )( document );
    </script>

    <!-- Plugins CSS File -->
    <link rel="stylesheet" href="{{asset('front/assets/css/bootstrap.min.css')}}">

    <!-- Main CSS File -->
    <link rel="stylesheet" href="{{asset('front/assets/css/demo3.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('front/assets/vendor/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('front/assets/vendor/simple-line-icons/css/simple-line-icons.min.css')}}">
</head>

<body class="full-screen-slider">
    <div class="page-wrapper">
        <header class="header header-transparent">
            <div class="header-middle sticky-header">
                <div class="container">
                    <div class="header-left">
                        <button class="mobile-menu-toggler" type="button">
                            <i class="fas fa-bars"></i>
                        </button>

                        <a href="{{url('/')}}" class="logo">
                            <img src="{{asset('front/assets/images/logo-black.png')}}" alt="Biocaremaxplus" style="height:50px;">
                        </a>

                        <nav class="main-nav font2">
                            <ul class="menu">
                                <li class="active">
                                    <a href="{{url('/')}}">Home</a>
                                </li>
                                <li>
                                    @php $productcategories = App\Http\Controllers\AdminController::getCategories(); @endphp
                    
                                    <a href="">Categories</a>
                                    <div class="megamenu megamenu-fixed-width megamenu-3cols">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                @if(isset($productcategories) && count($productcategories) > 0)
                                                <ul class="submenu">
                                                    @foreach($productcategories as $productcategory)
                                                    <li><a href="{{url('allproducts', Illuminate\Support\Facades\Crypt::encrypt($productcategory->id))}}">{{$productcategory->name}}</a></li>
                                                    @endforeach
                                                </ul>
                                                @endif
                                            </div>
                                            
                                        </div>
                                    </div><!-- End .megamenu -->
                                </li>
                                <li><a href="{{url('/aboutus')}}">About Us</a></li>
                                <li><a href="{{url('/contactus')}}">Contact Us</a>
                                </li>
                            </ul>
                        </nav>
                    </div><!-- End .header-left -->

                    <div class="header-right">
                        <div class="header-search header-search-popup header-search-category d-none d-sm-block">
                            <a href="{{url('/')}}#" class="search-toggle" role="button"><i class="icon-magnifier"></i></a>
                            <form action="{{url('/quicksearch')}}#" method="get">
                                <div class="header-search-wrapper">
                                    <input type="search" class="form-control" name="q" id="q"
                                        placeholder="I'm searching for..." required="">
                                        
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
                                    <button class="btn text-dark icon-magnifier" type="submit"></button>
                                </div><!-- End .header-search-wrapper -->
                            </form>
                        </div>

                        @guest('admin')
                            @guest('web')
                                <a href="{{url('/login')}}" class="header-icon header-icon-user" title="Login"><i
                                        class="icon-user-2"></i></a>
                            @endguest
                        @endguest
                        

                        
                    </div><!-- End .header-right -->
                </div><!-- End .container -->
            </div><!-- End .header-middle -->
        </header><!-- End .header -->

        <main class="main">
            @if(isset($banners) && count($banners) > 0)
                <div class="home-slider slide-animate owl-carousel owl-theme show-nav-hover nav-big">
                    @foreach($banners as $key=>$banner)
                        <div class="home-slide home-slide{{$key}} banner d-flex align-items-center">
                            <img class="slide-bg" src="{{asset('front/assets/images/banners/'.$banner->image)}}"
                                style="background-color: #ecc;" alt="{{$banner->name}}">
                                <!--
                                <div class="banner-layer appear-animate" data-animation-name="fadeInUpShorter">
                                    <h2>Network & Earn</h2>
                                    <h3 class="text-uppercase mb-0">Get up to 30% off</h3>
                                    <h4 class="m-b-4">on Discount Product</h4>
    
                                    <a href="{{url('/allproducts')}}" class="btn btn-dark btn-xl" role="button">Shop Now</a>
                                   
                                </div><!-- End .banner-layer -->
                                
                        </div><!-- End .home-slide -->
                    @endforeach
                </div><!-- End .home-slider -->
            @endif

            <section class="container">
                <h2 class="section-title ls-n-15 text-center pt-2 m-b-4">Featured Products</h2>

                @if(isset($featuredProducts) && count($featuredProducts) > 0)
                    <div class="owl-carousel owl-theme nav-image-center show-nav-hover nav-outer cats-slider appear-animate"
                        data-animation-name="fadeInUpShorter" data-animation-delay="200" data-animation-duration="1000">
                        @foreach($featuredProducts as $featuredProduct)
                        <div class="product-category">
                            <a href="{{url('/login')}}">
                                <figure>
                                    <img src="{{asset('front/assets/images/products/'.$featuredProduct->image)}}"  class="w-100" style="width: 273px; height: 273px; object-fit: cover;"
                                        alt="{{$featuredProduct->name}}" />
                                </figure>
                                <div class="category-content">
                                    <h3>{{$featuredProduct->name}}</h3>
                                    <span><mark class="count">&#8358; {{$featuredProduct->price}}</mark> </span>
                                </div>
                            </a>
                        </div>
                        @endforeach
                        
                    </div>
                @endif
            </section>

            <section class="bg-gray banners-section text-center">
                <h2 class="section-title ls-n-15 text-center pt-2 m-b-4">Shop By Category</h2>
                @if(isset($categories) && count($categories) > 0)
                <div class="container py-2">
                    <div class="row">
                        @foreach($categories as $key=>$category)
                            <div class="col-sm-6 col-lg-3 appear-animate" data-animation-name="fadeInLeftShorter"
                                data-animation-delay="@if($key==0) 100 @elseif($key==1) 300 @elseif($key==2) 500 @elseif($key==3) 700 @endif" data-animation-duration="1000">
                                <div class="home-banner banner banner-sm-vw mb-2">
                                    <img src="{{asset('front/assets/images/categories/'.$category->image)}}"
                                        style="background-color: #ccc;" class="w-100" style="width: 419px; height: 629px; object-fit: cover;" alt="$category->name" />
                                    <div class="banner-layer banner-layer-bottom text-left">
                                        <!--<h3 class="m-b-2">{{$category->name}}</h3>-->
                                        <a href="{{url('allproducts', Illuminate\Support\Facades\Crypt::encrypt($category->id))}}" class="btn  btn-light bg-white" role="button">Shop By {{$category->name}}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                    </div>
                </div>
                @endif
            </section>

            <section class="container pb-3 mb-1">
                <h2 class="section-title ls-n-15 text-center pb-2 m-b-4">Discounted Products</h2>

                @if(isset( $discountProducts) && count( $discountProducts) > 0)
                <div class="row py-4">
                    @foreach( $discountProducts as  $discountProduct)
                    <div class="col-6 col-sm-4 col-md-3 col-xl-2 appear-animate" data-animation-name="fadeIn"
                        data-animation-delay="300" data-animation-duration="1000">
                        <div class="product-default inner-quickview inner-icon">
                            <figure>
                                <a href="{{url('/login')}}">
                                    <img src="{{asset('front/assets/images/products/'. $discountProduct->image)}}" 
                                        class="w-100" style="width: 273px; height: 273px; object-fit: cover;" alt="{{ $discountProduct->name}}" />
                                </a>
                                <div class="label-group">
                                    <div class="product-label label-sale">{{$discountProduct->discount}}</div>
                                </div>
                                <div class="btn-icon-group">
                                    <a href="{{url('/login')}}" class="btn-icon btn-add-cart product-type-simple"><i
                                            class="icon-shopping-cart"></i></a>
                                </div>
                                <a href="{{url('/login')}}" class="btn-quickview" title="Quick View">Quick
                                    View</a>
                            </figure>
                            <div class="product-details">
                               
                                <div class="price-box">
                                    <span class="old-price">&#8358; {{$discountProduct->price}}</span>
                                    <span class="product-price">&#8358; {{$discountProduct->oldprice}}</span>
                                </div><!-- End .price-box -->
                            </div><!-- End .product-details -->
                        </div>
                    </div>
                    @endforeach
                    
                </div>
                @endif

                
            </section>
        </main><!-- End .main -->

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
                    <li><a href="{{url('/')}}">Home</a></li>
                    <li>
                        <a href="{{url('/allproducts')}}">Categories</a>
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
                        <li><a href="{{url('/myaccount')}}">My Account</a></li>
                        <li><a href="{{url('/cart')}}">Cart</a></li>
                    @endauth
                    <li><a href="{{url('/contactus')}}">Contact Us</a></li>
                    <li><a href="{{url('/announcements')}}">Announcements</a></li>
                    @guest('admin')
                        @guest('web')
                            <li><a href="{{url('/login')}}" class="login-link">Log In</a></li>
                        @endguest
                    @endguest
                </ul>
            </nav><!-- End .mobile-nav -->

            <form class="search-wrapper mb-2" action="{{url('/')}}#">
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
            <a href="{{url('/myaccount')}}" class="">
                <i class="icon-user-2"></i>Account
            </a>
        </div>
        @endauth
        <div class="sticky-info">
            <a href="cart.html" class="">
                <i class="icon-shopping-cart position-relative">
                    <span class="cart-count badge-circle">3</span>
                </i>Cart
            </a>
        </div>
    </div>

    
    <a id="scroll-top" href="{{url('/')}}#top" title="Top" role="button"><i class="icon-angle-up"></i></a>

    <!-- Plugins JS File -->
    <!--<script data-cfasync="false" src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="assets/js/jquery.min.js"></script>-->
    

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{asset('front/assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('front/assets/js/plugins.min.js')}}"></script>
    <script src="{{asset('front/assets/js/jquery.appear.min.js')}}"></script>

    <!-- Main JS File -->
    <script src="{{asset('front/assets/js/main.min.js')}}"></script>
</body>

</html>