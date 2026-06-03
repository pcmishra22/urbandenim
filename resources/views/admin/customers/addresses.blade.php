@extends('layouts.dashboard')

@section('title', 'Customer Addresses')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Addresses - {{ $customer->name }}</h1>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.customers.index') }}">Back</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Line 1</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Country</th>
                            <th>Postal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($addresses as $address)
                            <tr>
                                <td>{{ $address->line1 }}</td>
                                <td>{{ $address->city }}</td>
                                <td>{{ $address->state }}</td>
                                <td>{{ $address->country }}</td>
                                <td>{{ $address->postal_code }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No addresses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $addresses->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>
</div>
@endsection

