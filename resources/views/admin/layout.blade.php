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
	<!-- Include TinyMCE from CDN -->
    <script src="https://cdn.tiny.cloud/1/wuonfcpr2azq20osd89xe6vv5q8putz8t2artlu13mwiqkml/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>




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
						<a href="{{url('/')}}" class="logo">
							<img src="{{asset('front/assets/images/'. $settings[1]->content)}}" width="111" height="44" alt="Biocaremaxplus Logo">
						</a>
					</div><!-- End .header-left -->

					<div class="header-right w-lg-max">
						<div
							class="header-icon header-search header-search-inline header-search-category w-lg-max text-right mt-0">
							<a href="{{url('admin')}}#" class="search-toggle" role="button"><i class="icon-search-3"></i></a>
							<form action="{{url('admin')}}" method="post" enctype="multipart/form-data">
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
							<h6><span>Call us now</span><a href="tel:#" class="text-dark font1">{{$settings[2]->content}}</a></h6>
						</div>
						@endif

						<a href="{{url('admin')}}" class="header-icon" title="login"><i class="icon-user-2"></i></a>

						
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
                                                    <li><a href="{{url('allproducts', Illuminate\Support\Facades\Crypt::encrypt($productcategory->id))}}">{{$productcategory->name}}</a></li>
                                                    @endforeach
                                                </ul>
                                            @endif
										</div>
										
									</div>
								</div><!-- End .megamenu -->
							</li>
							<li class="active"><a href="{{url('/aboutus')}}">About Us</a></li>
							<li><a href="{{url('/announcements')}}">Announcements</a></li>
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
						<a href="category.html">Categories</a>
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

			<form class="search-wrapper mb-2" action="login.html#">
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

	<style>
		.menucss{
            background: white;
            padding: 10px;
            box-shadow: 0 0.3em 0.35em rgba(128, 128, 128, 0.5); /* Increased shadow size and changed color to grey */
            border-radius: 10px !important;
        }
        
        .icon-circle {
          display: flex; 
          justify-content: center; 
          align-items: center;
          width: 150px; 
          height: 150px; 
          border-radius: 50%; 
          background-color: #f0f0f0; 
          color: #333;
          font-size: 0; 
        }
        
        .icon-circle i {
          font-size: 100px; 
          line-height: 1;
          margin: 0; 
        }
        
        .icon-circle {
            font-size:0px !important;
        }
        
        .user-box{
            background-color: #f0f0f0;
            width:100%;
            border-radius:3px;
            padding:10px;
        }


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


	</style>

	<script type="text/javascript">
		$(document).ready(function() {
			$('.select2').select2({
				allowClear: true
			});
		});

		$(document).ready(function() {
			$('#member').select2({
				placeholder: "Select a member",
				allowClear: true
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
							console.log(data);
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
    @if(isset($storesid))
        {{-- JavaScript to select options --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Example array of selected IDs (this should match the format in your backend)
                var selectedIds = @json($storesid);
        
                // Get the select element
                var selectElement = document.getElementById('stores-select');
        
                // Set selected options
                Array.from(selectElement.options).forEach(function(option) {
                    if (selectedIds.includes(option.value)) {
                        option.selected = true;
                    }
                });
        
                // Refresh select2 if needed
                if (typeof $.fn.select2 === 'function') {
                    $('#stores-select').select2();
                }
            });
        </script>
    @endif
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const currentUrl = window.location.pathname;
            const baseUrl = '{{ url("/") }}';
            const targetUrls = [`${baseUrl}/admin/membertree`, `${baseUrl}/memberdownlines`];
        
            // Check if the current URL matches one of the target URLs
            if (targetUrls.some(url => currentUrl.includes(url.replace(baseUrl, '')))) {
                // Extract the ID from the URL if available
                const urlParts = currentUrl.split('/');
                const id = urlParts[urlParts.length - 1]; // Get the last part of the URL, which should be the ID
        
                // Construct the fetch URL
                let fetchUrl = `${baseUrl}/getdownlines`;
                if (id && !isNaN(id)) { // Ensure ID is a number
                    fetchUrl = `${baseUrl}/getdownlines/${id}`;
                }
        
                // Fetch data and handle response
                fetch(fetchUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();  // Get the raw text of the response for debugging
                    })
                    .then(text => {
                        console.log('Raw response text:', text);  // Log raw response to see what you get from the server
                        let data;
                        try {
                            data = JSON.parse(text);  // Try to parse the response as JSON
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                            return;  // Exit if JSON parsing fails
                        }
                        const treeContainer = document.getElementById('downline-tree');
                        if (treeContainer) {
                            treeContainer.innerHTML = generateTreeHtml(data, 0);
                        } else {
                            console.error('Element with ID "downline-tree" not found.');
                        }
                    })
                    .catch(error => console.error('Error fetching data:', error));
        
                // Function to generate the tree HTML
                function generateTreeHtml(node, level) {
                    const maxDepth = 3; // Show up to 3 levels
        
                    if (level > maxDepth) {
                        return '';
                    }
        
                    const profilePicture = node && node.profile_picture ? node.profile_picture : '{{ asset('path/to/default/image.png') }}';
        
                    let html = `
                        <li class="${node ? '' : 'no-member'}">
                            <a href="${node ? `{{ url('/admin/membertree') }}/${node.id}` : '#'}">
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
    
    <script>
         tinymce.init({
            selector: 'textarea',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [
              { value: 'First.Name', title: 'First Name' },
              { value: 'Email', title: 'Email' },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
          });
    </script>
    
    <script>
        document.getElementById('select-all').onclick = function() {
            let checkboxes = document.querySelectorAll('.select-checkbox');
            for (let checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        };
    </script>


</body>

</html>