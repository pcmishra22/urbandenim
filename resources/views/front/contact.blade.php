@extends('layouts.eshopper')
@section('title', 'Contact Us - Jeanzo')

@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Contact Us', 'breadcrumb' => 'Contact', 'showCategories' => false])

<div class="container-fluid pb-5" style="background:#faf8f8;padding-top:28px;">

    <div class="text-center mb-5 pt-2">
        <h2 class="font-weight-bold" style="color:#2d2d2d;">Contact For Any Queries</h2>
        <div class="mx-auto" style="width:50px;height:3px;background:var(--j-primary);border-radius:2px;margin-top:8px;"></div>
    </div>

    <div class="row px-xl-5">

        <div class="col-lg-7 mb-5">
            <div class="j-section">
                <div class="j-section-title"><i class="fa fa-paper-plane mr-2" style="color:var(--j-primary);"></i>Send Us a Message</div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Your Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Full Name" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-600">Your Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@example.com" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" placeholder="How can we help?" value="{{ old('subject') }}" required>
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="6" placeholder="Write your message here…" required>{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-primary px-5 py-2" type="submit" style="border-radius:8px;">
                        <i class="fa fa-paper-plane mr-2"></i>Send Message
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-5 mb-5">
            @php use App\Models\SiteSetting; $s = SiteSetting::all_settings(); @endphp
            <div class="j-section">
                <div class="j-section-title"><i class="fa fa-map-marker-alt mr-2" style="color:var(--j-primary);"></i>Get In Touch</div>
                <p class="text-muted mb-4" style="font-size:.9rem;">We'd love to hear from you. We'll respond within 24 hours.</p>

                @foreach([['fa-map-marker-alt','Address',$s['store_address'] ?? '123 Street, New York, USA'],['fa-envelope','Email',$s['store_email'] ?? 'info@jeanzo.in'],['fa-phone-alt','Phone',$s['store_phone'] ?? '+012 345 67890'],['fa-clock','Hours',$s['store_hours'] ?? 'Mon – Sat, 9am – 6pm']] as [$icon,$label,$val])
                <div class="d-flex align-items-start mb-3">
                    <div style="width:38px;height:38px;border-radius:50%;background:var(--j-primary-lt);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:14px;">
                        <i class="fa {{ $icon }}" style="color:var(--j-primary);"></i>
                    </div>
                    <div>
                        <div class="font-weight-700 mb-1" style="font-size:.9rem;">{{ $label }}</div>
                        <div class="text-muted small">{{ $val }}</div>
                    </div>
                </div>
                @endforeach

                <div class="d-flex mt-3" style="gap:10px;">
                    @if(!empty($s['twitter_url']))<a class="btn btn-primary btn-square" href="{{ $s['twitter_url'] }}" target="_blank"><i class="fab fa-twitter"></i></a>@endif
                    @if(!empty($s['facebook_url']))<a class="btn btn-primary btn-square" href="{{ $s['facebook_url'] }}" target="_blank"><i class="fab fa-facebook-f"></i></a>@endif
                    @if(!empty($s['linkedin_url']))<a class="btn btn-primary btn-square" href="{{ $s['linkedin_url'] }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>@endif
                    @if(!empty($s['instagram_url']))<a class="btn btn-primary btn-square" href="{{ $s['instagram_url'] }}" target="_blank"><i class="fab fa-instagram"></i></a>@endif
                    @if(empty($s['twitter_url']) && empty($s['facebook_url']) && empty($s['linkedin_url']) && empty($s['instagram_url']))
                        <a class="btn btn-primary btn-square" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-primary btn-square" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-primary btn-square" href="#"><i class="fab fa-instagram"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row px-xl-5">
        <div class="col-12">
            <div class="j-section p-0 overflow-hidden">
                <iframe style="width:100%;height:360px;border:0;display:block;"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.15830869428!2d-74.11976373946229!3d40.69766374874431!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sin!4v1623481043513!5m2!1sen!2sin"
                    allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection
