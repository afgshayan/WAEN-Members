<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    // ---------------------------------------------------------------------------
    // Show login form
    // ---------------------------------------------------------------------------

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('persons.index');
        }

        $captcha = $this->captchaSettings();
        return view('auth.login', compact('captcha'));
    }

    // ---------------------------------------------------------------------------
    // Handle login attempt
    // ---------------------------------------------------------------------------

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:191',
            'password' => 'required|string|min:6|max:100',
        ]);

        $maxAttempts  = (int) Setting::get('login_max_attempts', 5);
        $decaySeconds = (int) Setting::get('login_decay_seconds', 60);
        $throttleKey  = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => "Too many login attempts. Please try again in {$seconds} seconds."]);
        }

        // CAPTCHA validation
        $captcha = $this->captchaSettings();
        if ($captcha['type'] !== 'none') {
            $captchaError = $this->verifyCaptcha($request, $captcha);
            if ($captchaError) {
                return back()->withInput($request->only('email'))
                    ->withErrors(['captcha' => $captchaError]);
            }
        }

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended(route('persons.index'));
        }

        RateLimiter::hit($throttleKey, $decaySeconds);

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }

    // ---------------------------------------------------------------------------
    // Logout
    // ---------------------------------------------------------------------------

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ---------------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------------

    private function captchaSettings(): array
    {
        try {
            return [
                'type'     => Setting::get('captcha_type', 'none'),
                'site_key' => Setting::get('captcha_site_key', ''),
                'theme'    => Setting::get('captcha_theme', 'light'),
                'language' => Setting::get('captcha_language', 'en'),
            ];
        } catch (\Throwable) {
            return ['type' => 'none', 'site_key' => '', 'theme' => 'light', 'language' => 'en'];
        }
    }

    private function verifyCaptcha(Request $request, array $captcha): ?string
    {
        try {
            $secretKey = Setting::get('captcha_secret_key', '');
            if (empty($secretKey)) return null;

            $type          = $captcha['type'];
            $responseToken = $request->input('g-recaptcha-response')
                ?? $request->input('h-captcha-response')
                ?? $request->input('cf-turnstile-response');

            if (empty($responseToken)) {
                return 'Please complete the CAPTCHA.';
            }

            $urls = [
                'recaptcha2' => 'https://www.google.com/recaptcha/api/siteverify',
                'recaptcha3' => 'https://www.google.com/recaptcha/api/siteverify',
                'hcaptcha'   => 'https://hcaptcha.com/siteverify',
                'turnstile'  => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            ];

            $url = $urls[$type] ?? null;
            if (!$url) return null;

            $response = Http::asForm()->post($url, [
                'secret'   => $secretKey,
                'response' => $responseToken,
                'remoteip' => $request->ip(),
            ]);

            $body = $response->json();

            if ($type === 'recaptcha3') {
                $score = $body['score'] ?? 0;
                if (!($body['success'] ?? false) || $score < 0.5) {
                    return 'CAPTCHA score too low. Please try again.';
                }
                return null;
            }

            return ($body['success'] ?? false) ? null : 'CAPTCHA verification failed. Please try again.';
        } catch (\Throwable) {
            return null; // Don't block login if CAPTCHA service is unreachable
        }
    }
}
