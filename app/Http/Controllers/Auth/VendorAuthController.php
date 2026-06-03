<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class VendorAuthController extends Controller
{
    /**
     * Show vendor login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('vendor.dashboard');
        }
        return view('auth.vendor.login');
    }

    /**
     * Where to redirect users after login / registration / password reset / email verification.
     */
    protected $redirectTo = '/vendor/dashboard';

    /**
     * Handle vendor login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials) && Auth::user()->role === 'vendor') {
            $request->session()->regenerate();
            return redirect()->route('vendor.dashboard')->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or not a vendor account.',
        ])->onlyInput('email');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('vendor'); // Assuming 'vendor' is the guard for vendors
    }

    /**
     * Show vendor registration form
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('vendor.dashboard');
        }
        return view('auth.vendor.register');
    }

    /**
     * Handle vendor registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'shop_name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'vendor',
        ]);

        Auth::login($user);
        return redirect()->route('vendor.dashboard')->with('success', 'Vendor account created successfully!');
    }

    /**
     * Logout vendor
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('vendor.login')->with('success', 'Logged out successfully.');
    }

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showVerificationNotice(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect($this->redirectPath())
                    : view('auth.vendor.verify'); // You'll need to create this view
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request)
    {
        if ($request->route('id') == $request->user()->getKey() &&
            $request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect($this->redirectPath())->with('verified', true);
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }

    /**
     * Display the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.vendor.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Get the broker to be used during password reset.
     */
    public function broker()
    {
        return Password::broker('users');
    }

    /**
     * Get the response for a successful password reset link.
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return back()->with('status', __($response));
    }

    /**
     * Get the response for a failed password reset link.
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()->withErrors(['email' => __($response)]);
    }

    /**
     * Display the password reset view for the given token.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.vendor.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Handle a password reset request for the application.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $response = $this->broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        return $response == Password::PASSWORD_RESET
            ? redirect($this->redirectTo)->with('status', __($response))
            : back()->withErrors(['email' => __($response)]);
    }
}
