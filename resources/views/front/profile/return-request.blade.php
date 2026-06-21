@extends('layouts.eshopper')
@section('title', 'Request Return — Order #' . $order->id)
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="container-fluid pb-5" style="background:#faf8f8;">
<div class="row px-xl-5 pt-4">
    @include('front.partials.profile-sidebar')

    <div class="col-lg-9 mb-5">
        <div class="d-flex align-items-center mb-4" style="gap:12px;">
            <a href="{{ route('profile.order-details', $order->id) }}" class="btn btn-outline-dark btn-sm">
                <i class="fa fa-arrow-left mr-1"></i>Back
            </a>
            <h5 class="font-weight-bold m-0">Request Return — Order #{{ $order->id }}</h5>
        </div>

        @if($errors->any())
        <div class="alert alert-danger py-2 mb-3">
            @foreach($errors->all() as $err)
                <div><i class="fa fa-exclamation-circle mr-1"></i>{{ $err }}</div>
            @endforeach
        </div>
        @endif

        {{-- 7-day window notice --}}
        <div class="alert alert-info py-2 mb-4" style="font-size:.88rem;">
            <i class="fa fa-info-circle mr-1"></i>
            Returns accepted within <strong>7 days</strong> of delivery.
            Your order was delivered on <strong>{{ $order->updated_at->format('d M Y') }}</strong>.
            Window closes: <strong>{{ $order->updated_at->addDays(7)->format('d M Y') }}</strong>.
        </div>

        {{-- Items in order --}}
        <div class="j-section mb-4">
            <div class="j-section-title"><i class="fa fa-box mr-2" style="color:var(--j-primary);"></i>Items in Order #{{ $order->id }}</div>
            @foreach($order->products as $product)
            <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="gap:12px;">
                @if($product->images && $product->images->isNotEmpty())
                    <img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}"
                         style="width:50px;height:50px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                @else
                    <div style="width:50px;height:50px;border-radius:8px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa fa-box" style="color:#ccc;"></i>
                    </div>
                @endif
                <div>
                    <div style="font-size:.9rem;font-weight:600;">{{ $product->name }}</div>
                    <small class="text-muted">Qty: {{ $product->pivot->quantity }} × ₹{{ number_format($product->pivot->price, 2) }}</small>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Return form --}}
        <div class="j-section">
            <div class="j-section-title"><i class="fa fa-undo mr-2" style="color:var(--j-primary);"></i>Return Details</div>

            <form method="POST" action="{{ route('profile.return.store', $order->id) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label font-weight-600">Return Type <span class="text-danger">*</span></label>
                    <div class="d-flex" style="gap:12px;flex-wrap:wrap;">
                        <label style="flex:1;min-width:140px;cursor:pointer;">
                            <input type="radio" name="type" value="return" {{ old('type','return')==='return'?'checked':'' }} required style="display:none;" class="type-radio">
                            <div class="type-card" data-val="return">
                                <i class="fa fa-undo fa-lg mb-2" style="color:#e74c3c;"></i>
                                <div style="font-weight:700;">Return & Refund</div>
                                <div style="font-size:.78rem;color:#888;">Send back the item, get your money back</div>
                            </div>
                        </label>
                        <label style="flex:1;min-width:140px;cursor:pointer;">
                            <input type="radio" name="type" value="exchange" {{ old('type')==='exchange'?'checked':'' }} style="display:none;" class="type-radio">
                            <div class="type-card" data-val="exchange">
                                <i class="fa fa-exchange-alt fa-lg mb-2" style="color:#3498db;"></i>
                                <div style="font-weight:700;">Exchange</div>
                                <div style="font-size:.78rem;color:#888;">Swap for a different size or colour</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label font-weight-600">Reason for Return <span class="text-danger">*</span></label>
                    <select class="form-control @error('reason') is-invalid @enderror" name="reason" required>
                        <option value="">— Select a reason —</option>
                        <option {{ old('reason')==='Wrong size'?'selected':'' }}>Wrong size</option>
                        <option {{ old('reason')==='Product not as described'?'selected':'' }}>Product not as described</option>
                        <option {{ old('reason')==='Defective / damaged product'?'selected':'' }}>Defective / damaged product</option>
                        <option {{ old('reason')==='Wrong item received'?'selected':'' }}>Wrong item received</option>
                        <option {{ old('reason')==='Poor quality'?'selected':'' }}>Poor quality</option>
                        <option {{ old('reason')==='Changed my mind'?'selected':'' }}>Changed my mind</option>
                        <option {{ old('reason')==='Other'?'selected':'' }}>Other</option>
                    </select>
                    @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label font-weight-600">Additional Details <span class="text-muted fw-normal">(optional)</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              name="description" rows="3"
                              placeholder="Please describe the issue in detail. This helps us process your return faster.">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="p-3 mb-4" style="background:#faf8f8;border-radius:10px;border:1px solid #eee;font-size:.83rem;color:#666;line-height:1.7;">
                    <strong>What happens next?</strong><br>
                    1. We review your request within 24–48 hours.<br>
                    2. If approved, we arrange a free reverse pickup from your address.<br>
                    3. Once we receive the item, your refund is processed within 5–7 business days.
                </div>

                <button type="submit" class="btn btn-primary px-5 py-2" style="font-weight:600;">
                    <i class="fa fa-paper-plane mr-2"></i>Submit Return Request
                </button>
            </form>
        </div>

    </div>
</div>
</div>

<style>
.type-card{
    border:2px solid #e0e0e0;border-radius:12px;padding:16px;text-align:center;
    transition:border-color .15s,background .15s;
}
.type-radio:checked + .type-card{
    border-color:var(--j-primary);background:#fff4f4;
}
</style>
<script>
document.querySelectorAll('.type-radio').forEach(function(radio){
    radio.addEventListener('change',function(){
        document.querySelectorAll('.type-card').forEach(function(c){c.style.borderColor='#e0e0e0';c.style.background='';});
        if(this.checked) {
            this.nextElementSibling.style.borderColor='var(--j-primary)';
            this.nextElementSibling.style.background='#fff4f4';
        }
    });
    if(radio.checked){
        radio.nextElementSibling.style.borderColor='var(--j-primary)';
        radio.nextElementSibling.style.background='#fff4f4';
    }
});
</script>
@endsection
