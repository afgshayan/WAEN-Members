<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SettingController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAdmin()) abort(403, 'Only administrators can access settings.');
        $settings = Setting::allKeyed();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!auth()->user()->isAdmin()) abort(403, 'Only administrators can update settings.');
        $request->validate([
            'app_name'             => 'required|string|max:100',
            'app_description'      => 'nullable|string|max:255',
            'timezone'             => 'required|string|max:60',
            'per_page_default'     => 'required|integer|min:5|max:1000',
            'date_format'          => 'required|string|max:30',

            'session_lifetime'     => 'required|integer|min:5|max:10080',
            'login_slug'           => 'required|string|max:50|regex:/^[a-zA-Z0-9\-_]+$/',
            'login_max_attempts'   => 'required|integer|min:1|max:100',
            'login_decay_seconds'  => 'required|integer|min:10|max:3600',
            'force_https'          => 'nullable|boolean',
            'remember_me_days'     => 'required|integer|min:1|max:365',

            'captcha_type'         => 'required|in:none,recaptcha2,recaptcha3,hcaptcha,turnstile',
            'captcha_site_key'     => 'nullable|string|max:200',
            'captcha_secret_key'   => 'nullable|string|max:200',
            'captcha_theme'        => 'required|in:light,dark',
            'captcha_language'     => 'nullable|string|max:10',

            'import_batch_size'    => 'required|integer|min:50|max:5000',
            'export_chunk_size'    => 'required|integer|min:100|max:10000',
            'max_upload_mb'        => 'required|integer|min:1|max:500',

            'root_access_title'    => 'required|string|max:120',
            'root_access_message'  => 'required|string|max:1000',
        ]);

        $groups = [
            'general'       => ['app_name', 'app_description', 'timezone', 'per_page_default', 'date_format', 'root_access_title', 'root_access_message'],
            'security'      => ['session_lifetime', 'login_slug', 'login_max_attempts', 'login_decay_seconds', 'force_https', 'remember_me_days'],
            'captcha'       => ['captcha_type', 'captcha_site_key', 'captcha_secret_key', 'captcha_theme', 'captcha_language'],
            'import_export' => ['import_batch_size', 'export_chunk_size', 'max_upload_mb'],
        ];

        foreach ($groups as $group => $keys) {
            foreach ($keys as $key) {
                $value = $key === 'force_https'
                    ? ($request->boolean($key) ? '1' : '0')
                    : $request->input($key, '');
                Setting::set($key, $value, $group);
            }
        }

        return back()->with('success', 'Settings saved successfully.');
    }

    /**
     * Verify a CAPTCHA token against the provider's API using the supplied credentials.
     * Used by the "Test CAPTCHA" feature on the settings page.
     */
    public function testCaptcha(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:recaptcha2,recaptcha3,hcaptcha,turnstile',
            'site_key'   => 'required|string|max:200',
            'secret_key' => 'required|string|max:200',
            'token'      => 'required|string',
        ]);

        $type      = $request->input('type');
        $secretKey = $request->input('secret_key');
        $token     = $request->input('token');

        try {
            $endpoints = [
                'recaptcha2' => 'https://www.google.com/recaptcha/api/siteverify',
                'recaptcha3' => 'https://www.google.com/recaptcha/api/siteverify',
                'hcaptcha'   => 'https://hcaptcha.com/siteverify',
                'turnstile'  => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            ];

            $endpoint = $endpoints[$type];

            $response = Http::timeout(10)
                ->withOptions(['verify' => false]) // bypass SSL cert verification on local dev
                ->asForm()
                ->post($endpoint, [
                    'secret'   => $secretKey,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);

            $data = $response->json();

            if ($data['success'] ?? false) {
                $extra = '';
                if ($type === 'recaptcha3' && isset($data['score'])) {
                    $extra = ' Score: ' . number_format($data['score'], 2)
                           . ' (action: ' . ($data['action'] ?? 'n/a') . ')';
                }
                return response()->json([
                    'success' => true,
                    'message' => 'CAPTCHA verified successfully!' . $extra,
                ]);
            }

            $errors    = $data['error-codes'] ?? [];
            $errorMap  = [
                'missing-input-secret'   => 'Secret key is missing.',
                'invalid-input-secret'   => 'Secret key is invalid or malformed.',
                'missing-input-response' => 'CAPTCHA token was not provided.',
                'invalid-input-response' => 'CAPTCHA token is invalid or expired.',
                'bad-request'            => 'The request was invalid.',
                'timeout-or-duplicate'   => 'The CAPTCHA token has expired or was already used.',
            ];

            $messages = array_map(fn($e) => $errorMap[$e] ?? $e, $errors);

            return response()->json([
                'success' => false,
                'message' => implode(' ', $messages) ?: 'Verification failed.',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
