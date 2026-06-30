@extends('layouts.eshopper')
@section('title', 'About Jeanzo — Factory-Direct Premium Denim for Men & Women India')
@section('meta_description', 'Jeanzo is India\'s factory-direct premium denim brand for men and women. Our story, our values, and why thousands of Indians choose Jeanzo for quality jeans at honest prices.')
@section('canonical', route('about'))
@section('og_title', 'About Jeanzo — Factory-Direct Premium Denim India')
@section('og_description', 'Premium denim for men & women, straight from the factory. No middlemen, no markups. COD, free shipping & easy 7-day returns across India.')

@push('json_ld')
<script type="application/ld+json">
@verbatim
{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@type": "AboutPage",
            "@id": "https://jeanzo.in/about#webpage",
            "url": "https://jeanzo.in/about",
            "name": "About Jeanzo — Factory-Direct Premium Denim India",
            "description": "Jeanzo is India's factory-direct premium denim brand for men and women, run by Sonam Collection.",
            "isPartOf": { "@id": "https://jeanzo.in/#website" },
            "about": { "@id": "https://jeanzo.in/#organization" },
            "inLanguage": "en-IN",
            "breadcrumb": {
                "@type": "BreadcrumbList",
                "itemListElement": [
                    { "@type": "ListItem", "position": 1, "name": "Home",     "item": "https://jeanzo.in/" },
                    { "@type": "ListItem", "position": 2, "name": "About Us", "item": "https://jeanzo.in/about" }
                ]
            }
        }
    ]
}
@endverbatim
</script>
@endpush

@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'About Us', 'breadcrumb' => 'About Us', 'showCategories' => false])

<style>
.about-section { padding: 60px 0; }
.about-stat-card {
    border: 2px solid #f0eded;
    text-align: center;
    padding: 28px 20px;
    transition: border-color .2s;
}
.about-stat-card:hover { border-color: #D19C97; }
.about-stat-card .stat-num {
    font-size: 2.2rem;
    font-weight: 900;
    color: #D19C97;
    line-height: 1;
    margin-bottom: 6px;
}
.about-stat-card .stat-label {
    font-size: .8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #555;
}
.about-value-card {
    border-left: 3px solid #D19C97;
    padding: 16px 20px;
    margin-bottom: 16px;
    background: #fdf9f9;
}
.about-value-card h5 {
    font-size: .9rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 4px;
    color: #1a1a1a;
}
.about-value-card p {
    font-size: .85rem;
    color: #666;
    margin-bottom: 0;
    line-height: 1.6;
}
.about-trust-badge {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 6px;
    margin-bottom: 12px;
}
.about-trust-badge i { color: #D19C97; font-size: 1.2rem; flex-shrink: 0; }
.about-trust-badge span { font-size: .84rem; color: #444; line-height: 1.4; }
</style>

<div class="container-fluid pt-5 pb-3">
    <div class="row px-xl-5 align-items-center mb-5">
        <div class="col-lg-5 mb-4 mb-lg-0">
            <img src="{{ asset('eshopper/img/about.jpg') }}" class="img-fluid w-100"
                 alt="Jeanzo — Factory-Direct Premium Denim India"
                 style="object-fit:cover; max-height:480px;"
                 onerror="this.src='https://via.placeholder.com/600x480?text=Jeanzo+Denim'">
        </div>
        <div class="col-lg-7 pl-lg-5">
            <p class="mb-2" style="font-size:.65rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:#D19C97;">Our Story</p>
            <h1 class="font-weight-bold mb-4" style="font-size:clamp(1.6rem,3.5vw,2.4rem);line-height:1.15;color:#1a1a1a;">
                Premium Denim,<br>Straight From the Factory Floor
            </h1>

            <p class="mb-3" style="font-size:.95rem;line-height:1.8;color:#444;">
                Jeanzo was born from a simple frustration: why does a great pair of jeans cost ₹3,000 in a mall when the same quality fabric, cut, and stitching can leave the factory for a fraction of that price? We decided to do something about it.
            </p>
            <p class="mb-3" style="font-size:.95rem;line-height:1.8;color:#444;">
                Founded under <strong>Sonam Collection</strong>, a family-run textile business with over a decade of experience sourcing and supplying denim across India, Jeanzo cuts out every middleman between the factory and your wardrobe. No brand markup. No distributor margin. No retailer premium. Just honest, well-made denim delivered to your door — starting at ₹987.
            </p>
            <p class="mb-3" style="font-size:.95rem;line-height:1.8;color:#444;">
                We offer jeans for <strong>men and women</strong> — slim fit, straight fit, regular fit, wide leg, bootcut, skinny, and high-rise — crafted from quality denim fabric with reinforced stitching and hardware that lasts. Every pair goes through quality checks before it's packed and shipped.
            </p>
            <p class="mb-4" style="font-size:.95rem;line-height:1.8;color:#444;">
                Our promise is simple: if the jeans aren't right for you, send them back within 7 days, no questions asked. We offer Cash on Delivery across India, FREE shipping, and real customer support — 7 days a week — because we know buying clothes online requires trust, and we take that seriously.
            </p>

            {{-- Stats --}}
            <div class="row">
                <div class="col-6 col-md-3 mb-3">
                    <div class="about-stat-card">
                        <div class="stat-num">₹987</div>
                        <div class="stat-label">Starting Price</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="about-stat-card">
                        <div class="stat-num">7</div>
                        <div class="stat-label">Day Returns</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="about-stat-card">
                        <div class="stat-num">COD</div>
                        <div class="stat-label">Available</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="about-stat-card">
                        <div class="stat-num">Pan</div>
                        <div class="stat-label">India Delivery</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Our Values --}}
<div class="container-fluid" style="background:#fdf9f9; padding:52px 0;">
    <div class="row px-xl-5">
        <div class="col-12 text-center mb-4">
            <p style="font-size:.65rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:#D19C97;">What We Stand For</p>
            <h2 class="font-weight-bold" style="font-size:clamp(1.3rem,3vw,1.85rem);color:#1a1a1a;">Our Values</h2>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="about-value-card">
                <h5><i class="fa fa-cut mr-2" style="color:#D19C97;"></i>Quality First</h5>
                <p>Every pair is stitched with reinforced seams, quality hardware, and sanforized denim fabric — checked before it leaves our facility.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="about-value-card">
                <h5><i class="fa fa-rupee-sign mr-2" style="color:#D19C97;"></i>Honest Pricing</h5>
                <p>No artificial markups. Factory-direct means you pay for the product, not the distribution chain. Premium denim at prices that make sense.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="about-value-card">
                <h5><i class="fa fa-users mr-2" style="color:#D19C97;"></i>For Everyone</h5>
                <p>Denim for men and women, across every fit — slim, straight, wide leg, bootcut, skinny and regular. If it's denim, we have your size.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="about-value-card">
                <h5><i class="fa fa-handshake mr-2" style="color:#D19C97;"></i>Customer First</h5>
                <p>Real support, easy returns, COD availability. We make it risk-free to try Jeanzo because we're confident you'll keep coming back.</p>
            </div>
        </div>
    </div>
</div>

{{-- Trust & Business Details --}}
<div class="container-fluid py-5">
    <div class="row px-xl-5">
        <div class="col-lg-6 mb-4">
            <p style="font-size:.65rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:#D19C97;">Why Trust Us</p>
            <h2 class="font-weight-bold mb-4" style="font-size:1.5rem;color:#1a1a1a;">Shopping With Jeanzo Is Safe</h2>
            <div class="about-trust-badge">
                <i class="fa fa-undo"></i>
                <span><strong>7-Day No-Questions Return Policy.</strong> Not happy with fit or quality? Return it within 7 days for a full refund.</span>
            </div>
            <div class="about-trust-badge">
                <i class="fa fa-money-bill-wave"></i>
                <span><strong>Cash on Delivery available</strong> across India. Pay when you receive your order — no upfront risk.</span>
            </div>
            <div class="about-trust-badge">
                <i class="fa fa-shipping-fast"></i>
                <span><strong>Free shipping on every order.</strong> No minimum order value. We ship pan-India.</span>
            </div>
            <div class="about-trust-badge">
                <i class="fa fa-lock"></i>
                <span><strong>100% secure payments</strong> via Razorpay — UPI, Cards, Net Banking, and Wallets accepted.</span>
            </div>
            <div class="about-trust-badge">
                <i class="fa fa-headset"></i>
                <span><strong>Real customer support, 7 days a week.</strong> Email us at <a href="mailto:support@jeanzo.in">support@jeanzo.in</a> or WhatsApp us.</span>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <p style="font-size:.65rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:#D19C97;">Business Information</p>
            <h2 class="font-weight-bold mb-4" style="font-size:1.5rem;color:#1a1a1a;">Our Details</h2>
            <table class="table table-borderless" style="font-size:.88rem;">
                <tbody>
                    <tr>
                        <td class="font-weight-bold text-muted" style="width:140px;">Legal Name</td>
                        <td><strong>Sonam Collection</strong></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-muted">Brand Name</td>
                        <td>Jeanzo.in</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-muted">GST Number</td>
                        <td><strong>03BHHPS7451G1ZL</strong></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-muted">Email</td>
                        <td><a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-muted">Phone / WhatsApp</td>
                        <td><a href="tel:+917340753780" class="text-primary">+91 73407 53780</a></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-muted">Address</td>
                        <td>Punjab, India</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-muted">Returns</td>
                        <td>7-day hassle-free returns</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-muted">Delivery</td>
                        <td>Free shipping, pan-India</td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-3">
                <a href="{{ route('contact') }}" class="btn btn-primary mr-2">Contact Us</a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-dark">Shop Now</a>
            </div>
        </div>
    </div>
</div>

@endsection
