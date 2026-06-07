@extends('layouts.eshopper')

@section('content')
    @include('front.partials.page-banner', ['title' => 'My Addresses', 'breadcrumb' => 'Addresses'])
</div>
    </div>

    <div class="container-fluid pt-5 pb-5">
        <div class="row px-xl-5">
            @include('front.partials.profile-sidebar')

            <div class="col-lg-9 mb-5">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-semi-bold m-0">Saved Addresses</h5>
                    <a href="{{ route('profile.address.create') }}" class="btn btn-primary"><i class="fa fa-plus mr-2"></i>Add New Address</a>
                </div>

                @if($addresses->isEmpty())
                    <div class="text-center py-5 bg-light">
                        <i class="fa fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No addresses saved yet</h5>
                        <a href="{{ route('profile.address.create') }}" class="btn btn-primary mt-3">Add Your First Address</a>
                    </div>
                @else
                    <div class="row">
                        @foreach($addresses as $address)
                            <div class="col-md-6 mb-4">
                                <div class="border p-4 h-100 position-relative">
                                    @if($address->is_default)
                                        <span class="badge badge-primary position-absolute" style="top:10px;right:10px;">Default</span>
                                    @endif
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fa fa-{{ $address->address_type === 'billing' ? 'file-invoice' : 'truck' }} text-primary mr-2"></i>
                                        <strong class="text-capitalize">{{ $address->address_type }} Address</strong>
                                    </div>
                                    <p class="mb-1">{{ $address->full_name }}</p>
                                    <p class="mb-1 text-muted">{{ $address->street }}</p>
                                    <p class="mb-1 text-muted">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                    <p class="mb-3 text-muted">{{ $address->country }}</p>
                                    <p class="mb-3 text-muted"><i class="fa fa-phone mr-1"></i>{{ $address->phone }}</p>
                                    <div class="d-flex">
                                        <a href="{{ route('profile.address.edit', $address->id) }}" class="btn btn-sm btn-outline-primary mr-2">Edit</a>
                                        <form method="POST" action="{{ route('profile.address.delete', $address->id) }}" onsubmit="return confirm('Delete this address?')">
                                            @csrf
                                            @method('DELETE')
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
@endsection
