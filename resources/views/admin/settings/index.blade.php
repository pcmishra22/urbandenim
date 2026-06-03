@extends('layouts.dashboard')

@section('title', 'Settings')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-cog"></i> Site Settings</h2>
</div>

@if(session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
@endif

<div class="card mt-3">
    <div class="card-header"><strong>General Settings</strong></div>
    <div class="card-body">
        <p class="text-muted">
            <i class="fas fa-info-circle"></i>
            Settings management is available in a future release. Configuration is currently managed via <code>.env</code> and <code>config/</code> files.
        </p>
        <ul class="list-unstyled mb-0">
            <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>Site Name: <strong>{{ config('app.name', 'EShopper') }}</strong></li>
            <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>Environment: <strong>{{ config('app.env') }}</strong></li>
            <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>Debug Mode: <strong>{{ config('app.debug') ? 'On' : 'Off' }}</strong></li>
            <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>Timezone: <strong>{{ config('app.timezone') }}</strong></li>
        </ul>
    </div>
</div>
@endsection
