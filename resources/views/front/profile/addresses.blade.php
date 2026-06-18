@extends('layouts.eshopper')
@section('title', 'My Addresses | Jeanzo India')
@section('meta_description', 'Manage your saved delivery addresses in your Jeanzo account for faster checkout on every order.')
@section('meta_robots', 'noindex, nofollow')

@section('content')

<div class="container-fluid pb-5" style="background:#faf8f8;">
    <div class="row px-xl-5 pt-4">
        @include('front.partials.profile-sidebar')

        <div class="col-lg-9 mb-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="j-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="j-section-title mb-0"><i class="fa fa-map-marker-alt mr-2" style="color:var(--j-primary);"></i>Saved Addresses</div>
                    <a href="{{ route('profile.address.create') }}" class="btn btn-primary btn-sm px-4">
                        <i class="fa fa-plus mr-1"></i> Add New
                    </a>
                </div>

                @if($addresses->isEmpty())
                    <div class="text-center py-5">
                        <div style="width:80px;height:80px;border-radius:50%;background:var(--j-primary-lt);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                            <i class="fa fa-map-marker-alt fa-2x" style="color:var(--j-primary);"></i>
                        </div>
                        <h5 class="text-muted">No addresses saved yet</h5>
                        <a href="{{ route('profile.address.create') }}" class="btn btn-primary mt-3 px-5">Add Your First Address</a>
                    </div>
                @else
                    <div class="row">
                        @foreach($addresses as $address)
                        <div class="col-md-6 mb-3">
                            <div class="j-address-card {{ $address->is_default ? 'default-addr' : '' }}">
                                @if($address->is_default)
                                    <span class="j-badge j-badge-delivered" style="position:absolute;top:12px;right:12px;font-size:.7rem;">
                                        <i class="fa fa-check-circle mr-1"></i>Default
                                    </span>
                                @endif
                                <div class="d-flex align-items-center mb-2">
                                    <div style="width:32px;height:32px;border-radius:50%;background:var(--j-primary-lt);display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                                        <i class="fa fa-{{ $address->address_type === 'billing' ? 'file-invoice' : 'truck' }}" style="color:var(--j-primary);font-size:.8rem;"></i>
                                    </div>
                                    <strong class="text-capitalize text-dark">{{ $address->address_type }} Address</strong>
                                </div>
                                <p class="mb-1 font-weight-600">{{ $address->full_name }}</p>
                                <p class="mb-1 text-muted small">{{ $address->street }}</p>
                                <p class="mb-1 text-muted small">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                <p class="mb-2 text-muted small">{{ $address->country }}</p>
                                <p class="mb-3 text-muted small"><i class="fa fa-phone mr-1"></i>{{ $address->phone }}</p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('profile.address.edit', $address->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('profile.address.delete', $address->id) }}" onsubmit="return confirm('Delete this address?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
