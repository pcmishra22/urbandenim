@extends('layouts.dashboard')

@section('title', 'Return Details')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-undo"></i> Return #{{ $return->id }}</h2>
<span class="badge bg-primary">{{ $return->status ?? '-' }}</span>
</div>

<div class="card mt-3">
    <div class="card-header">
        <span>Return Information</span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="text-muted">Order</div>
                <div><strong>#{{ $return->order_id }}</strong></div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-muted">Customer</div>
                <div><strong>{{ $return->user->name ?? '-' }}</strong></div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-muted">Requested</div>
                <div>{{ $return->requested_at ? $return->requested_at->format('Y-m-d H:i') : '-' }}</div>
            </div>
        </div>

        @if($return->reason)
            <div class="mt-2">
                <div class="text-muted">Reason</div>
                <div>{{ $return->reason }}</div>
            </div>
        @endif

        <hr />

        <h5 class="mb-3">Items</h5>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Reason</th>
                </tr>
                </thead>
                <tbody>
                @forelse($return->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? ('Product #' . $item->product_id) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->reason ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-3">No items</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <span>Refund Approval</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.returns.refunds.approve', $return) }}">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Refund Wallet Amount</label>
                    <input class="form-control" name="amount" type="number" step="0.01" min="0" value="{{ old('amount', $return->refund_wallet_amount ?? 0) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Currency (3-letter)</label>
                    <input class="form-control" name="currency" type="text" maxlength="3" value="{{ old('currency', $return->refund_wallet_currency ?? 'USD') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Admin Notes (optional)</label>
                    <input class="form-control" name="reason" type="text" value="{{ old('reason', '') }}" placeholder="optional">
                </div>
            </div>
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-check"></i> Approve Refund
            </button>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <span>Reverse Pickup</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.returns.pickups.request', $return) }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Courier ID (optional)</label>
                    <input class="form-control" name="courier_id" type="number" value="{{ old('courier_id', '') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tracking ID (optional)</label>
                    <input class="form-control" name="tracking_id" type="text" value="{{ old('tracking_id', '') }}">
                </div>
            </div>
            <button class="btn btn-secondary" type="submit">
                <i class="fas fa-truck"></i> Request Reverse Pickup
            </button>
        </form>

        <hr />
        <h6 class="mb-2">Pickup History</h6>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead>
                <tr>
                    <th>Status</th>
                    <th>Tracking</th>
                    <th>Requested</th>
                </tr>
                </thead>
                <tbody>
                @forelse($return->pickupRequests as $pickup)
                    <tr>
                        <td>{{ $pickup->status }}</td>
                        <td>{{ $pickup->tracking_id ?? '-' }}</td>
                        <td>{{ $pickup->requested_at ? $pickup->requested_at->format('Y-m-d H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">No pickups yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <span>Exchange Requests</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.returns.exchanges.approve', $return) }}">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Exchange Wallet Amount</label>
                    <input class="form-control" name="amount" type="number" step="0.01" min="0" value="{{ old('amount', 0) }}" required>
                </div>
                <div class="col-md-8 mb-3 text-muted d-flex align-items-end">
                    Approving exchange will create an exchange request linked to this return.
                </div>
            </div>
            <button class="btn btn-warning" type="submit">
                <i class="fas fa-exchange-alt"></i> Approve Exchange
            </button>
        </form>

        @if($return->exchangeRequest)
            <div class="mt-3">
                <div class="text-muted">Latest Exchange Status: <strong>{{ $return->exchangeRequest->status }}</strong></div>
            </div>
        @endif

    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <span>Refund Wallet</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.returns.wallet.refund', $return) }}">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Amount</label>
                    <input class="form-control" name="amount" type="number" step="0.01" min="0" value="{{ old('amount', $return->refund_wallet_amount ?? 0) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Currency</label>
                    <input class="form-control" name="currency" type="text" maxlength="3" value="{{ old('currency', $return->refund_wallet_currency ?? 'USD') }}">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end text-muted">
                    Completes the wallet refund transaction.
                </div>
            </div>
            <button class="btn btn-success" type="submit">
                <i class="fas fa-wallet"></i> Refund to Wallet
            </button>
        </form>
    </div>
</div>
@endsection

