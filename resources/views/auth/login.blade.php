<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appName }} &mdash; Sign In</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- CAPTCHA scripts --}}
    @if(isset($captcha) && $captcha['type'] === 'recaptcha2')
    <script src="https://www.google.com/recaptcha/api.js?hl={{ $captcha['language'] }}" async defer></script>
    @elseif(isset($captcha) && $captcha['type'] === 'recaptcha3')
    <script src="https://www.google.com/recaptcha/api.js?render={{ $captcha['site_key'] }}&hl={{ $captcha['language'] }}"></script>
    @elseif(isset($captcha) && $captcha['type'] === 'hcaptcha')
    <script src="https://js.hcaptcha.com/1/api.js?hl={{ $captcha['language'] }}" async defer></script>
    @elseif(isset($captcha) && $captcha['type'] === 'turnstile')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #f4f6fb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        /* Outer card */
        .login-wrap {
            display: flex;
            width: 100%;
            max-width: 860px;
            min-height: 520px;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0,0,0,.22);
        }

        /* LEFT PANEL */
        .panel-left {
            flex: 1 1 42%;
            background: linear-gradient(160deg, #ea580c 0%, #f97316 55%, #fb923c 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 36px;
            overflow: hidden;
            color: #fff;
            text-align: center;
        }

        /* Decorative blobs on right edge of left panel */
        .blobs {
            position: absolute;
            right: -55px;
            top: 0; bottom: 0;
            width: 130px;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            pointer-events: none;
        }
        .blob {
            border-radius: 50%;
            flex-shrink: 0;
        }
        .blob-1 { width: 130px; height: 115px; background: rgba(255,255,255,.18); }
        .blob-2 { width: 110px; height: 95px;  background: rgba(255,255,255,.11); }
        .blob-3 { width: 140px; height: 120px; background: rgba(255,255,255,.16); }

        /* Logo circle */
        .logo-circle {
            width: 82px; height: 82px;
            background: rgba(255,255,255,.22);
            border: 3px solid rgba(255,255,255,.45);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.1rem;
            margin-bottom: 18px;
            position: relative; z-index: 1;
        }

        .panel-left .welcome-to {
            font-size: .9rem; font-weight: 300;
            letter-spacing: .5px; opacity: .85;
            margin-bottom: 4px;
            position: relative; z-index: 1;
        }
        .panel-left h1 {
            font-size: 2.1rem; font-weight: 700;
            letter-spacing: 2.5px;
            margin-bottom: 18px;
            position: relative; z-index: 1;
        }
        .panel-left p {
            font-size: .81rem; line-height: 1.65;
            opacity: .75; max-width: 200px;
            position: relative; z-index: 1;
        }
        .panel-left-footer {
            position: absolute; bottom: 14px;
            left: 0; right: 0;
            text-align: center;
            font-size: .68rem; opacity: .45;
            letter-spacing: .4px; z-index: 1;
        }

        /* RIGHT PANEL */
        .panel-right {
            flex: 1 1 58%;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px 48px;
        }

        .panel-right h3 {
            font-size: 1.5rem; font-weight: 700;
            color: #1a1a2e; margin-bottom: 6px;
        }
        .panel-right .sub {
            font-size: .83rem; color: #9ca3af;
            margin-bottom: 34px;
        }

        /* Fields — bottom-border only */
        .field { margin-bottom: 24px; }
        .field label {
            display: block;
            font-size: .77rem; font-weight: 600;
            color: #374151; margin-bottom: 8px;
            letter-spacing: .3px;
        }
        .field-inner {
            display: flex; align-items: center;
            border-bottom: 1.5px solid #d1d5db;
            padding-bottom: 8px;
            transition: border-color .2s;
        }
        .field-inner:focus-within { border-color: #f97316; }
        .field-inner i {
            color: #b0b7c3; font-size: 1rem;
            margin-right: 10px; flex-shrink: 0;
        }
        .field-inner input {
            flex: 1; border: none; outline: none;
            font-size: .9rem; color: #1f2937;
            background: transparent;
            font-family: 'Inter', sans-serif;
        }
        .field-inner input::placeholder { color: #c8cdd6; }
        .toggle-btn {
            background: none; border: none; cursor: pointer;
            color: #b0b7c3; padding: 0;
            display: flex; align-items: center;
            font-size: .95rem;
        }
        .toggle-btn:hover { color: #f97316; }

        /* Remember row */
        .remember-row {
            display: flex; align-items: center;
            gap: 8px; margin-bottom: 30px;
            font-size: .82rem; color: #6b7280;
        }
        .remember-row input[type=checkbox] {
            width: 15px; height: 15px;
            accent-color: #f97316; cursor: pointer;
        }
        .remember-row label { cursor: pointer; }

        /* Error box */
        .err-box {
            background: #fef2f2;
            border-left: 3px solid #ef4444;
            border-radius: 0 6px 6px 0;
            padding: 10px 14px;
            font-size: .82rem; color: #b91c1c;
            margin-bottom: 22px;
            display: flex; align-items: center; gap: 8px;
        }

        /* CAPTCHA */
        .captcha-wrap { margin-bottom: 22px; }
        .captcha-err { font-size: .78rem; color: #ef4444; margin-bottom: 8px; }

        /* Submit button */
        .btn-signin {
            width: 100%;
            padding: 12px 0;
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            color: #fff; border: none;
            border-radius: 50px;
            font-size: .92rem; font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer; letter-spacing: .4px;
            transition: opacity .2s, transform .15s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-signin:hover  { opacity: .88; transform: translateY(-1px); }
        .btn-signin:active { transform: translateY(0); }
        .btn-signin:disabled { opacity: .6; cursor: not-allowed; }

        /* Footer note */
        .right-footer {
            text-align: center; margin-top: 28px;
            font-size: .72rem; color: #c4c9d4;
            display: flex; align-items: center;
            justify-content: center; gap: 5px;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .login-wrap { flex-direction: column; max-width: 400px; border-radius: 18px; }
            .panel-left  { flex: none; padding: 32px 24px 24px; }
            .blobs, .panel-left p { display: none; }
            .panel-left h1 { margin-bottom: 0; }
            .panel-right { padding: 32px 28px 36px; }
        }
    </style>
</head>
<body>

<div class="login-wrap">

    {{-- LEFT PANEL --}}
    <div class="panel-left">
        <div class="blobs">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="blob blob-3"></div>
        </div>

        <div class="logo-circle">
            <i class="bi bi-people-fill"></i>
        </div>

        <div class="welcome-to">Welcome to</div>
        <h1>{{ $appName }}</h1>

        <p>{{ $appDesc ?: 'Manage your organisation members efficiently with a fast, secure, and modern platform.' }}</p>

        <div class="panel-left-footer">{{ $appName }}@if($appDesc) &nbsp;&middot;&nbsp; {{ $appDesc }}@endif</div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="panel-right">

        <h3>Sign in to your account</h3>
        <div class="sub">Enter your credentials to access the dashboard.</div>

        @if($errors->any())
        <div class="err-box">
            <i class="bi bi-shield-exclamation"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" autocomplete="on" id="loginForm">
            @csrf

            <div class="field">
                <label for="email">Email Address</label>
                <div class="field-inner">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" id="email"
                           value="{{ old('email') }}"
                           placeholder="Enter your email"
                           required autofocus autocomplete="email">
                </div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="field-inner">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" id="password"
                           placeholder="Enter your password"
                           required autocomplete="current-password">
                    <button type="button" class="toggle-btn" id="togglePwd" tabindex="-1">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="remember-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember me</label>
            </div>

            @if(isset($captcha) && $captcha['type'] !== 'none' && !empty($captcha['site_key']))
            <div class="captcha-wrap">
                @error('captcha')
                <div class="captcha-err"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                @enderror

                @if($captcha['type'] === 'recaptcha2')
                <div class="g-recaptcha" data-sitekey="{{ $captcha['site_key'] }}" data-theme="{{ $captcha['theme'] }}"></div>
                @elseif($captcha['type'] === 'hcaptcha')
                <div class="h-captcha" data-sitekey="{{ $captcha['site_key'] }}" data-theme="{{ $captcha['theme'] }}"></div>
                @elseif($captcha['type'] === 'turnstile')
                <div class="cf-turnstile" data-sitekey="{{ $captcha['site_key'] }}" data-theme="{{ $captcha['theme'] }}"></div>
                @elseif($captcha['type'] === 'recaptcha3')
                <input type="hidden" name="g-recaptcha-response" id="recaptcha3Token">
                @endif
            </div>
            @endif

            <button type="submit" class="btn-signin" id="loginBtn">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>

        </form>

        <div class="right-footer">
            <i class="bi bi-shield-lock"></i> Your connection is secured and encrypted
        </div>

    </div>
</div>

<script>
    document.getElementById('togglePwd').addEventListener('click', function () {
        var p = document.getElementById('password');
        var icon = document.getElementById('eyeIcon');
        if (p.type === 'password') {
            p.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            p.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });

    @if(isset($captcha) && $captcha['type'] === 'recaptcha3' && !empty($captcha['site_key']))
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        grecaptcha.ready(function() {
            grecaptcha.execute('{{ $captcha['site_key'] }}', {action: 'login'})
                .then(function(token) {
                    document.getElementById('recaptcha3Token').value = token;
                    form.submit();
                });
        });
    });
    @endif
</script>
</body>
</html>

