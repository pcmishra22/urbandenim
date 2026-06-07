<?php
namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::where('is_active', true)->orderBy('id')->get();
        return view('front.faq', compact('faqs'));
    }

    public function help()
    {
        return view('front.help');
    }

    public function newsletter(Request $request)
    {
        $request->validate([
            'name'  => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $existing = NewsletterSubscriber::where('email', $request->email)->first();
        if ($existing) {
            if (!$existing->is_active) {
                $existing->update(['is_active' => true, 'name' => $request->name ?: $existing->name]);
                return back()->with('newsletter_success', 'Welcome back! You have been re-subscribed.');
            }
            return back()->with('newsletter_success', 'You are already subscribed. Thank you!');
        }

        NewsletterSubscriber::create([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('newsletter_success', 'Thank you for subscribing to our newsletter!');
    }
}
