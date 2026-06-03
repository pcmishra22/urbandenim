@extends('layouts.dashboard')

@section('title', 'Customer Management')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Customer List</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Wallet</th>
                            <th style="width: 220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td class="fw-bold">{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>
                                    @php($blocked = (bool) ($customer->is_blocked ?? false))
                                    <span class="badge bg-{{ $blocked ? 'danger' : 'success' }}">
                                        {{ $blocked ? 'Blocked' : 'Active' }}
                                    </span>
                                </td>
                                <td>
                                    ${{ number_format((float) ($customer->wallet->balance ?? 0), 2) }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.customers.orders', $customer) }}">
                                            Orders
                                        </a>
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.customers.wallet', $customer) }}">
                                            Wallet
                                        </a>
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.customers.addresses', $customer) }}">
                                            Addresses
                                        </a>
                                    </div>
                                    <div class="mt-2">
                                        @if(!($customer->is_blocked ?? false))
                                            <form action="{{ route('admin.customers.toggleBlock', $customer) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="block" value="1" />
                                                <button class="btn btn-sm btn-danger" type="submit">Block</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.customers.toggleBlock', $customer) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="block" value="0" />
                                                <button class="btn btn-sm btn-success" type="submit">Unblock</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No customers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $customers->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection

