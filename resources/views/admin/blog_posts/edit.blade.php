@extends('layouts.dashboard')

@section('title', 'Edit Blog Post')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-edit"></i> Edit Blog Post</h2>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">

        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card mt-3">
    <div class="card-body">
        <form action="{{ route('admin.blogs.update', $post) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $post->title) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug (optional)</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $post->slug) }}">
                        <div class="form-text">If empty, slug will be generated from title.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Excerpt</label>
                        <textarea name="excerpt" class="form-control" rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" class="form-control" rows="8">{{ old('content', $post->content) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Featured Image URL</label>
                        <input type="text" name="featured_image_url" class="form-control" value="{{ old('featured_image_url', $post->featured_image_url) }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="blog_category_id" class="form-control">
                            <option value="">-- None --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (string)old('blog_category_id', $post->blog_category_id) === (string)$category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_featured" class="form-check-input" value="1" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label">Featured Blog</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="draft" {{ old('status', $post->status)==='draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $post->status)==='published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Published At (optional)</label>
                        <input type="date" name="published_at" class="form-control" value="{{ old('published_at', optional($post->published_at)->format('Y-m-d')) }}">
                    </div>

                    <hr>
                    <h5 class="text-muted">SEO (VERY IMPORTANT FOR SEO)</h5>

                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $post->meta_title) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $post->meta_description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Canonical URL</label>
                        <input type="text" name="canonical_url" class="form-control" value="{{ old('canonical_url', $post->canonical_url) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">OG Title</label>
                        <input type="text" name="og_title" class="form-control" value="{{ old('og_title', $post->og_title) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">OG Description</label>
                        <textarea name="og_description" class="form-control" rows="3">{{ old('og_description', $post->og_description) }}</textarea>
                    </div>
                </div>
            </div>

            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-muted">Tags</h5>
                    <select name="tag_ids[]" multiple class="form-control" style="height: 180px;">
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" {{ in_array($tag->id, $selectedTagIds) ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Use Ctrl/⌘ to select multiple.</div>
                </div>

                <div class="col-md-6">
                    <h5 class="text-muted">Related Posts</h5>
                    <select name="related_post_ids[]" multiple class="form-control" style="height: 180px;">
                        @foreach($allPosts as $p)
                            <option value="{{ $p->id }}" {{ in_array($p->id, $selectedRelatedIds) ? 'selected' : '' }}>
                                {{ $p->title }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Select posts to show as related.</div>
                </div>
            </div>

            <button class="btn btn-primary mt-3" type="submit">
                <i class="fas fa-save"></i> Update Post
            </button>
        </form>
    </div>
</div>
@endsection

