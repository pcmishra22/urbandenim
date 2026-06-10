@extends('layouts.dashboard')
@section('title', 'Site Settings')
@section('content')
<div class="page-title"><h2><i class="fas fa-cog"></i> Site Settings</h2></div>

@if(session('success'))
    <div class="alert alert-success mt-3 alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card mt-3">
    <div class="card-body">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf @method('PUT')

            <h5 class="border-bottom pb-2 mb-4"><i class="fab fa-facebook me-2 text-primary"></i>Social Media Links</h5>
            <div class="row">
                @foreach(['facebook'=>'fab fa-facebook text-primary','twitter'=>'fab fa-twitter text-info','instagram'=>'fab fa-instagram text-danger','linkedin'=>'fab fa-linkedin text-primary','youtube'=>'fab fa-youtube text-danger'] as $key => $icon)
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="{{ $icon }} me-2"></i>{{ ucfirst($key) }} URL</label>
                    <input type="url" name="{{ $key }}_url" class="form-control"
                           value="{{ $settings[$key.'_url'] ?? '' }}" placeholder="https://{{ $key }}.com/yourpage">
                </div>
                @endforeach
            </div>

            <h5 class="border-bottom pb-2 mb-4 mt-4"><i class="fas fa-store me-2"></i>Store Information</h5>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Store Address</label>
                    <input type="text" name="store_address" class="form-control" value="{{ $settings['store_address'] ?? '' }}" placeholder="123 Main St, City, State - PIN">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Store Phone</label>
                    <input type="text" name="store_phone" class="form-control" value="{{ $settings['store_phone'] ?? '' }}" placeholder="+91-XXXXX-XXXXX">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Store Email</label>
                    <input type="email" name="store_email" class="form-control" value="{{ $settings['store_email'] ?? '' }}" placeholder="info@Jeanzo.in">
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Settings</button>
        </form>
    </div>
</div>
@endsection
