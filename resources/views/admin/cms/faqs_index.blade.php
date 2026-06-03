@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">CMS: FAQ Management</h1>
        <a href="#" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-2"></i>Add New FAQ
        </a>
    </div>

    <!-- Status Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Frequently Asked Questions</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4" style="width: 50px;">ID</th>
                            <th>Question</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs ?? [] as $faq)
                            <tr>
                                <td class="px-4 text-muted">{{ $faq->id }}</td>
                                <td class="fw-bold">{{ $faq->question }}</td>
                                <td>
                                    <span class="badge bg-{{ $faq->is_active ? 'success' : 'secondary' }}">
                                        {{ $faq->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $faq->updated_at->diffForHumans() }}</td>
                                <td class="text-end px-4">
                                    <div class="btn-group shadow-sm">
                                        <a href="#" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <button type="button" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-question-circle fa-3x mb-3"></i>
                                        <p>No FAQs found in the database.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($faqs) && method_exists($faqs, 'links'))
            <div class="card-footer bg-white py-3">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection