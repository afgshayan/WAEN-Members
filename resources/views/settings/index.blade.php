@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Advanced Settings')
@section('page-sub', 'Configure application, security, CAPTCHA and import/export options')
@section('breadcrumb')
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
@if(session('success'))
<div class="alert d-flex align-items-center gap-2 mb-4"
     style="background:#dcfce7; color:#15803d; border:none; border-radius:10px;">
    <i class="bi bi-check-circle-fill fs-5"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<form method="POST" action="{{ route('settings.update') }}" novalidate>
    @csrf
    @method('PUT')

    {{-- Tab navigation --}}
    <ul class="nav mb-4" id="settingsTabs" role="tablist"
        style="gap:.5rem; border:none;">
        @foreach([
            ['general',       'bi-gear-fill',       'General'],
            ['security',      'bi-shield-lock-fill', 'Security'],
            ['captcha',       'bi-robot',            'CAPTCHA'],
            ['import_export', 'bi-arrow-left-right', 'Import / Export'],
        ] as [$tab, $icon, $label])
        <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-2 {{ $loop->first ? 'active' : '' }}"
                    style="border-radius:8px; border:1px solid #e5e7eb; color:#374151; font-size:.875rem; font-weight:500;
                           padding:.45rem .9rem; background:#fff;"
                    id="tab-{{ $tab }}" data-bs-toggle="tab"
                    data-bs-target="#pane-{{ $tab }}" type="button" role="tab">
                <i class="bi {{ $icon }}"></i>{{ $label }}
            </button>
        </li>
        @endforeach
    </ul>

    <div class="tab-content">

        {{-- ====================== GENERAL ====================== --}}
        <div class="tab-pane fade show active" id="pane-general" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-gear-fill text-muted"></i>
                    <span>General Settings</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Application Name</label>
                            <input type="text" name="app_name" class="form-control @error('app_name') is-invalid @enderror"
                                   value="{{ old('app_name', $settings['app_name'] ?? 'Nonprofit Members Portal') }}" required maxlength="100">
                            @error('app_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Description</label>
                            <input type="text" name="app_description" class="form-control @error('app_description') is-invalid @enderror"
                                   value="{{ old('app_description', $settings['app_description'] ?? '') }}" maxlength="255">
                            @error('app_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Timezone</label>
                            <select name="timezone" class="form-select @error('timezone') is-invalid @enderror">
                                @foreach(timezone_identifiers_list() as $tz)
                                <option value="{{ $tz }}" {{ ($settings['timezone'] ?? 'UTC') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                @endforeach
                            </select>
                            @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Default Records Per Page</label>
                            <input type="number" name="per_page_default" class="form-control @error('per_page_default') is-invalid @enderror"
                                   value="{{ old('per_page_default', $settings['per_page_default'] ?? 100) }}" min="5" max="1000">
                            @error('per_page_default')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Date Format</label>
                            <select name="date_format" class="form-select @error('date_format') is-invalid @enderror">
                                @foreach(['Y-m-d' => 'YYYY-MM-DD', 'd/m/Y' => 'DD/MM/YYYY', 'm/d/Y' => 'MM/DD/YYYY', 'd.m.Y' => 'DD.MM.YYYY'] as $val => $lbl)
                                <option value="{{ $val }}" {{ ($settings['date_format'] ?? 'Y-m-d') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Root access page --}}
                        <div class="col-12 mt-2">
                            <hr class="my-1">
                            <p class="fw-semibold mb-3 mt-3" style="font-size:.875rem; color:#374151;">
                                <i class="bi bi-shield-x me-1 text-danger"></i>
                                Root Directory Access Page
                                <span class="text-muted fw-normal ms-1" style="font-size:.78rem;">
                                    Shown when someone opens the project root URL without <code>/public</code>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Page Title</label>
                            <input type="text" name="root_access_title"
                                   class="form-control @error('root_access_title') is-invalid @enderror"
                                   value="{{ old('root_access_title', $settings['root_access_title'] ?? 'Access Restricted') }}"
                                   maxlength="120" required>
                            @error('root_access_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Access Denied Message</label>
                            <textarea name="root_access_message" rows="3"
                                      class="form-control @error('root_access_message') is-invalid @enderror"
                                      maxlength="1000" required>{{ old('root_access_message', $settings['root_access_message'] ?? 'You do not have permission to access this area.') }}</textarea>
                            @error('root_access_message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text" style="font-size:.78rem;">
                                <i class="bi bi-lightbulb me-1 text-warning"></i>
                                Supports line breaks. The application login button appears automatically if APP_URL is set.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================== SECURITY ====================== --}}
        <div class="tab-pane fade" id="pane-security" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock-fill text-muted"></i>
                    <span>Security Settings</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Session Lifetime (minutes)</label>
                            <input type="number" name="session_lifetime" class="form-control @error('session_lifetime') is-invalid @enderror"
                                   value="{{ old('session_lifetime', $settings['session_lifetime'] ?? 120) }}" min="5" max="10080">
                            <div class="form-text" style="font-size:.78rem;">10080 min = 7 days</div>
                            @error('session_lifetime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">
                                Login URL Slug
                                <span class="badge ms-1" style="background:#fff7ed;color:#c2410c;font-size:.7rem;font-weight:600;">URL</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text text-muted" style="font-size:.8rem;">/</span>
                                <input type="text" name="login_slug"
                                       class="form-control @error('login_slug') is-invalid @enderror"
                                       value="{{ old('login_slug', $settings['login_slug'] ?? 'login') }}"
                                       maxlength="50" pattern="[a-zA-Z0-9\-_]+"
                                       placeholder="login">
                                @error('login_slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-text" style="font-size:.78rem;">
                                Login page URL: <code>{{ config('app.url') }}/<strong>{{ $settings['login_slug'] ?? 'login' }}</strong></code><br>
                                Only letters, numbers, <code>-</code> and <code>_</code>. Clear cache after changing.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Max Login Attempts</label>
                            <input type="number" name="login_max_attempts" class="form-control @error('login_max_attempts') is-invalid @enderror"
                                   value="{{ old('login_max_attempts', $settings['login_max_attempts'] ?? 5) }}" min="1" max="100">
                            @error('login_max_attempts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Lockout Duration (seconds)</label>
                            <input type="number" name="login_decay_seconds" class="form-control @error('login_decay_seconds') is-invalid @enderror"
                                   value="{{ old('login_decay_seconds', $settings['login_decay_seconds'] ?? 60) }}" min="10" max="3600">
                            @error('login_decay_seconds')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Remember Me Duration (days)</label>
                            <input type="number" name="remember_me_days" class="form-control @error('remember_me_days') is-invalid @enderror"
                                   value="{{ old('remember_me_days', $settings['remember_me_days'] ?? 30) }}" min="1" max="365">
                            @error('remember_me_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold d-block" style="font-size:.875rem;">Force HTTPS</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       name="force_https" id="force_https" value="1"
                                       {{ ($settings['force_https'] ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="force_https" style="font-size:.875rem;">
                                    Redirect all HTTP traffic to HTTPS
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================== CAPTCHA ====================== --}}
        <div class="tab-pane fade" id="pane-captcha" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-robot text-muted"></i>
                    <span>CAPTCHA Settings</span>
                </div>
                <div class="card-body p-4">
                    <div class="alert" style="background:#fefce8; color:#854d0e; border:none; border-radius:10px; font-size:.84rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        CAPTCHA is displayed on the login form to prevent automated attacks.
                        Select a provider and paste your credentials from their dashboard.
                    </div>

                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">CAPTCHA Provider</label>
                            <div class="row g-3" id="captchaProviders">
                                @foreach([
                                    ['none',       'bi-x-circle',       'None',                   'No CAPTCHA on login',                  '#6b7280'],
                                    ['recaptcha2', 'bi-google',         'reCAPTCHA v2',            'Google checkbox challenge',            '#4285f4'],
                                    ['recaptcha3', 'bi-google',         'reCAPTCHA v3',            'Invisible score-based protection',     '#34a853'],
                                    ['hcaptcha',   'bi-shield-check',   'hCaptcha',               'Privacy-focused alternative to Google','#ff6b35'],
                                    ['turnstile',  'bi-cloud-check',    'Cloudflare Turnstile',    'Smart invisible challenge',            '#f38020'],
                                ] as [$val, $icon, $name, $desc, $color])
                                <div class="col-md-4 col-lg-3">
                                    <label class="captcha-card p-3 rounded-3 d-block cursor-pointer"
                                           style="border:2px solid {{ ($settings['captcha_type'] ?? 'none') === $val ? $color : '#e5e7eb' }};
                                                  background:{{ ($settings['captcha_type'] ?? 'none') === $val ? 'rgba('.implode(',',sscanf(substr($color,1),"%02x%02x%02x")).',0.05)' : '#fff' }};
                                                  transition:all .2s;">
                                        <input type="radio" name="captcha_type" value="{{ $val }}"
                                               class="d-none captcha-radio"
                                               {{ ($settings['captcha_type'] ?? 'none') === $val ? 'checked' : '' }}>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="bi {{ $icon }}" style="color:{{ $color }}; font-size:1.1rem;"></i>
                                            <strong style="font-size:.875rem;">{{ $name }}</strong>
                                        </div>
                                        <div style="font-size:.76rem; color:#6b7280;">{{ $desc }}</div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6" id="siteKeyBlock"
                             style="{{ ($settings['captcha_type'] ?? 'none') === 'none' ? 'display:none' : '' }}">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Site Key <span class="text-danger">*</span></label>
                            <input type="text" name="captcha_site_key" id="captcha_site_key"
                                   class="form-control @error('captcha_site_key') is-invalid @enderror"
                                   value="{{ old('captcha_site_key', $settings['captcha_site_key'] ?? '') }}"
                                   maxlength="200" placeholder="Your public site key">
                            @error('captcha_site_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6" id="secretKeyBlock"
                             style="{{ ($settings['captcha_type'] ?? 'none') === 'none' ? 'display:none' : '' }}">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Secret Key <span class="text-danger">*</span></label>
                            <input type="password" name="captcha_secret_key" id="captcha_secret_key"
                                   class="form-control @error('captcha_secret_key') is-invalid @enderror"
                                   value="{{ old('captcha_secret_key', $settings['captcha_secret_key'] ?? '') }}"
                                   maxlength="200" placeholder="Your private secret key">
                            @error('captcha_secret_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Widget Theme</label>
                            <select name="captcha_theme" class="form-select">
                                <option value="light" {{ ($settings['captcha_theme'] ?? 'light') === 'light' ? 'selected' : '' }}>Light</option>
                                <option value="dark"  {{ ($settings['captcha_theme'] ?? 'light') === 'dark'  ? 'selected' : '' }}>Dark</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Widget Language</label>
                            <input type="text" name="captcha_language" class="form-control"
                                   value="{{ old('captcha_language', $settings['captcha_language'] ?? 'en') }}"
                                   maxlength="10" placeholder="en">
                            <div class="form-text" style="font-size:.78rem;">ISO 639-1 code, e.g. en, fa, de</div>
                        </div>

                        {{-- Test CAPTCHA button --}}
                        <div class="col-12" id="captchaTestBlock"
                             style="{{ ($settings['captcha_type'] ?? 'none') === 'none' ? 'display:none' : '' }}">
                            <div class="p-4 rounded-3" style="background:#f8fafc; border:1.5px dashed #e2e8f0;">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div>
                                        <div style="font-size:.875rem; font-weight:600; color:#1e293b; margin-bottom:3px;">
                                            <i class="bi bi-flask me-1" style="color:#f97316;"></i>Test Your CAPTCHA
                                        </div>
                                        <div style="font-size:.78rem; color:#64748b;">Enter your keys above, then click Test to verify they work correctly before saving.</div>
                                    </div>
                                    <button type="button" id="btnTestCaptcha"
                                            class="btn ms-auto"
                                            style="background:linear-gradient(135deg,#ea580c,#f97316);color:#fff;border:none;border-radius:50px;padding:9px 22px;font-size:.85rem;font-weight:600;">
                                        <i class="bi bi-play-circle me-1"></i>Run Test
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Provider quick-links --}}
                        <div class="col-12" id="captchaLinks" style="{{ ($settings['captcha_type'] ?? 'none') === 'none' ? 'display:none' : '' }}">
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                <a href="https://www.google.com/recaptcha/admin/create" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>reCAPTCHA Admin Console
                                </a>
                                <a href="https://www.hcaptcha.com/signup-interstitial" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>hCaptcha Dashboard
                                </a>
                                <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Cloudflare Turnstile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================== IMPORT / EXPORT ====================== --}}
        <div class="tab-pane fade" id="pane-import_export" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left-right text-muted"></i>
                    <span>Import / Export Settings</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Import Batch Size</label>
                            <input type="number" name="import_batch_size" class="form-control @error('import_batch_size') is-invalid @enderror"
                                   value="{{ old('import_batch_size', $settings['import_batch_size'] ?? 500) }}" min="50" max="5000">
                            <div class="form-text" style="font-size:.78rem;">Rows inserted per DB transaction</div>
                            @error('import_batch_size')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Export Chunk Size</label>
                            <input type="number" name="export_chunk_size" class="form-control @error('export_chunk_size') is-invalid @enderror"
                                   value="{{ old('export_chunk_size', $settings['export_chunk_size'] ?? 1000) }}" min="100" max="10000">
                            <div class="form-text" style="font-size:.78rem;">Rows read per query during export</div>
                            @error('export_chunk_size')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Max Upload Size (MB)</label>
                            <input type="number" name="max_upload_mb" class="form-control @error('max_upload_mb') is-invalid @enderror"
                                   value="{{ old('max_upload_mb', $settings['max_upload_mb'] ?? 50) }}" min="1" max="500">
                            <div class="form-text" style="font-size:.78rem;">Maximum allowed CSV file size</div>
                            @error('max_upload_mb')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end tab-content --}}

    {{-- Fixed save bar --}}
    <div class="mt-4 d-flex justify-content-end">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-floppy-fill me-1"></i>Save Settings
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
// ── Tab switching (plain JS, no Bootstrap dependency) ──────────────────────
(function () {
    var tabs  = document.querySelectorAll('[data-bs-toggle="tab"]');
    var panes = document.querySelectorAll('.tab-pane');

    tabs.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            // Deactivate all tabs & panes
            tabs.forEach(function (t) {
                t.classList.remove('active');
                t.style.background = '#fff';
                t.style.color      = '#374151';
                t.style.borderColor = '#e5e7eb';
            });
            panes.forEach(function (p) {
                p.classList.remove('show', 'active');
            });

            // Activate clicked tab
            btn.classList.add('active');
            btn.style.background  = '#fff7ed';
            btn.style.color       = '#ea580c';
            btn.style.borderColor = '#f97316';

            // Show target pane
            var target = document.querySelector(btn.getAttribute('data-bs-target'));
            if (target) {
                target.classList.add('show', 'active');
            }
        });

        // Apply active style to first tab on load
        if (btn.classList.contains('active')) {
            btn.style.background  = '#fff7ed';
            btn.style.color       = '#ea580c';
            btn.style.borderColor = '#f97316';
        }
    });
})();

// CAPTCHA provider card toggle
document.querySelectorAll('.captcha-radio').forEach(function(radio) {
    radio.closest('label').addEventListener('click', function() {
        var val = this.querySelector('input').value;

        // Reset all cards
        document.querySelectorAll('.captcha-card').forEach(function(c) {
            c.style.border = '2px solid #e5e7eb';
            c.style.background = '#fff';
        });

        // Highlight selected
        this.style.border = '2px solid #2563eb';
        this.style.background = 'rgba(37,99,235,0.05)';

        var show = val !== 'none';
        document.getElementById('siteKeyBlock').style.display    = show ? '' : 'none';
        document.getElementById('secretKeyBlock').style.display  = show ? '' : 'none';
        document.getElementById('captchaLinks').style.display    = show ? '' : 'none';
        document.getElementById('captchaTestBlock').style.display = show ? '' : 'none';
    });
});
</script>
@endpush

@push('modals')
{{-- ══ CAPTCHA TEST MODAL ══ --}}
<div id="captchaTestModal" style="display:none; position:fixed; inset:0; z-index:9999;
     background:rgba(0,0,0,.5); align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:20px; width:100%; max-width:480px;
                margin:16px; overflow:hidden;
                box-shadow:0 30px 80px rgba(0,0,0,.18);">
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid #f1f5f9;
                    display:flex; align-items:center; justify-content:space-between;">
            <div style="font-weight:700; color:#1e293b; font-size:.95rem;">
                <i class="bi bi-flask me-2" style="color:#f97316;"></i>CAPTCHA Test
            </div>
            <button type="button" id="closeCaptchaModal"
                    style="background:none;border:none;font-size:1.2rem;color:#94a3b8;cursor:pointer;line-height:1;">&#10005;</button>
        </div>
        <div style="padding:1.5rem;">
            <p style="font-size:.83rem;color:#64748b;margin-bottom:1.25rem;line-height:1.6;">
                Solve the CAPTCHA below using your <strong>Site Key</strong>, then click <strong>Verify</strong> to confirm your Secret Key is also valid.
            </p>
            <div id="captchaWidgetWrap" style="min-height:78px; display:flex; align-items:center; justify-content:center;"></div>
            <input type="hidden" id="captchaTestToken">
            <div id="captchaTestResult" style="display:none; margin-top:1rem; padding:.75rem 1rem;
                 border-radius:10px; font-size:.83rem; font-weight:500;"></div>
        </div>
        <div style="padding:1rem 1.5rem; border-top:1px solid #f1f5f9;
                    display:flex; justify-content:flex-end; gap:.75rem;">
            <button type="button" id="cancelCaptchaModal"
                    style="background:#f8fafc;border:1.5px solid #e2e8f0;color:#64748b;
                           border-radius:50px;padding:8px 20px;font-size:.83rem;font-weight:500;cursor:pointer;">Cancel</button>
            <button type="button" id="verifyCaptchaBtn"
                    style="background:linear-gradient(135deg,#ea580c,#f97316);color:#fff;
                           border:none;border-radius:50px;padding:8px 22px;
                           font-size:.83rem;font-weight:600;cursor:pointer;opacity:.5;" disabled>
                <i class="bi bi-shield-check me-1"></i>Verify
            </button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
(function () {
    var testBtn     = document.getElementById('btnTestCaptcha');
    var modal       = document.getElementById('captchaTestModal');
    var closeBtn    = document.getElementById('closeCaptchaModal');
    var cancelBtn   = document.getElementById('cancelCaptchaModal');
    var verifyBtn   = document.getElementById('verifyCaptchaBtn');
    var resultBox   = document.getElementById('captchaTestResult');
    var tokenInput  = document.getElementById('captchaTestToken');
    var widgetWrap  = document.getElementById('captchaWidgetWrap');
    var scriptLoaded = {};
    var widgetId    = null;

    function getCaptchaType()   { var el = document.querySelector('input[name=captcha_type]:checked'); return el ? el.value : ''; }
    function getSiteKey()       { return (document.getElementById('captcha_site_key')   || {}).value || ''; }
    function getSecretKey()     { return (document.getElementById('captcha_secret_key') || {}).value || ''; }

    function showResult(success, message) {
        resultBox.style.display = 'block';
        resultBox.style.background  = success ? '#dcfce7' : '#fef2f2';
        resultBox.style.color       = success ? '#15803d' : '#b91c1c';
        resultBox.style.borderLeft  = '3px solid ' + (success ? '#22c55e' : '#ef4444');
        resultBox.innerHTML = (success ? '&#10003; ' : '&#10005; ') + message;
    }

    function closeModal() {
        modal.style.display = 'none';
        widgetWrap.innerHTML = '';
        tokenInput.value = '';
        resultBox.style.display = 'none';
        verifyBtn.disabled = true;
        verifyBtn.style.opacity = '.5';
        widgetId = null;
    }

    if (closeBtn)  closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });

    function onTokenReceived(token) {
        tokenInput.value = token;
        verifyBtn.disabled = false;
        verifyBtn.style.opacity = '1';
    }

    function loadCaptchaWidget(type, siteKey) {
        widgetWrap.innerHTML = '';
        tokenInput.value = '';
        verifyBtn.disabled = true;
        verifyBtn.style.opacity = '.5';

        if (!siteKey) { showResult(false, 'Please enter your Site Key first.'); return; }

        // ── Build the inner widget div ───────────────────────────────────────
        function createInnerDiv() {
            widgetWrap.innerHTML = '';
            var div = document.createElement('div');
            div.id = 'captchaWidgetInner';
            widgetWrap.appendChild(div);
            return div;
        }

        // ── Render after the library is confirmed ready ──────────────────────
        function renderWidget() {
            createInnerDiv();
            try {
                if (type === 'recaptcha2') {
                    // Use grecaptcha.ready() — fires only when render() is available
                    grecaptcha.ready(function () {
                        widgetId = grecaptcha.render('captchaWidgetInner', {
                            sitekey: siteKey,
                            callback: onTokenReceived,
                            'expired-callback': function () {
                                verifyBtn.disabled = true;
                                verifyBtn.style.opacity = '.5';
                                tokenInput.value = '';
                            }
                        });
                    });
                } else if (type === 'hcaptcha') {
                    widgetId = hcaptcha.render('captchaWidgetInner', {
                        sitekey: siteKey,
                        callback: onTokenReceived
                    });
                } else if (type === 'turnstile') {
                    widgetId = turnstile.render('#captchaWidgetInner', {
                        sitekey: siteKey,
                        callback: onTokenReceived,
                        'error-callback': function () { showResult(false, 'Turnstile error. Check your Site Key.'); }
                    });
                }
            } catch (e) {
                showResult(false, 'Widget render error: ' + e.message);
            }
        }

        // ── reCAPTCHA v3 — invisible ─────────────────────────────────────────
        if (type === 'recaptcha3') {
            widgetWrap.innerHTML = '<div style="color:#64748b;font-size:.82rem;padding:10px 0;">'
                + '<i class="bi bi-info-circle me-1"></i>'
                + 'reCAPTCHA v3 is invisible. Click Verify to test your Secret Key with a generated token.</div>';
            function execV3() {
                grecaptcha.ready(function () {
                    grecaptcha.execute(siteKey, { action: 'test' })
                        .then(onTokenReceived)
                        .catch(function () {
                            showResult(false, 'Could not execute reCAPTCHA v3. Check your Site Key.');
                        });
                });
            }
            if (window.grecaptcha) { execV3(); return; }
            var sv3 = document.createElement('script');
            sv3.src = 'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent(siteKey);
            sv3.onerror = function () { showResult(false, 'Failed to load reCAPTCHA v3 script.'); };
            sv3.onload = execV3;
            document.head.appendChild(sv3);
            return;
        }

        // ── reCAPTCHA v2 / hCaptcha / Turnstile ─────────────────────────────
        // Use the library's own onload callback param to know it is truly ready.
        var cbName   = '__captchaWidgetReady_' + Date.now();
        var scriptUrl;
        if (type === 'recaptcha2') scriptUrl = 'https://www.google.com/recaptcha/api.js?render=explicit&onload=' + cbName;
        if (type === 'hcaptcha')   scriptUrl = 'https://js.hcaptcha.com/1/api.js?render=explicit&onload=' + cbName;
        if (type === 'turnstile')  scriptUrl = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=' + cbName;

        if (!scriptUrl) { showResult(false, 'Unsupported CAPTCHA type.'); return; }

        // If the library is already loaded, render immediately
        var alreadyLoaded = (type === 'recaptcha2' && window.grecaptcha && typeof grecaptcha.render === 'function')
                         || (type === 'hcaptcha'   && window.hcaptcha)
                         || (type === 'turnstile'  && window.turnstile);

        if (alreadyLoaded) {
            renderWidget();
            return;
        }

        widgetWrap.innerHTML = '<div style="color:#64748b;font-size:.82rem;">Loading CAPTCHA widget…</div>';

        // Register the global onload callback the CDN will call
        window[cbName] = function () {
            delete window[cbName];
            renderWidget();
        };

        var s = document.createElement('script');
        s.src = scriptUrl;
        s.onerror = function () {
            delete window[cbName];
            showResult(false, 'Failed to load CAPTCHA script. Check your Site Key or network.');
        };
        document.head.appendChild(s);
    }

    if (testBtn) {
        testBtn.addEventListener('click', function () {
            var type  = getCaptchaType();
            var sKey  = getSiteKey();
            var secKey = getSecretKey();

            if (!type || type === 'none') { alert('Please select a CAPTCHA provider first.'); return; }
            if (!sKey)   { alert('Please enter your Site Key first.'); return; }
            if (!secKey) { alert('Please enter your Secret Key first.'); return; }

            resultBox.style.display = 'none';
            modal.style.display = 'flex';
            loadCaptchaWidget(type, sKey);
        });
    }

    if (verifyBtn) {
        verifyBtn.addEventListener('click', function () {
            var token  = tokenInput.value;
            var type   = getCaptchaType();
            var sKey   = getSiteKey();
            var secKey = getSecretKey();

            if (!token) { showResult(false, 'No CAPTCHA token yet. Please solve the challenge first.'); return; }

            verifyBtn.disabled = true;
            verifyBtn.style.opacity = '.5';
            verifyBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Verifying…';
            resultBox.style.display = 'none';

            fetch('{{ route('settings.test-captcha') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    type:       type,
                    site_key:   sKey,
                    secret_key: secKey,
                    token:      token,
                }),
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                showResult(data.success, data.message);
            })
            .catch(function(e) {
                showResult(false, 'Network error: ' + e.message);
            })
            .finally(function() {
                verifyBtn.disabled = false;
                verifyBtn.style.opacity = '1';
                verifyBtn.innerHTML = '<i class="bi bi-shield-check me-1"></i>Verify';
            });
        });
    }
})();
</script>
@endpush
