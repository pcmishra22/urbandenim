@extends('layouts.dashboard')

@section('title', 'Update Shipment - Admin')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-edit"></i> Update Shipment Tracking</h2>
    <a href="{{ route('admin.shipments.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-header">Shipment</div>
    <div class="card-body">
        <div class="mb-3 text-muted">
            Order: <strong>#{{ $shipment->order->id }}</strong> &nbsp;|&nbsp;
            Customer: <strong>{{ $shipment->order->user->name ?? '-' }}</strong>
        </div>

        <form method="POST" action="{{ route('admin.shipments.update', $shipment) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Courier</label>
                    <select class="form-select" name="courier_id">
                        <option value="" {{ empty($shipment->courier_id) ? 'selected' : '' }}>Select courier...</option>
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}" {{ (string)$shipment->courier_id === (string)$courier->id ? 'selected' : '' }}>
                                {{ $courier->name }} ({{ $courier->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Tracking ID</label>
                    <input type="text" name="tracking_id" value="{{ old('tracking_id', $shipment->tracking_id) }}" class="form-control" maxlength="255">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <input type="text" name="status" value="{{ old('status', $shipment->status) }}" class="form-control" required maxlength="50">
                    <div class="form-text">Example: created, shipped, in_transit, delivered</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Shipped At</label>
                    <input type="datetime-local" name="shipped_at" value="{{ old('shipped_at') ? old('shipped_at') : ($shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d\TH:i') : '') }}" class="form-control">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Delivered At</label>
                    <input type="datetime-local" name="delivered_at" value="{{ old('delivered_at') ? old('delivered_at') : ($shipment->delivered_at ? $shipment->delivered_at->format('Y-m-d\TH:i') : '') }}" class="form-control">
                </div>
            </div>

            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Update
            </button>
        </form>
    </div>
</div>
@endsection

