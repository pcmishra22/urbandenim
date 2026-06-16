@extends('layouts.eshopper')
@section('title', 'Order Confirmed — Jeanzo')

@section('content')
@include('front.partials.design-system')

{{-- ── Confirmation hero ── --}}
<div style="background:linear-gradient(135deg,var(--j-primary-lt) 0%,#fff 100%);padding:48px 20px 32px;text-align:center;">
    <div style="width:72px;height:72px;border-radius:50%;background:var(--j-primary);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;box-shadow:0 4px 20px rgba(209,156,151,.4);">
        <i class="fa fa-check" style="color:#fff;font-size:32px;"></i>
    </div>
    <h2 style="font-weight:800;color:var(--j-dark);margin-bottom:6px;">
        @if($order->payment_method === 'cod')
            Order Placed Successfully!
        @else
            Payment Successful!
        @endif
    </h2>
    <p style="color:var(--j-muted);font-size:1rem;margin-bottom:4px;">
        @if($order->payment_method === 'cod')
            Your order <strong>#{{ $order->id }}</strong> is confirmed. Pay <strong>₹{{ number_format($order->total_price,2) }}</strong> on delivery.
        @else
            Payment of <strong>₹{{ number_format($order->total_price,2) }}</strong> received for Order <strong>#{{ $order->id }}</strong>.
        @endif
    </p>
    <p style="color:var(--j-muted);font-size:.88rem;">A confirmation email has been sent to your registered email address.</p>

    @if(session('success'))
        <div class="alert alert-success d-inline-block px-4 py-2 mt-2" style="border-radius:8px;font-size:.88rem;">
            <i class="fa fa-check-circle mr-1"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-inline-block px-4 py-2 mt-2" style="border-radius:8px;font-size:.88rem;">
            <i class="fa fa-exclamation-circle mr-1"></i>{{ session('error') }}
        </div>
    @endif
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- ── Status strip ── --}}
            <div class="row text-center mb-4">
                @php
                    $cards = [
                        ['label'=>'Order ID',    'value'=>'#'.$order->id,                          'icon'=>'fa-hashtag'],
                        ['label'=>'Date',        'value'=>$order->created_at->format('d M Y'),      'icon'=>'fa-calendar-alt'],
                        ['label'=>'Payment',     'value'=>match($order->payment_method){
                            'cod'  => 'Cash on Delivery',
                            'upi'  => 'UPI / Net Banking',
                            default=> 'Card'
                        },                                                                           'icon'=>'fa-credit-card'],
                        ['label'=>'Status',      'value'=>ucfirst($order->status),                  'icon'=>'fa-truck'],
                    ];
                @endphp
                @foreach($cards as $c)
                <div class="col-6 col-md-3 mb-3">
                    <div class="j-section h-100 py-3 text-center" style="margin-bottom:0;">
                        <i class="fa {{ $c['icon'] }} mb-2 d-block" style="color:var(--j-primary);font-size:1.3rem;"></i>
                        <small class="text-muted d-block mb-1">{{ $c['label'] }}</small>
                        <strong style="font-size:.9rem;">{{ $c['value'] }}</strong>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ── Items ordered ── --}}
            <div class="j-section mb-4" style="padding:0;overflow:hidden;">
                <div class="px-4 py-3" style="border-bottom:1.5px solid var(--j-border);">
                    <span style="font-weight:700;font-size:1rem;color:var(--j-dark);">
                        <i class="fa fa-shopping-bag mr-2" style="color:var(--j-primary);"></i>Items Ordered
                    </span>
                </div>

                @foreach($order->products as $product)
                <div class="d-flex align-items-center px-4 py-3" style="border-bottom:1px solid var(--j-border);">
                    {{-- Product image --}}
                    <div style="width:60px;height:60px;border-radius:8px;overflow:hidden;flex-shrink:0;border:1.5px solid var(--j-border);">
                        @php $img = $product->images && $product->images->isNotEmpty() ? $product->images->first()->url : asset('eshopper/img/product-1.jpg'); @endphp
                        <img src="{{ $img }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    {{-- Name + qty --}}
                    <div class="ml-3 flex-grow-1">
                        <div style="font-weight:600;font-size:.9rem;color:var(--j-dark);">{{ $product->name }}</div>
                        <small class="text-muted">Qty: {{ $product->pivot->quantity }}
                            @if($product->pivot->quantity > 1)
                                × ₹{{ number_format($product->pivot->price,2) }}
                            @endif
                        </small>
                    </div>
                    {{-- Line total --}}
                    <div style="font-weight:700;color:var(--j-dark);white-space:nowrap;">
                        ₹{{ number_format($product->pivot->quantity * $product->pivot->price, 2) }}
                    </div>
                </div>
                @endforeach

                {{-- Totals --}}
                <div class="px-4 py-3">
                    <div class="d-flex justify-content-between mb-2" style="font-size:.9rem;">
                        <span class="text-muted">Subtotal</span>
                        <span>₹{{ number_format($order->subtotal,2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" style="font-size:.9rem;">
                        <span class="text-muted">Shipping</span>
                        <span>
                            @if($order->shipping_cost == 0)
                                <span class="text-success font-weight-700">FREE</span>
                            @else
                                ₹{{ number_format($order->shipping_cost,2) }}
                            @endif
                        </span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success" style="font-size:.9rem;">
                        <span>Discount @if($order->coupon_code)<small class="text-muted">({{ $order->coupon_code }})</small>@endif</span>
                        <span>− ₹{{ number_format($order->discount_amount,2) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between pt-2" style="border-top:1.5px solid var(--j-border);font-weight:800;font-size:1.05rem;">
                        <span style="color:var(--j-dark);">Total</span>
                        <span style="color:var(--j-primary);">₹{{ number_format($order->total_price,2) }}</span>
                    </div>
                </div>
            </div>

            {{-- ── Two column: address + what's next ── --}}
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="j-section h-100" style="margin-bottom:0;">
                        <div style="font-weight:700;margin-bottom:12px;font-size:.95rem;">
                            <i class="fa fa-map-marker-alt mr-2" style="color:var(--j-primary);"></i>Shipping To
                        </div>
                        <div style="font-size:.88rem;line-height:1.7;color:var(--j-dark);">
                            <strong>{{ $order->shipping_full_name }}</strong><br>
                            {{ $order->shipping_street }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} – {{ $order->shipping_postal_code }}<br>
                            {{ $order->shipping_country }}<br>
                            <i class="fa fa-phone mr-1 text-muted"></i>{{ $order->shipping_phone }}
                        </div>
                        @if($order->notes)
                        <div class="mt-2 pt-2" style="border-top:1px solid var(--j-border);font-size:.82rem;color:var(--j-muted);">
                            <i class="fa fa-sticky-note mr-1"></i>{{ $order->notes }}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="j-section h-100" style="margin-bottom:0;">
                        <div style="font-weight:700;margin-bottom:12px;font-size:.95rem;">
                            <i class="fa fa-info-circle mr-2" style="color:var(--j-primary);"></i>What Happens Next
                        </div>
                        <div style="font-size:.86rem;line-height:1.8;color:var(--j-muted);">
                            @if($order->payment_method === 'cod')
                            <div class="mb-1"><i class="fa fa-check-circle mr-2 text-success"></i>Order confirmed &amp; placed</div>
                            <div class="mb-1"><i class="fa fa-circle mr-2" style="color:var(--j-primary);"></i>We'll pack your items</div>
                            <div class="mb-1"><i class="fa fa-circle mr-2" style="color:var(--j-primary);"></i>Shipped in 1–2 business days</div>
                            <div class="mb-1"><i class="fa fa-circle mr-2" style="color:var(--j-primary);"></i>Delivered in 3–7 business days</div>
                            <div><i class="fa fa-circle mr-2" style="color:var(--j-primary);"></i>Pay <strong>₹{{ number_format($order->total_price,2) }}</strong> on delivery</div>
                            @else
                            <div class="mb-1"><i class="fa fa-check-circle mr-2 text-success"></i>Payment received</div>
                            <div class="mb-1"><i class="fa fa-check-circle mr-2 text-success"></i>Order confirmed</div>
                            <div class="mb-1"><i class="fa fa-circle mr-2" style="color:var(--j-primary);"></i>We'll pack your items</div>
                            <div class="mb-1"><i class="fa fa-circle mr-2" style="color:var(--j-primary);"></i>Shipped in 1–2 business days</div>
                            <div><i class="fa fa-circle mr-2" style="color:var(--j-primary);"></i>Delivered in 3–7 business days</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Action buttons ── --}}
            <div class="d-flex justify-content-between flex-wrap" style="gap:12px;">
                <a href="{{ route('products.index') }}"
                   class="btn btn-outline-primary px-4 py-3"
                   style="border-radius:10px;font-weight:600;">
                    <i class="fa fa-shopping-bag mr-2"></i>Continue Shopping
                </a>
                <a href="{{ route('profile.order-details', $order->id) }}"
                   class="btn btn-primary px-4 py-3"
                   style="border-radius:10px;font-weight:600;">
                    <i class="fa fa-list mr-2"></i>View Order Details
                </a>
            </div>

        </div>
    </div>
</div>

@endsection
