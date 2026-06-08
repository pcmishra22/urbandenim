@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">Edit Shipment #{{ $shipment->id }}</div>
        <div class="card-body">
            <form action="{{ route('admin.shipments.update', $shipment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Order ID</label>
                    <input type="text" class="form-control" value="{{ $shipment->order->id ?? 'N/A' }}" disabled>
                </div>
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" class="form-control" value="{{ $shipment->order->user->name ?? 'N/A' }}" disabled>
                </div>
                <div class="form-group">
                    <label for="courier_id">Courier</label>
                    <select name="courier_id" id="courier_id" class="form-control" required>
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}" {{ $shipment->courier_id == $courier->id ? 'selected' : '' }}>{{ $courier->name }}</option>
                        @endforeach
                    </select>
                    @error('courier_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="tracking_id">Tracking Number</label>
                    <input type="text" name="tracking_id" id="tracking_id" class="form-control" value="{{ old('tracking_id', $shipment->tracking_id) }}">
                    @error('tracking_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        @foreach(['pending', 'shipped', 'in_transit', 'delivered', 'cancelled'] as $status)
                            <option value="{{ $status }}" {{ $shipment->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="shipped_at">Shipped At</label>
                    <input type="datetime-local" name="shipped_at" id="shipped_at" class="form-control" value="{{ old('shipped_at', $shipment->shipped_at ? \Carbon\Carbon::parse($shipment->shipped_at)->format('Y-m-d\TH:i') : '') }}">
                    @error('shipped_at')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="delivered_at">Delivered At</label>
                    <input type="datetime-local" name="delivered_at" id="delivered_at" class="form-control" value="{{ old('delivered_at', $shipment->delivered_at ? \Carbon\Carbon::parse($shipment->delivered_at)->format('Y-m-d\TH:i') : '') }}">
                    @error('delivered_at')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Update Shipment</button>
                <a href="{{ route('admin.shipments.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection