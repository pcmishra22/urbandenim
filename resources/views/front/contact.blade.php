@extends('layouts.eshopper')

@section('title', 'Contact Us - EShopper')

@section('content')

    <!-- Page Header Start -->
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">Contact Us</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">Contact</p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Contact Start -->
    <div class="container-fluid pt-5">
        <div class="text-center mb-4">
            <h2 class="section-title px-5"><span class="px-2">Contact For Any Queries</span></h2>
        </div>
        <div class="row px-xl-5">
            <div class="col-lg-7 mb-5">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif
                <div class="contact-form">
                    <form method="POST" action="{{ route('contact') }}">
                        @csrf
                        <div class="control-group mb-3">
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Your Name" value="{{ old('name') }}" required>
                            @error('name')<p class="help-block text-danger">{{ $message }}</p>@else<p class="help-block text-danger"></p>@enderror
                        </div>
                        <div class="control-group mb-3">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   placeholder="Your Email" value="{{ old('email') }}" required>
                            @error('email')<p class="help-block text-danger">{{ $message }}</p>@else<p class="help-block text-danger"></p>@enderror
                        </div>
                        <div class="control-group mb-3">
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                                   placeholder="Subject" value="{{ old('subject') }}" required>
                            @error('subject')<p class="help-block text-danger">{{ $message }}</p>@else<p class="help-block text-danger"></p>@enderror
                        </div>
                        <div class="control-group mb-3">
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror"
                                      rows="6" placeholder="Message" required>{{ old('message') }}</textarea>
                            @error('message')<p class="help-block text-danger">{{ $message }}</p>@else<p class="help-block text-danger"></p>@enderror
                        </div>
                        <div>
                            <button class="btn btn-primary py-2 px-4" type="submit">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5 mb-5">
                <h5 class="font-weight-semi-bold mb-3">Get In Touch</h5>
                <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible — usually within 24 hours.</p>
                <div class="d-flex flex-column mb-4">
                    <h5 class="font-weight-semi-bold mb-3">Head Office</h5>
                    <p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-3"></i>123 Street, New York, USA</p>
                    <p class="mb-2"><i class="fa fa-envelope text-primary mr-3"></i>info@eshopper.com</p>
                    <p class="mb-2"><i class="fa fa-phone-alt text-primary mr-3"></i>+012 345 67890</p>
                    <p class="mb-0"><i class="fa fa-clock text-primary mr-3"></i>Mon – Sat, 9am – 6pm</p>
                </div>
                <div class="d-flex flex-column mb-4">
                    <h5 class="font-weight-semi-bold mb-3">Support</h5>
                    <p class="mb-2"><i class="fa fa-envelope text-primary mr-3"></i>support@eshopper.com</p>
                    <p class="mb-0"><i class="fa fa-phone-alt text-primary mr-3"></i>+012 345 67891</p>
                </div>
                <div class="d-flex mt-2">
                    <a class="btn btn-primary btn-square mr-2" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-primary btn-square mr-2" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-primary btn-square mr-2" href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-primary btn-square" href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->

    <!-- Google Map -->
    <div class="container-fluid pb-5">
        <div class="row px-xl-5">
            <div class="col-12">
                <iframe style="width: 100%; height: 400px; border: 0;"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.15830869428!2d-74.11976373946229!3d40.69766374874431!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sin!4v1623481043513!5m2!1sen!2sin"
                    allowfullscreen="" loading="lazy">
                </iframe>
            </div>
        </div>
    </div>

@endsection
