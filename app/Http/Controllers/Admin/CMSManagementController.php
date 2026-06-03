<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\Faq;
use Illuminate\Http\Request;

class CMSManagementController extends Controller
{
    private function checkAdmin()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }
    }

    // --- Static Pages (Terms, Privacy, About) ---
    public function pagesIndex()
    {
        $this->checkAdmin();
        $pages = CmsPage::all();
        
        // Auto-seed defaults if empty
        if ($pages->isEmpty()) {
            $defaults = [
                ['slug' => 'terms', 'title' => 'Terms & Conditions'],
                ['slug' => 'privacy', 'title' => 'Privacy Policy'],
                ['slug' => 'about', 'title' => 'About Us'],
            ];
            foreach ($defaults as $d) {
                CmsPage::create($d);
            }
            $pages = CmsPage::all();
        }
        return view('admin.cms.pages_index', compact('pages'));
    }

    public function pagesEdit($slug)
    {
        $this->checkAdmin();
        $page = CmsPage::where('slug', $slug)->firstOrFail();
        return view('admin.cms.pages_edit', compact('page'));
    }

    public function pagesUpdate(Request $request, $slug)
    {
        $this->checkAdmin();
        $page = CmsPage::where('slug', $slug)->firstOrFail();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);
        $page->update($validated);
        return redirect()->route('admin.cms.pages.index')->with('success', 'Page updated successfully');
    }

    // --- FAQ Management ---
    public function faqsIndex()
    {
        $this->checkAdmin();
        $faqs = Faq::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.cms.faqs_index', compact('faqs'));
    }

    public function faqsCreate()
    {
        $this->checkAdmin();
        return view('admin.cms.faqs_create');
    }

    public function faqsStore(Request $request)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_active' => 'boolean',
        ]);
        Faq::create($validated);
        return redirect()->route('admin.cms.faqs.index')->with('success', 'FAQ created successfully');
    }

    public function faqsEdit(Faq $faq)
    {
        $this->checkAdmin();
        return view('admin.cms.faqs_edit', compact('faq'));
    }

    public function faqsUpdate(Request $request, Faq $faq)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_active' => 'boolean',
        ]);
        $faq->update($validated);
        return redirect()->route('admin.cms.faqs.index')->with('success', 'FAQ updated successfully');
    }

    public function faqsDestroy(Faq $faq)
    {
        $this->checkAdmin();
        $faq->delete();
        return redirect()->route('admin.cms.faqs.index')->with('success', 'FAQ deleted successfully');
    }
}