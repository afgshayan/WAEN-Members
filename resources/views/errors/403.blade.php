<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Denied | Nonprofit Members Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, #fff7ed 0%, #fff 45%, #fff7ed 100%);
            overflow: hidden;
        }

        /* Ambient blobs */
        .bg-blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }
        .bg-blob-1 {
            width: 480px; height: 480px;
            background: rgba(251,146,60,.18);
            top: -120px; left: -100px;
        }
        .bg-blob-2 {
            width: 340px; height: 340px;
            background: rgba(249,115,22,.12);
            bottom: -80px; right: -80px;
        }
        .bg-blob-3 {
            width: 220px; height: 220px;
            background: rgba(234,88,12,.09);
            top: 40%; left: 60%;
        }

        /* Card */
        .card {
            position: relative; z-index: 1;
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 30px 80px rgba(249,115,22,.13), 0 8px 24px rgba(0,0,0,.07);
            padding: 52px 56px 44px;
            max-width: 760px;
            width: calc(100% - 32px);
            text-align: center;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ea580c, #f97316, #fb923c);
        }

        /* Logo */
        .logo {
            display: inline-flex; align-items: center; gap: 9px;
            margin-bottom: 28px;
        }
        .logo-icon {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem; color: #fff; font-weight: 800;
        }
        .logo-text { font-size: .95rem; font-weight: 700; color: #1e293b; letter-spacing: 1.8px; }

        /* Big number */
        .err-num {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -4px;
            background: linear-gradient(135deg, #ea580c 0%, #f97316 50%, #fb923c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            position: relative;
        }

        /* Illustration area */
        .illustration { margin: 4px auto 20px; display: block; max-width: 520px; width: 100%; }

        /* Text */
        .err-title {
            font-size: 1.25rem; font-weight: 700;
            color: #1e293b; margin-bottom: 10px;
        }
        .err-sub {
            font-size: .88rem; color: #64748b;
            line-height: 1.7; max-width: 420px;
            margin: 0 auto 32px;
        }

        /* Buttons */
        .btns { display: flex; align-items: center; justify-content: center; gap: 12px; flex-wrap: wrap; }

        .btn-primary {
            padding: 11px 28px;
            background: linear-gradient(135deg, #ea580c, #f97316);
            color: #fff; border: none;
            border-radius: 50px;
            font-size: .875rem; font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer; text-decoration: none;
            letter-spacing: .3px;
            transition: opacity .2s, transform .15s;
            box-shadow: 0 4px 16px rgba(249,115,22,.35);
        }
        .btn-primary:hover { opacity: .88; transform: translateY(-1px); }

        .btn-secondary {
            padding: 10px 28px;
            background: transparent;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
            border-radius: 50px;
            font-size: .875rem; font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer; text-decoration: none;
            transition: border-color .2s, color .2s, transform .15s;
        }
        .btn-secondary:hover { border-color: #f97316; color: #f97316; transform: translateY(-1px); }

        /* Floating birds */
        .birds {
            position: absolute;
            pointer-events: none;
        }
        .birds-tl { top: 22px; left: 28px; opacity: .35; }
        .birds-tr { top: 32px; right: 38px; opacity: .25; transform: scaleX(-1); }

        /* Footer */
        .card-footer {
            margin-top: 28px;
            font-size: .72rem; color: #cbd5e1;
            letter-spacing: .3px;
        }
    </style>
</head>
<body>

<div class="bg-blob bg-blob-1"></div>
<div class="bg-blob bg-blob-2"></div>
<div class="bg-blob bg-blob-3"></div>

<div class="card">

    <!-- Decorative birds top-left -->
    <svg class="birds birds-tl" width="52" height="22" viewBox="0 0 52 22" fill="none">
        <path d="M2 8 C5 4, 9 4, 11 8" stroke="#f97316" stroke-width="1.8" fill="none" stroke-linecap="round"/>
        <path d="M16 4 C20 0, 24 0, 26 4" stroke="#f97316" stroke-width="1.8" fill="none" stroke-linecap="round"/>
        <path d="M32 11 C35 7, 39 7, 41 11" stroke="#fb923c" stroke-width="1.6" fill="none" stroke-linecap="round"/>
    </svg>

    <!-- Decorative birds top-right -->
    <svg class="birds birds-tr" width="44" height="18" viewBox="0 0 44 18" fill="none">
        <path d="M2 7 C5 3, 9 3, 11 7" stroke="#f97316" stroke-width="1.6" fill="none" stroke-linecap="round"/>
        <path d="M18 3 C22 0, 26 0, 28 3" stroke="#fb923c" stroke-width="1.5" fill="none" stroke-linecap="round"/>
    </svg>

    <!-- Logo -->
    <div class="logo">
        <div class="logo-icon">A</div>
        <span class="logo-text">Members Portal</span>
    </div>

    <!-- 403 number -->
    <div class="err-num">403</div>

    <!-- ─── SVG ILLUSTRATION ─── -->
    <svg class="illustration" viewBox="0 0 520 200" fill="none" xmlns="http://www.w3.org/2000/svg">

        <!-- Ground line -->
        <line x1="20" y1="172" x2="500" y2="172" stroke="#fed7aa" stroke-width="2"/>

        <!-- Background mountains / hills -->
        <ellipse cx="420" cy="172" rx="95" ry="32" fill="#fff7ed"/>
        <ellipse cx="100" cy="172" rx="70" ry="24" fill="#fff7ed"/>

        <!-- Background decorative building (distant) -->
        <rect x="58" y="132" width="28" height="40" rx="3" fill="#ffedd5"/>
        <rect x="64" y="125" width="16" height="10" rx="2" fill="#fed7aa"/>
        <rect x="62" y="138" width="6" height="7" rx="1" fill="#fdba74"/>
        <rect x="72" y="138" width="6" height="7" rx="1" fill="#fdba74"/>
        <rect x="62" y="150" width="6" height="7" rx="1" fill="#fdba74"/>
        <rect x="72" y="150" width="6" height="7" rx="1" fill="#fdba74"/>

        <!-- Background building 2 -->
        <rect x="440" y="140" width="24" height="32" rx="3" fill="#ffedd5"/>
        <rect x="444" y="133" width="16" height="10" rx="2" fill="#fed7aa"/>
        <rect x="444" y="145" width="5" height="6" rx="1" fill="#fdba74"/>
        <rect x="453" y="145" width="5" height="6" rx="1" fill="#fdba74"/>
        <rect x="444" y="156" width="5" height="6" rx="1" fill="#fdba74"/>
        <rect x="453" y="156" width="5" height="6" rx="1" fill="#fdba74"/>

        <!-- ─── LARGE SHIELD (center-right) ─── -->
        <!-- Shield body -->
        <path d="M310 38 L380 38 L380 118 Q345 148 310 118 Z" fill="url(#shieldGrad)" opacity="0.15" rx="4"/>
        <path d="M314 42 L376 42 L376 116 Q345 143 314 116 Z" fill="url(#shieldGrad2)" stroke="#f97316" stroke-width="2.5"/>
        <!-- Shield inner -->
        <path d="M322 52 L368 52 L368 112 Q345 132 322 112 Z" fill="#fff7ed" stroke="#fb923c" stroke-width="1.5"/>
        <!-- Lock on shield -->
        <rect x="333" y="82" width="24" height="20" rx="4" fill="#f97316"/>
        <path d="M337 82 L337 75 Q345 68 353 75 L353 82" stroke="#ea580c" stroke-width="3" fill="none" stroke-linecap="round"/>
        <circle cx="345" cy="91" r="3.5" fill="#fff"/>
        <rect x="343.5" y="91" width="3" height="5" rx="1" fill="#fff"/>
        <!-- Shield checkmark / X mark -->
        <path d="M335 67 L340 72 L355 57" stroke="#f97316" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" opacity="0.5"/>

        <!-- ─── PERSON / USER (left of shield) ─── -->
        <!-- Body -->
        <rect x="168" y="98" width="42" height="52" rx="10" fill="#fb923c"/>
        <!-- Head -->
        <circle cx="189" cy="82" r="18" fill="#fdba74"/>
        <!-- Hair -->
        <path d="M171 80 Q171 62 189 62 Q207 62 207 80" fill="#ea580c"/>
        <!-- Face - eyes -->
        <circle cx="183" cy="80" r="2.5" fill="#7c2d12"/>
        <circle cx="195" cy="80" r="2.5" fill="#7c2d12"/>
        <!-- Mouth - sad/surprised -->
        <path d="M184 89 Q189 86 194 89" stroke="#7c2d12" stroke-width="1.8" fill="none" stroke-linecap="round"/>
        <!-- Arms raised in surrender -->
        <path d="M168 108 L148 88" stroke="#fb923c" stroke-width="10" stroke-linecap="round"/>
        <path d="M210 108 L230 88" stroke="#fb923c" stroke-width="10" stroke-linecap="round"/>
        <!-- Legs -->
        <rect x="172" y="150" width="14" height="22" rx="7" fill="#fb923c"/>
        <rect x="192" y="150" width="14" height="22" rx="7" fill="#fb923c"/>
        <!-- Shoes -->
        <ellipse cx="179" cy="171" rx="10" ry="5" fill="#7c2d12"/>
        <ellipse cx="199" cy="171" rx="10" ry="5" fill="#7c2d12"/>
        <!-- Badge / ID card on chest -->
        <rect x="178" y="108" width="22" height="28" rx="4" fill="#fff7ed" stroke="#fdba74" stroke-width="1.5"/>
        <circle cx="189" cy="117" r="5" fill="#fed7aa"/>
        <rect x="182" y="126" width="14" height="2.5" rx="1.2" fill="#fdba74"/>
        <rect x="184" y="130" width="10" height="2" rx="1" fill="#fcd9aa"/>

        <!-- ─── EXCLAMATION / STOP SIGN (floating top right of shield area) ─── -->
        <circle cx="400" cy="52" r="22" fill="#fff7ed" stroke="#f97316" stroke-width="2.5"/>
        <rect x="397" y="38" width="6" height="16" rx="3" fill="#f97316"/>
        <circle cx="400" cy="60" r="3.5" fill="#f97316"/>

        <!-- ─── SMALL DECORATIVE PERSON (far left, distant) ─── -->
        <circle cx="42" cy="150" r="8" fill="#fed7aa"/>
        <rect x="37" y="158" width="10" height="14" rx="5" fill="#fb923c"/>
        <line x1="37" y1="162" x2="30" y2="170" stroke="#fb923c" stroke-width="4" stroke-linecap="round"/>
        <line x1="47" y1="162" x2="54" y2="170" stroke="#fb923c" stroke-width="4" stroke-linecap="round"/>

        <!-- ─── SMALL PERSON 2 (far right) ─── -->
        <circle cx="478" cy="152" r="7" fill="#fed7aa"/>
        <rect x="474" y="159" width="8" height="13" rx="4" fill="#fdba74"/>

        <!-- Ground shadows -->
        <ellipse cx="189" cy="173" rx="28" ry="5" fill="#fed7aa" opacity="0.4"/>
        <ellipse cx="345" cy="175" rx="38" ry="5" fill="#fed7aa" opacity="0.3"/>

        <!-- Floating sparkles -->
        <circle cx="270" cy="50" r="3" fill="#f97316" opacity="0.4"/>
        <circle cx="255" cy="75" r="2" fill="#fb923c" opacity="0.35"/>
        <circle cx="280" cy="90" r="2.5" fill="#f97316" opacity="0.3"/>
        <circle cx="240" cy="60" r="1.8" fill="#ea580c" opacity="0.45"/>

        <!-- Defs -->
        <defs>
            <linearGradient id="shieldGrad" x1="310" y1="38" x2="380" y2="148" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#f97316"/>
                <stop offset="100%" stop-color="#ea580c"/>
            </linearGradient>
            <linearGradient id="shieldGrad2" x1="314" y1="42" x2="376" y2="143" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#fb923c" stop-opacity="0.35"/>
                <stop offset="100%" stop-color="#f97316" stop-opacity="0.55"/>
            </linearGradient>
        </defs>
    </svg>
    <!-- ─── END ILLUSTRATION ─── -->

    <!-- Text -->
    <div class="err-title">{{ $title ?? 'Access Denied' }}</div>
    <p class="err-sub">
        {{ $message ?? "It looks like you don't have permission to view this page. Please contact your administrator." }}
    </p>

    <!-- Buttons -->
    <div class="btns">
        <a href="{{ url('/') }}" class="btn-primary">&#8594; Go to Home</a>
        <a href="javascript:history.back()" class="btn-secondary">Previous Page</a>
    </div>

    <div class="card-footer">Nonprofit Members Portal &nbsp;&middot;&nbsp; Member Management System</div>
</div>

</body>
</html>
