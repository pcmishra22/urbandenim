@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">CMS Pages</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th style="width: 160px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages as $page)
                        <tr>
                            <td>{{ $page->title }}</td>
                            <td><code>{{ $page->slug }}</code></td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="{{ route('admin.cms.pages.edit', $page->slug) }}">Edit</a>
                            </td>
                        </tr>
                    @endforeach

                    @if($pages->isEmpty())
                        <tr>
                            <td colspan="3" class="text-center text-muted">No pages found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

