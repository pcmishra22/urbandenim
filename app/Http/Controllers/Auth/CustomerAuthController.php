<?php

namespace App\Http\Controllers\Auth;

use App\Mail\NewUserAdminMail;
use App\Mail\UserLoginAdminMail;
use App\Mail\UserLoginMail;
use App\Mail\WelcomeMail;
use App\Mail\VerifyEmailMail;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    protected $redirectTo = '/dashboard';

    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('customer.dashboard');
        return view('auth.customer.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials) && Auth::user()->role === 'customer') {
            $request->session()->regenerate();

            // Notify user of login
            try {
                Mail::to(Auth::user()->email)->send(new UserLoginMail(
                    user:    Auth::user(),
                    loginAt: now()->format('d M Y, h:i A'),
                    ip:      $request->ip(),
                ));
            } catch (\Throwable $e) { Log::warning('Login user email failed', ['error' => $e->getMessage()]); }

            // Notify admin of login
            try {
                $adminEmail = env('ADMIN_EMAIL', 'support@jeanzo.in');
                Mail::to($adminEmail)->send(new UserLoginAdminMail(
                    user:    Auth::user(),
                    loginAt: now()->format('d M Y, h:i A'),
                    ip:      $request->ip(),
                ));
            } catch (\Throwable $e) { Log::warning('Login admin email failed', ['error' => $e->getMessage()]); }

            return redirect()->intended(route('customer.dashboard'))->with('success', 'Welcome back!');
        }

        return back()->withErrors(['email' => 'Invalid credentials or not a customer account.'])->onlyInput('email');
    }

    protected function guard() { return Auth::guard('web'); }

    public function showRegister()
    {
        if (Auth::check()) return redirect()->route('customer.dashboard');
        return view('auth.customer.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'customer',
        ]);

        Auth::login($user);

        // Welcome email to user
        try { 
            Mail::to($user->email)
                ->send((new WelcomeMail($user))->subject("Welcome to Jeanzo, {$user->name}!")); 
        }
        catch (\Throwable $e) { Log::warning('Welcome email failed', ['error' => $e->getMessage()]); }

        // Send email verification link to the user
        try {
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $user->id,
                    'hash' => sha1($user->email),
                ]
            );

            Mail::to($user->email)->send(
                (new VerifyEmailMail($user, $verificationUrl))->subject("Verify Your Email Address — Jeanzo")
            );
        } catch (\Throwable $e) {
            Log::warning('Verification email send failed', ['error' => $e->getMessage()]);
        }

        // Notify admin
        try {
            $adminEmail = env('ADMIN_EMAIL', 'support@jeanzo.in');
            Mail::to($adminEmail)
                ->send(new NewUserAdminMail($user));
        } catch (\Throwable $e) { Log::warning('New user admin email failed', ['error' => $e->getMessage()]); }

        return redirect()->route('customer.dashboard')->with('success', 'Account created! Check your email.');

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('customer.login')->with('success', 'Logged out successfully.');
    }

    public function showVerificationNotice(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectTo) : view('auth.customer.verify');
    }

    public function verify(Request $request)
    {
        if ($request->route('id') == $request->user()->getKey() && $request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectTo);
        }
        if ($request->user()->markEmailAsVerified()) event(new Verified($request->user()));
        return redirect($this->redirectTo)->with('verified', true);
    }

    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) return redirect($this->redirectTo);
        $user = $request->user();
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        try {
            Mail::to($user->email)
                ->send((new VerifyEmailMail($user, $verificationUrl))->subject("Verify Your Email Address — Jeanzo"));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Verify email send failed', ['error' => $e->getMessage()]);
        }
        return back()->with('status', 'Verification link sent!');
    }

    public function showForgotPassword() { return view('auth.customer.forgot-password'); }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.customer.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['token'=>'required','email'=>'required|email','password'=>'required|min:6|confirmed']);
        $status = Password::reset(
            $request->only('email','password','password_confirmation','token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));
                $user->save();
                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('customer.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
