<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteSettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->pluck('value','key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'facebook_url'  => 'nullable|url|max:255',
            'twitter_url'   => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url'  => 'nullable|url|max:255',
            'youtube_url'   => 'nullable|url|max:255',
            'store_address' => 'nullable|string|max:500',
            'store_phone'   => 'nullable|string|max:50',
            'store_email'   => 'nullable|email|max:255',
        ]);

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value);
        }
        Cache::forget('site_settings_all');

        return redirect()->back()->with('success', 'Settings saved successfully!');
    }

    public function newsletters()
    {
        $subscribers = NewsletterSubscriber::orderByDesc('created_at')->paginate(30);
        return view('admin.settings.newsletters', compact('subscribers'));
    }

    public function toggleSubscriber(NewsletterSubscriber $subscriber)
    {
        $subscriber->update(['is_active' => !$subscriber->is_active]);
        return back()->with('success', 'Subscriber status updated.');
    }

    public function destroySubscriber(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('success', 'Subscriber removed.');
    }
}
