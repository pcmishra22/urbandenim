@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">Create New Shipment</div>
        <div class="card-body">
            <form action="{{ route('admin.shipments.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="order_id">Select Order</label>
                    <select name="order_id" id="order_id" class="form-control" required>
                        <option value="">-- Select Order --</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>Order #{{ $order->id }} - {{ $order->user->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                    @error('order_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="courier_id">Courier</label>
                    <select name="courier_id" id="courier_id" class="form-control" required>
                        <option value="">-- Select Courier --</option>
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}" {{ old('courier_id') == $courier->id ? 'selected' : '' }}>{{ $courier->name }}</option>
                        @endforeach
                    </select>
                    @error('courier_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="tracking_id">Tracking Number</label>
                    <input type="text" name="tracking_id" id="tracking_id" class="form-control" value="{{ old('tracking_id') }}">
                    @error('tracking_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="shipped" {{ old('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="in_transit" {{ old('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Create Shipment</button>
                <a href="{{ route('admin.shipments.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection