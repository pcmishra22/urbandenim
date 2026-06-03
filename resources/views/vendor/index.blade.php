@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Vendor Management</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Verification</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->name }}</td>
                            <td>{{ $vendor->email }}</td>
                            <td><span class="badge badge-primary">{{ ucfirst($vendor->role) }}</span></td>
                            <td>
                                @if($vendor->vendorProfile && $vendor->vendorProfile->is_verified)
                                    <span class="badge badge-success">Verified</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if(!$vendor->vendorProfile || !$vendor->vendorProfile->is_verified)
                                <form action="{{ route('admin.vendors.approve', $vendor->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                @endif
                                <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $vendors->links() }}
            </div>
        </div>
    </div>
</div>
@endsection