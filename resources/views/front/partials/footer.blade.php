@php
    use App\Models\SiteSetting;
    $s = SiteSetting::all_settings();
@endphp

    <!-- Footer Start -->
    <div class="container-fluid bg-secondary text-dark mt-5 pt-5">
        <div class="row px-xl-5 pt-5">

            {{-- Col 1: Brand + Social --}}
            <div class="col-lg-3 col-md-6 mb-5 pr-3 pr-xl-5">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <h1 class="mb-4 display-5 font-weight-semi-bold"><span class="text-primary font-weight-bold border border-white px-3 mr-1">J</span>eanzo</h1>
                </a>
                <p class="mb-2">Quality fashion at your fingertips. Fast delivery and easy returns.</p>
                <p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-3"></i>{{ $s['store_address'] ?? '123 Street, New York, USA' }}</p>
                <p class="mb-2"><i class="fa fa-envelope text-primary mr-3"></i>{{ $s['store_email'] ?? 'info@jeanzo.in' }}</p>
                <p class="mb-2"><i class="fa fa-phone-alt text-primary mr-3"></i>{{ $s['store_phone'] ?? '+012 345 67890' }}</p>
                <div class="d-flex mt-3">
                    @if(!empty($s['twitter_url']))<a class="btn btn-primary btn-square mr-2" href="{{ $s['twitter_url'] }}" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a>@endif
                    @if(!empty($s['facebook_url']))<a class="btn btn-primary btn-square mr-2" href="{{ $s['facebook_url'] }}" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>@endif
                    @if(!empty($s['linkedin_url']))<a class="btn btn-primary btn-square mr-2" href="{{ $s['linkedin_url'] }}" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i></a>@endif
                    @if(!empty($s['instagram_url']))<a class="btn btn-primary btn-square mr-2" href="{{ $s['instagram_url'] }}" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>@endif
                    @if(!empty($s['youtube_url']))<a class="btn btn-primary btn-square" href="{{ $s['youtube_url'] }}" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a>@endif
                </div>
            </div>

            {{-- Col 2: Quick Links + My Account --}}
            <div class="col-lg-3 col-md-6 mb-5">
                <h5 class="font-weight-bold text-dark mb-4">Quick Links</h5>
                <div class="d-flex flex-column justify-content-start mb-4">
                    <a class="text-dark mb-2" href="{{ url('/') }}"><i class="fa fa-angle-right mr-2"></i>Home</a>
                    <a class="text-dark mb-2" href="{{ route('products.index') }}"><i class="fa fa-angle-right mr-2"></i>Our Shop</a>
                    <a class="text-dark mb-2" href="{{ route('blog.index') }}"><i class="fa fa-angle-right mr-2"></i>Blog</a>
                    <a class="text-dark mb-2" href="{{ route('cart.index') }}"><i class="fa fa-angle-right mr-2"></i>Shopping Cart</a>
                    <a class="text-dark mb-2" href="{{ route('checkout.index') }}"><i class="fa fa-angle-right mr-2"></i>Checkout</a>
                    <a class="text-dark mb-2" href="{{ route('faq') }}"><i class="fa fa-angle-right mr-2"></i>FAQs</a>
                    <a class="text-dark mb-2" href="{{ route('help') }}"><i class="fa fa-angle-right mr-2"></i>Help Center</a>
                    <a class="text-dark" href="{{ route('contact') }}"><i class="fa fa-angle-right mr-2"></i>Contact Us</a>
                </div>

                <h5 class="font-weight-bold text-dark mb-3">My Account</h5>
                <div class="d-flex flex-column justify-content-start">
                    @auth
                    <a class="text-dark mb-2" href="{{ route('profile.dashboard') }}"><i class="fa fa-angle-right mr-2"></i>My Profile</a>
                    <a class="text-dark mb-2" href="{{ route('profile.orders') }}"><i class="fa fa-angle-right mr-2"></i>My Orders</a>
                    <a class="text-dark mb-2" href="{{ route('profile.reviews') }}"><i class="fa fa-angle-right mr-2"></i>My Reviews</a>
                    <a class="text-dark mb-2" href="{{ route('wishlist.index') }}"><i class="fa fa-angle-right mr-2"></i>Wishlist</a>
                    @else
                    <a class="text-dark mb-2" href="{{ route('customer.login') }}"><i class="fa fa-angle-right mr-2"></i>Login</a>
                    <a class="text-dark mb-2" href="{{ route('customer.register') }}"><i class="fa fa-angle-right mr-2"></i>Register</a>
                    @endauth
                    <a class="text-dark" href="{{ route('contact') }}"><i class="fa fa-angle-right mr-2"></i>Support</a>
                </div>
            </div>

            {{-- Col 3: Legal --}}
            <div class="col-lg-3 col-md-6 mb-5">
                <h5 class="font-weight-bold text-dark mb-4">Legal</h5>
                <p class="mb-3 text-dark"><i class="fa fa-store text-primary mr-2"></i>This website is owned &amp; operated by <strong>Sonam Collection</strong>.</p>
                <div class="d-flex flex-column justify-content-start">
                    <a class="text-dark mb-2" href="{{ route('legal.terms') }}"><i class="fa fa-angle-right mr-2"></i>Terms &amp; Conditions</a>
                    <a class="text-dark mb-2" href="{{ route('legal.privacy') }}"><i class="fa fa-angle-right mr-2"></i>Privacy Policy</a>
                    <a class="text-dark mb-2" href="{{ route('legal.refund') }}"><i class="fa fa-angle-right mr-2"></i>Return &amp; Refund Policy</a>
                    <a class="text-dark mb-2" href="{{ route('legal.shipping') }}"><i class="fa fa-angle-right mr-2"></i>Shipping Policy</a>
                    <a class="text-dark" href="{{ route('legal.cancellation') }}"><i class="fa fa-angle-right mr-2"></i>Cancellation Policy</a>
                </div>
            </div>

            {{-- Col 4: Newsletter --}}
            <div class="col-lg-3 col-md-6 mb-5">
                <h5 class="font-weight-bold text-dark mb-4">Newsletter</h5>
                @if(session('newsletter_success'))
                    <div class="alert alert-success py-2 small">{{ session('newsletter_success') }}</div>
                @endif
                <form action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="name" class="form-control border-0 py-4"
                               placeholder="Your Name" value="{{ old('name') }}">
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" class="form-control border-0 py-4 @error('email') is-invalid @enderror"
                               placeholder="Your Email *" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <button class="btn btn-primary btn-block border-0 py-3" type="submit">Subscribe Now</button>
                    </div>
                </form>
            </div>

        </div>

        {{-- Bottom bar --}}
        <div class="row border-top border-light mx-xl-5 py-4">
            <div class="col-md-6 px-xl-0">
                <p class="mb-md-0 text-center text-md-left text-dark">
                    &copy; {{ date('Y') }} <a class="text-dark font-weight-semi-bold" href="{{ url('/') }}">Jeanzo</a>. All Rights Reserved. |
                    A brand of <strong>Sonam Collection</strong>
                </p>
            </div>
            <div class="col-md-6 px-xl-0 text-center text-md-right">
                <small>
                    <a class="text-dark mr-3" href="{{ route('legal.terms') }}">Terms</a>
                    <a class="text-dark mr-3" href="{{ route('legal.privacy') }}">Privacy</a>
                    <a class="text-dark mr-3" href="{{ route('legal.refund') }}">Returns</a>
                    <a class="text-dark" href="{{ route('legal.shipping') }}">Shipping</a>
                </small>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('eshopper/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('eshopper/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('eshopper/js/main.js') }}"></script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-FKT9P78GTQ"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-FKT9P78GTQ');
    </script>
    @stack('scripts')
</body>
</html>
