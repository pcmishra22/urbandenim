@extends('layouts.dashboard')

@section('title', 'Customer Wallet')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Wallet - {{ $customer->name }}</h1>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.customers.index') }}">Back</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <h6>Wallet Balance</h6>
                <div class="value">${{ number_format((float) ($customer->wallet->balance ?? 0), 2) }}</div>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        Wallet details shown from <code>wallet</code> relationship.
    </div>
</div>
@endsection

