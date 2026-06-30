@php
    use App\Models\SiteSetting;
    $s = SiteSetting::all_settings();
@endphp

    <!-- Footer Start -->
    <footer class="container-fluid bg-secondary text-dark mt-5 pt-5" itemscope itemtype="https://schema.org/WPFooter">

        {{-- ── Top: Trust Badges Strip ── --}}
        <div class="row px-xl-5 pb-3 border-bottom border-light mx-xl-0" style="border-color:#d0cece !important;">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-center align-items-center" style="gap:20px 40px; padding:10px 0 6px;">
                    <div class="d-flex align-items-center" style="gap:8px; font-size:.82rem; color:#333;">
                        <i class="fa fa-shipping-fast text-primary" style="font-size:1.1rem;"></i>
                        <span><strong>Free Shipping</strong> on orders ₹500+</span>
                    </div>
                    <div class="d-flex align-items-center" style="gap:8px; font-size:.82rem; color:#333;">
                        <i class="fa fa-money-bill-wave text-primary" style="font-size:1.1rem;"></i>
                        <span><strong>Cash on Delivery</strong> available</span>
                    </div>
                    <div class="d-flex align-items-center" style="gap:8px; font-size:.82rem; color:#333;">
                        <i class="fa fa-undo text-primary" style="font-size:1.1rem;"></i>
                        <span><strong>7-Day Easy Returns</strong></span>
                    </div>
                    <div class="d-flex align-items-center" style="gap:8px; font-size:.82rem; color:#333;">
                        <i class="fa fa-lock text-primary" style="font-size:1.1rem;"></i>
                        <span><strong>100% Secure</strong> Payments</span>
                    </div>
                    <div class="d-flex align-items-center" style="gap:8px; font-size:.82rem; color:#333;">
                        <i class="fa fa-headset text-primary" style="font-size:1.1rem;"></i>
                        <span><strong>Customer Support</strong> 7 days</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row px-xl-5 pt-5">

            {{-- Col 1: Brand + Description + Social --}}
            <div class="col-lg-3 col-md-6 mb-5 pr-3 pr-xl-5 footer-col" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
                <a href="{{ url('/') }}" class="text-decoration-none" itemprop="url">
                    <h2 class="mb-3" style="font-size:1.8rem; font-weight:800; letter-spacing:-0.5px;" itemprop="name">
                        <span class="text-primary font-weight-bold border border-dark px-2 mr-1">J</span>eanzo
                    </h2>
                </a>
                <p class="mb-2 small" itemprop="description">India's premium factory-direct denim brand for men &amp; women. Shop slim fit, straight fit, wide leg, bootcut &amp; skinny jeans. Free delivery · COD · 7-day returns.</p>
                <p class="mb-1 small"><i class="fa fa-envelope text-primary mr-2"></i><a href="mailto:{{ $s['store_email'] ?? 'support@jeanzo.in' }}" class="text-dark" itemprop="email">{{ $s['store_email'] ?? 'support@jeanzo.in' }}</a></p>
                <p class="mb-1 small"><i class="fa fa-phone-alt text-primary mr-2"></i><a href="tel:{{ preg_replace('/\D/','',$s['store_phone'] ?? '') }}" class="text-dark" itemprop="telephone">{{ $s['store_phone'] ?? '+91 73407 53780' }}</a></p>
                <p class="mb-1 small"><i class="fa fa-map-marker-alt text-primary mr-2"></i><span itemprop="address">{{ $s['store_address'] ?? 'India' }}</span></p>
                <p class="mb-3 small text-muted"><i class="fa fa-file-invoice text-primary mr-2"></i>GST No: <strong>{{ $s['gst_number'] ?? '03BHHPS7451G1ZL' }}</strong></p>
                <div class="d-flex mt-2" style="gap:8px;">
                    @if(!empty($s['instagram_url']))<a class="btn btn-primary btn-square" href="{{ $s['instagram_url'] }}" target="_blank" rel="noopener" aria-label="Jeanzo on Instagram"><i class="fab fa-instagram"></i></a>@endif
                    @if(!empty($s['facebook_url']))<a class="btn btn-primary btn-square" href="{{ $s['facebook_url'] }}" target="_blank" rel="noopener" aria-label="Jeanzo on Facebook"><i class="fab fa-facebook-f"></i></a>@endif
                    @if(!empty($s['twitter_url']))<a class="btn btn-primary btn-square" href="{{ $s['twitter_url'] }}" target="_blank" rel="noopener" aria-label="Jeanzo on Twitter"><i class="fab fa-twitter"></i></a>@endif
                    @if(!empty($s['youtube_url']))<a class="btn btn-primary btn-square" href="{{ $s['youtube_url'] }}" target="_blank" rel="noopener" aria-label="Jeanzo on YouTube"><i class="fab fa-youtube"></i></a>@endif
                </div>
            </div>

            {{-- Col 2: Shop by Fit (NEW — critical for SEO category linking) --}}
            <div class="col-lg-2 col-md-6 mb-5 footer-col">
                <h5 class="font-weight-bold text-dark mb-4" style="font-size:.95rem; letter-spacing:.3px; text-transform:uppercase;">Shop by Fit</h5>
                <nav aria-label="Shop by fit">
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-dark mb-2 small" href="{{ route('products.index', ['category_name' => 'Slim Fit']) }}"><i class="fa fa-angle-right mr-2"></i>Slim Fit Jeans</a>
                        <a class="text-dark mb-2 small" href="{{ route('products.index', ['category_name' => 'Straight Fit']) }}"><i class="fa fa-angle-right mr-2"></i>Straight Fit Jeans</a>
                        <a class="text-dark mb-2 small" href="{{ route('products.index', ['category_name' => 'Regular Fit']) }}"><i class="fa fa-angle-right mr-2"></i>Regular Fit Jeans</a>
                        <a class="text-dark mb-2 small" href="{{ route('products.index', ['category_name' => 'Wide Leg']) }}"><i class="fa fa-angle-right mr-2"></i>Wide Leg Jeans</a>
                        <a class="text-dark mb-2 small" href="{{ route('products.index', ['category_name' => 'Bootcut']) }}"><i class="fa fa-angle-right mr-2"></i>Bootcut Jeans</a>
                        <a class="text-dark mb-2 small" href="{{ route('products.index', ['category_name' => 'Skinny']) }}"><i class="fa fa-angle-right mr-2"></i>Skinny Jeans</a>
                        <a class="text-dark mb-2 small" href="{{ route('products.index') }}"><i class="fa fa-angle-right mr-2"></i>All Jeans</a>
                    </div>
                </nav>
            </div>

            {{-- Col 3: Quick Links + My Account --}}
            <div class="col-lg-2 col-md-6 mb-5 footer-col">
                <h5 class="font-weight-bold text-dark mb-4" style="font-size:.95rem; letter-spacing:.3px; text-transform:uppercase;">Company</h5>
                <nav aria-label="Company links">
                    <div class="d-flex flex-column justify-content-start mb-4">
                        <a class="text-dark mb-2 small" href="{{ url('/') }}"><i class="fa fa-angle-right mr-2"></i>Home</a>
                        <a class="text-dark mb-2 small" href="{{ route('about') }}"><i class="fa fa-angle-right mr-2"></i>About Us</a>
                        <a class="text-dark mb-2 small" href="{{ route('blog.index') }}"><i class="fa fa-angle-right mr-2"></i>Denim Style Blog</a>
                        <a class="text-dark mb-2 small" href="{{ route('faq') }}"><i class="fa fa-angle-right mr-2"></i>FAQs</a>
                        <a class="text-dark mb-2 small" href="{{ route('help') }}"><i class="fa fa-angle-right mr-2"></i>Help Center</a>
                        <a class="text-dark mb-2 small" href="{{ route('contact') }}"><i class="fa fa-angle-right mr-2"></i>Contact Us</a>
                        <a class="text-dark mb-2 small" href="{{ route('profile.orders') }}"><i class="fa fa-angle-right mr-2"></i>Track My Order</a>
                        <a class="text-dark small" href="/sitemap.xml" rel="nofollow"><i class="fa fa-angle-right mr-2"></i>Sitemap</a>
                    </div>
                </nav>

                <h5 class="font-weight-bold text-dark mb-3" style="font-size:.95rem; letter-spacing:.3px; text-transform:uppercase;">My Account</h5>
                <nav aria-label="Account links">
                    <div class="d-flex flex-column justify-content-start">
                        @auth
                        <a class="text-dark mb-2 small" href="{{ route('profile.dashboard') }}"><i class="fa fa-angle-right mr-2"></i>My Profile</a>
                        <a class="text-dark mb-2 small" href="{{ route('profile.orders') }}"><i class="fa fa-angle-right mr-2"></i>My Orders</a>
                        <a class="text-dark small" href="{{ route('wishlist.index') }}"><i class="fa fa-angle-right mr-2"></i>Wishlist</a>
                        @else
                        <a class="text-dark mb-2 small" href="{{ route('customer.login') }}"><i class="fa fa-angle-right mr-2"></i>Login</a>
                        <a class="text-dark small" href="{{ route('customer.register') }}"><i class="fa fa-angle-right mr-2"></i>Create Account</a>
                        @endauth
                    </div>
                </nav>
            </div>

            {{-- Col 4: Legal + Policies --}}
            <div class="col-lg-2 col-md-6 mb-5 footer-col">
                <h5 class="font-weight-bold text-dark mb-4" style="font-size:.95rem; letter-spacing:.3px; text-transform:uppercase;">Policies</h5>
                <nav aria-label="Legal and policy links">
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-dark mb-2 small" href="{{ route('legal.terms') }}"><i class="fa fa-angle-right mr-2"></i>Terms &amp; Conditions</a>
                        <a class="text-dark mb-2 small" href="{{ route('legal.privacy') }}"><i class="fa fa-angle-right mr-2"></i>Privacy Policy</a>
                        <a class="text-dark mb-2 small" href="{{ route('legal.refund') }}"><i class="fa fa-angle-right mr-2"></i>Return &amp; Refund Policy</a>
                        <a class="text-dark mb-2 small" href="{{ route('legal.shipping') }}"><i class="fa fa-angle-right mr-2"></i>Shipping Policy</a>
                        <a class="text-dark small" href="{{ route('legal.cancellation') }}"><i class="fa fa-angle-right mr-2"></i>Cancellation Policy</a>
                    </div>
                </nav>
                <p class="mt-4 mb-0 small text-muted"><i class="fa fa-store text-primary mr-1"></i>Owned &amp; operated by <strong>Sonam Collection</strong>.</p>
            </div>

            {{-- Col 5: Newsletter --}}
            <div class="col-lg-3 col-md-6 mb-5 footer-col">
                <h5 class="font-weight-bold text-dark mb-3" style="font-size:.95rem; letter-spacing:.3px; text-transform:uppercase;">Get Style Updates</h5>
                <p class="small text-muted mb-3">New arrivals, style tips and exclusive offers — straight to your inbox.</p>
                @if(session('newsletter_success'))
                    <div class="alert alert-success py-2 small">{{ session('newsletter_success') }}</div>
                @endif
                <form action="{{ route('newsletter.subscribe') }}" method="POST" aria-label="Newsletter signup">
                    @csrf
                    <div class="form-group mb-2">
                        <input type="text" name="name" class="form-control border-0 py-3 small"
                               placeholder="Your Name" value="{{ old('name') }}" autocomplete="name">
                    </div>
                    <div class="form-group mb-2">
                        <input type="email" name="email" class="form-control border-0 py-3 small @error('email') is-invalid @enderror"
                               placeholder="Your Email *" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-primary btn-block border-0 py-3 font-weight-bold" type="submit">
                        <i class="fa fa-paper-plane mr-2"></i>Subscribe
                    </button>
                </form>

                {{-- Payment Icons --}}
                <div class="mt-4">
                    <p class="small font-weight-bold text-dark mb-2">We Accept</p>
                    <div class="d-flex flex-wrap align-items-center" style="gap:8px;">
                        <span class="badge badge-light border" style="font-size:.72rem; padding:5px 8px;"><i class="fa fa-money-bill-wave mr-1 text-success"></i>COD</span>
                        <span class="badge badge-light border" style="font-size:.72rem; padding:5px 8px;"><i class="fa fa-mobile-alt mr-1 text-primary"></i>UPI</span>
                        <span class="badge badge-light border" style="font-size:.72rem; padding:5px 8px;"><i class="fa fa-credit-card mr-1 text-primary"></i>Card</span>
                        <span class="badge badge-light border" style="font-size:.72rem; padding:5px 8px;"><i class="fa fa-university mr-1 text-primary"></i>Net Banking</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- SEO text block for crawlers --}}
        <div class="row px-xl-5 pb-2">
            <div class="col-12">
                <p class="small text-muted mb-0" style="font-size:.72rem; line-height:1.6;">
                    <strong>Jeanzo</strong> is India's premium online destination for men's denim jeans.
                    Shop <a href="{{ route('products.index', ['category_name'=>'Slim Fit']) }}" class="text-muted">slim fit jeans</a>,
                    <a href="{{ route('products.index', ['category_name'=>'Straight Fit']) }}" class="text-muted">straight fit jeans</a>,
                    <a href="{{ route('products.index', ['category_name'=>'Regular Fit']) }}" class="text-muted">regular fit jeans</a>,
                    <a href="{{ route('products.index', ['category_name'=>'Wide Leg']) }}" class="text-muted">wide leg jeans</a>,
                    <a href="{{ route('products.index', ['category_name'=>'Bootcut']) }}" class="text-muted">bootcut jeans</a> and
                    <a href="{{ route('products.index', ['category_name'=>'Skinny']) }}" class="text-muted">skinny jeans</a> online in India.
                    Free shipping on orders above ₹500 · Cash on Delivery available · Easy 7-day returns · Pan-India delivery.
                </p>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="row border-top border-light mx-xl-5 py-4">
            <div class="col-md-6 px-xl-0">
                <p class="mb-md-0 text-center text-md-left text-dark small">
                    &copy; {{ date('Y') }} <a class="text-dark font-weight-bold" href="{{ url('/') }}">Jeanzo.in</a>. All Rights Reserved. · A brand of <strong>Sonam Collection</strong>
                </p>
            </div>
            <div class="col-md-6 px-xl-0 text-center text-md-right">
                <small>
                    <a class="text-dark mr-3" href="{{ route('legal.terms') }}">Terms</a>
                    <a class="text-dark mr-3" href="{{ route('legal.privacy') }}">Privacy</a>
                    <a class="text-dark mr-3" href="{{ route('legal.refund') }}">Returns</a>
                    <a class="text-dark mr-3" href="{{ route('legal.shipping') }}">Shipping</a>
                    <a class="text-dark" href="{{ route('faq') }}">FAQs</a>
                </small>
            </div>
        </div>
    </footer>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <style>
        #wa-tooltip { animation: wa-pulse 3s ease-in-out infinite; }
        @keyframes wa-pulse {
            0%,100% { opacity:1; transform:translateY(0); }
            50%      { opacity:.7; transform:translateY(-4px); }
        }
    </style>

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
    <!--@include('front.partials.exit-popup')-->
</body>
</html>
