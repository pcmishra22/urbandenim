<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all'); // all|pending|approved|spam|featured
        $q = Review::query()->with(['product', 'user'])->latest();

        if ($filter === 'pending') {
            $q->where('status', 'pending')->where('is_spam', false);
        } elseif ($filter === 'approved') {
            $q->where('status', 'approved')->where('is_spam', false);
        } elseif ($filter === 'spam') {
            $q->where('is_spam', true)->orWhere('status', 'spam');
        } elseif ($filter === 'featured') {
            $q->where('is_featured', true);
        }

        if ($request->filled('product_id')) {
            $q->where('product_id', $request->query('product_id'));
        }

        $reviews = $q->paginate(15)->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'filter' => $filter,
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return view('admin.reviews.create', [
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['nullable', 'string', 'max:5000'],
        ]);

        $review = new Review();
        $review->fill([
            'product_id' => (int) $validated['product_id'],
            'user_id' => $validated['user_id'] ?? null,
            'rating' => (int) $validated['rating'],
            'review_text' => $validated['review_text'] ?? null,
        ]);

        $this->applySpamDetection($review);
        $this->applyApprovalDefaults($review);

        $review->save();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review created successfully.');
    }

    public function edit(Review $review)
    {
        return view('admin.reviews.edit', [
            'review' => $review->load(['product', 'user']),
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', 'in:pending,approved,rejected,spam'],
            'is_featured' => ['nullable', 'boolean'],
            'is_spam' => ['nullable', 'boolean'],
            'is_approved' => ['nullable', 'boolean'],
        ]);

        $review->fill([
            'product_id' => (int) $validated['product_id'],
            'user_id' => $validated['user_id'] ?? null,
            'rating' => (int) $validated['rating'],
            'review_text' => $validated['review_text'] ?? null,
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
        ]);

        // If admin explicitly sets state fields, respect them.
        if (array_key_exists('is_spam', $validated) || array_key_exists('is_approved', $validated) || array_key_exists('status', $validated)) {
            $review->is_spam = (bool) ($validated['is_spam'] ?? $review->is_spam);
            $review->is_approved = (bool) ($validated['is_approved'] ?? $review->is_approved);
            $review->status = $validated['status'] ?? $review->status;
        } else {
            $this->applySpamDetection($review);
            $this->applyApprovalDefaults($review);
        }

        // Normalize status based on flags
        if ($review->is_spam) {
            $review->status = 'spam';
            $review->is_approved = false;
        }

        if ($review->status === 'approved') {
            $review->is_approved = true;
            $review->is_spam = false;
        }

        $review->save();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }

    // ===== Review-specific actions =====
    public function approve(Review $review)
    {
        $review->is_spam = false;
        $review->is_approved = true;
        $review->status = 'approved';
        $review->save();

        return back()->with('success', 'Review approved.');
    }

    public function reject(Review $review)
    {
        $review->is_spam = false;
        $review->is_approved = false;
        $review->status = 'rejected';
        $review->save();

        return back()->with('success', 'Review rejected.');
    }

    public function markSpam(Review $review)
    {
        $review->is_spam = true;
        $review->is_approved = false;
        $review->status = 'spam';
        $review->save();

        return back()->with('success', 'Review marked as spam.');
    }

    public function toggleFeatured(Review $review)
    {
        $review->is_featured = !$review->is_featured;

        // Featured reviews should not be spam
        if ($review->is_featured) {
            $review->is_spam = false;
            if ($review->status === 'spam') {
                $review->status = 'pending';
            }
        }

        $review->save();

        return back()->with('success', $review->is_featured ? 'Marked as featured.' : 'Removed from featured.');
    }

    // ===== Helpers =====
    private function applySpamDetection(Review $review): void
    {
        $text = (string) ($review->review_text ?? '');
        $lower = Str::lower($text);

        $score = 0;

        // Heuristics: links / keywords / excessive repetition
        if (preg_match('/https?:\/\//i', $text) || preg_match('/www\./i', $text)) {
            $score += 30;
        }

        $blacklist = [
            'buy now',
            'free money',
            'crypto',
            'whatsapp',
            'telegram',
            'click here',
            'stock',
        ];

        foreach ($blacklist as $word) {
            if (Str::contains($lower, $word)) {
                $score += 20;
            }
        }

        // Repetition (e.g., "!!!!!!" or "loooool")
        if (preg_match('/(.)\1\1\1,?\1?/', $text)) {
            $score += 15;
        }

        // Very short and generic
        $trim = trim($lower);
        if (strlen($trim) > 0 && strlen($trim) < 12) {
            $score += 10;
        }

        $review->spam_score = $score;
        $review->is_spam = $score >= 40;
    }

    private function applyApprovalDefaults(Review $review): void
    {
        if ($review->is_spam) {
            $review->is_approved = false;
            $review->status = 'spam';
            return;
        }

        // Default newly created reviews are pending
        if (!in_array($review->status, ['approved', 'rejected', 'spam'], true)) {
            $review->status = 'pending';
            $review->is_approved = false;
        }
    }
}

