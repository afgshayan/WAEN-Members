<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>404 — Page Not Found | Nonprofit Members Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        html,body{height:100%;}
        body{
            font-family:'Inter',sans-serif;
            min-height:100vh;
            background:linear-gradient(180deg,#fff 0%,#fff7ed 60%,#fed7aa 100%);
            display:flex;flex-direction:column;
            align-items:center;
            overflow-x:hidden;
        }

        /* ── Logo bar ── */
        .topbar{
            width:100%;display:flex;align-items:center;justify-content:center;
            padding:28px 0 0;gap:10px;
        }
        .logo-icon{
            width:34px;height:34px;
            background:linear-gradient(135deg,#f97316,#ea580c);
            border-radius:9px;
            display:flex;align-items:center;justify-content:center;
            font-size:.85rem;font-weight:800;color:#fff;flex-shrink:0;
        }
        .logo-text{font-size:.95rem;font-weight:700;color:#1e293b;letter-spacing:1.8px;}

        /* ── Center text block ── */
        .content{
            text-align:center;
            padding:32px 24px 8px;
            position:relative;z-index:2;
        }
        .err-num{
            font-size:clamp(5.5rem,15vw,9rem);
            font-weight:900;line-height:1;letter-spacing:-4px;
            background:linear-gradient(135deg,#ea580c 0%,#f97316 50%,#fb923c 100%);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
            margin-bottom:4px;
        }
        .err-title{
            font-size:clamp(.95rem,2.5vw,1.15rem);
            font-weight:600;color:#374151;
            margin-bottom:10px;letter-spacing:.2px;
        }
        .err-sub{
            font-size:clamp(.8rem,2vw,.875rem);
            color:#6b7280;line-height:1.7;
            max-width:460px;margin:0 auto 28px;
        }

        /* ── Buttons ── */
        .btns{display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;}
        .btn-primary{
            padding:11px 28px;
            background:linear-gradient(135deg,#ea580c,#f97316);
            color:#fff;border:none;border-radius:50px;
            font-size:.875rem;font-weight:600;font-family:'Inter',sans-serif;
            cursor:pointer;text-decoration:none;letter-spacing:.3px;
            transition:opacity .2s,transform .15s;
            box-shadow:0 4px 16px rgba(249,115,22,.35);
        }
        .btn-primary:hover{opacity:.88;transform:translateY(-1px);}
        .btn-secondary{
            padding:10px 28px;
            background:transparent;color:#64748b;
            border:1.5px solid #e2e8f0;border-radius:50px;
            font-size:.875rem;font-weight:500;font-family:'Inter',sans-serif;
            cursor:pointer;text-decoration:none;
            transition:border-color .2s,color .2s,transform .15s;
        }
        .btn-secondary:hover{border-color:#f97316;color:#f97316;transform:translateY(-1px);}

        /* ── Decorative birds ── */
        .birds{position:absolute;pointer-events:none;}
        .birds-tl{top:18px;left:24px;opacity:.3;}
        .birds-tr{top:26px;right:32px;opacity:.22;transform:scaleX(-1);}

        /* ── Illustration ── */
        .illustration-wrap{
            width:100%;max-width:820px;
            margin-top:auto;position:relative;z-index:1;flex-shrink:0;
        }
        .illustration-wrap svg{width:100%;display:block;}

        /* ── Footer ── */
        .page-footer{
            position:absolute;bottom:14px;left:0;right:0;
            text-align:center;font-size:.7rem;color:#d1d5db;letter-spacing:.4px;z-index:3;
        }
    </style>
</head>
<body>

<!-- Logo -->
<div class="topbar">
    <div class="logo-icon">A</div>
    <span class="logo-text">Members Portal</span>
</div>

<!-- Decorative birds -->
<svg class="birds birds-tl" width="56" height="24" viewBox="0 0 56 24" fill="none">
    <path d="M2 9C5 5,9 5,11 9" stroke="#f97316" stroke-width="2" fill="none" stroke-linecap="round"/>
    <path d="M17 5C21 1,25 1,27 5" stroke="#f97316" stroke-width="2" fill="none" stroke-linecap="round"/>
    <path d="M34 12C37 8,41 8,43 12" stroke="#fb923c" stroke-width="1.7" fill="none" stroke-linecap="round"/>
</svg>
<svg class="birds birds-tr" width="46" height="20" viewBox="0 0 46 20" fill="none">
    <path d="M2 8C5 4,9 4,11 8" stroke="#f97316" stroke-width="1.8" fill="none" stroke-linecap="round"/>
    <path d="M18 4C22 1,26 1,28 4" stroke="#fb923c" stroke-width="1.6" fill="none" stroke-linecap="round"/>
</svg>

<!-- Center content -->
<div class="content">
    <div class="err-num">404</div>
    <div class="err-title">Page Not Found</div>
    <p class="err-sub">Looks like you took a wrong turn. The page you're looking for doesn't exist or has been moved.</p>
    <div class="btns">
        <a href="{{ url('/dashboard') }}" class="btn-primary">&#8594; Go to Home</a>
        <a href="javascript:history.back()" class="btn-secondary">Previous Page</a>
    </div>
</div>

<!-- ── ILLUSTRATION SCENE ── -->
<div class="illustration-wrap">
<svg viewBox="0 0 820 340" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="skyG" x1="0" y1="0" x2="0" y2="340" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#fff7ed" stop-opacity="0"/>
            <stop offset="100%" stop-color="#fed7aa" stop-opacity=".6"/>
        </linearGradient>
        <linearGradient id="signGrad" x1="480" y1="100" x2="640" y2="260" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#fb923c"/>
            <stop offset="100%" stop-color="#ea580c"/>
        </linearGradient>
        <radialGradient id="glow2" cx="560" cy="200" r="130" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#f97316" stop-opacity=".14"/>
            <stop offset="100%" stop-color="#f97316" stop-opacity="0"/>
        </radialGradient>
        <filter id="shadow2">
            <feDropShadow dx="0" dy="6" stdDeviation="10" flood-color="#f97316" flood-opacity=".2"/>
        </filter>
    </defs>

    <rect width="820" height="340" fill="url(#skyG)"/>
    <ellipse cx="560" cy="210" rx="140" ry="120" fill="url(#glow2)"/>

    <!-- Background hills -->
    <ellipse cx="100" cy="322" rx="140" ry="38" fill="#ffedd5" opacity=".7"/>
    <ellipse cx="720" cy="320" rx="150" ry="40" fill="#ffedd5" opacity=".6"/>
    <ellipse cx="400" cy="325" rx="220" ry="28" fill="#fff7ed" opacity=".5"/>

    <!-- Distant building left -->
    <rect x="45" y="228" width="32" height="72" rx="3" fill="#ffedd5"/>
    <rect x="51" y="219" width="20" height="12" rx="2" fill="#fed7aa"/>
    <rect x="49" y="234" width="7" height="8" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="61" y="234" width="7" height="8" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="49" y="247" width="7" height="8" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="61" y="247" width="7" height="8" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="49" y="260" width="7" height="8" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="61" y="260" width="7" height="8" rx="1.5" fill="#fdba74" opacity=".7"/>

    <!-- Distant building right -->
    <rect x="740" y="238" width="28" height="62" rx="3" fill="#ffedd5"/>
    <rect x="746" y="230" width="16" height="10" rx="2" fill="#fed7aa"/>
    <rect x="744" y="244" width="6" height="7" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="754" y="244" width="6" height="7" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="744" y="256" width="6" height="7" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="754" y="256" width="6" height="7" rx="1.5" fill="#fdba74" opacity=".7"/>

    <!-- Ground line -->
    <line x1="0" y1="300" x2="820" y2="300" stroke="#fed7aa" stroke-width="2.5"/>

    <!-- ── ROAD / PATH ── -->
    <!-- Road going toward horizon with question mark at end -->
    <path d="M330 300 Q410 300 430 230 Q445 200 470 190" stroke="#fed7aa" stroke-width="28" stroke-linecap="round" fill="none"/>
    <path d="M330 300 Q410 300 430 230 Q445 200 470 190" stroke="#fff7ed" stroke-width="16" stroke-linecap="round" fill="none" stroke-dasharray="18 12"/>

    <!-- ── GIANT QUESTION MARK (center-right, friendly) ── -->
    <!-- Q mark top arc -->
    <path d="M540 100 Q540 68 575 68 Q610 68 610 100 Q610 125 580 135 L580 158" stroke="url(#signGrad)" stroke-width="22" fill="none" stroke-linecap="round" filter="url(#shadow2)"/>
    <circle cx="580" cy="178" r="12" fill="url(#signGrad)" filter="url(#shadow2)"/>
    <!-- Highlight on Q mark -->
    <path d="M555 88 Q558 74 575 72" stroke="rgba(255,255,255,.35)" stroke-width="7" fill="none" stroke-linecap="round"/>

    <!-- Shadow on ground -->
    <ellipse cx="580" cy="300" rx="60" ry="10" fill="#fed7aa" opacity=".4"/>

    <!-- ── PERSON (left, holding map, confused) ── -->
    <ellipse cx="240" cy="300" rx="38" ry="8" fill="#fed7aa" opacity=".4"/>
    <!-- Legs -->
    <rect x="224" y="255" width="15" height="38" rx="7.5" fill="#fb923c"/>
    <rect x="243" y="255" width="15" height="38" rx="7.5" fill="#fb923c"/>
    <!-- Shoes -->
    <ellipse cx="231" cy="293" rx="13" ry="5.5" fill="#7c2d12"/>
    <ellipse cx="250" cy="293" rx="13" ry="5.5" fill="#7c2d12"/>
    <!-- Body -->
    <rect x="216" y="198" width="50" height="62" rx="13" fill="#fb923c"/>
    <!-- Shirt detail -->
    <path d="M228 198 L241 210 L254 198" fill="#ea580c" opacity=".45"/>
    <!-- ID badge -->
    <rect x="224" y="214" width="24" height="30" rx="4" fill="#fff7ed" stroke="#fdba74" stroke-width="1.5"/>
    <circle cx="236" cy="224" r="5" fill="#fed7aa"/>
    <rect x="228" y="232" width="15" height="2.5" rx="1.2" fill="#fdba74"/>
    <rect x="230" y="237" width="11" height="2" rx="1" fill="#fcd9aa"/>
    <!-- Head -->
    <circle cx="241" cy="178" r="21" fill="#fdba74"/>
    <!-- Hair -->
    <path d="M220 176 Q220 155 241 155 Q262 155 262 176" fill="#7c2d12"/>
    <!-- Eyes (wide, confused) -->
    <circle cx="234" cy="176" r="3.5" fill="#431407"/>
    <circle cx="248" cy="176" r="3.5" fill="#431407"/>
    <!-- Eyebrows up / confused -->
    <path d="M231 169 Q234 165 237 168" stroke="#431407" stroke-width="1.8" fill="none" stroke-linecap="round"/>
    <path d="M245 168 Q248 165 251 169" stroke="#431407" stroke-width="1.8" fill="none" stroke-linecap="round"/>
    <!-- Mouth (open, confused) -->
    <ellipse cx="241" cy="188" rx="5" ry="4" fill="#c2410c" opacity=".4"/>
    <!-- Left arm holding map -->
    <path d="M216 218 Q195 230 183 250" stroke="#fb923c" stroke-width="12" stroke-linecap="round" fill="none"/>
    <!-- Right arm up scratching head -->
    <path d="M266 215 Q275 200 265 185" stroke="#fb923c" stroke-width="12" stroke-linecap="round" fill="none"/>
    <circle cx="265" cy="182" r="8.5" fill="#fdba74"/>

    <!-- ── MAP in hand ── -->
    <rect x="155" y="245" width="50" height="38" rx="5" fill="#fff7ed" stroke="#fdba74" stroke-width="2" transform="rotate(-15,180,264)"/>
    <!-- Map lines -->
    <path d="M163 256 Q172 252 180 258 Q188 264 196 260" stroke="#fdba74" stroke-width="1.8" fill="none" stroke-linecap="round" transform="rotate(-15,180,264)"/>
    <path d="M165 264 Q175 268 185 264" stroke="#fcd9aa" stroke-width="1.5" fill="none" stroke-linecap="round" transform="rotate(-15,180,264)"/>
    <!-- Small X on map -->
    <text x="183" y="263" font-size="10" fill="#f97316" font-weight="700" transform="rotate(-15,180,264)">?</text>

    <!-- ── Signpost (right side, pointing nowhere) ── -->
    <!-- Post -->
    <rect x="695" y="195" width="10" height="105" rx="5" fill="#fdba74"/>
    <!-- Signs pointing different ways -->
    <rect x="650" y="195" width="80" height="24" rx="5" fill="#f97316"/>
    <polygon points="650,207 642,195 650,183" fill="#f97316"/>
    <text x="660" y="212" font-size="10" fill="#fff" font-weight="600">THIS WAY</text>

    <rect x="705" y="228" width="75" height="22" rx="5" fill="#fb923c"/>
    <polygon points="780,228 788,239 780,250" fill="#fb923c"/>
    <text x="710" y="243" font-size="10" fill="#fff" font-weight="600">THAT WAY</text>

    <rect x="658" y="260" width="68" height="22" rx="5" fill="#ea580c"/>
    <polygon points="658,260 650,271 658,282" fill="#ea580c"/>
    <text x="663" y="275" font-size="10" fill="#fff" font-weight="600">??? WAY</text>

    <!-- Ground shadow signpost -->
    <ellipse cx="700" cy="300" rx="25" ry="6" fill="#fed7aa" opacity=".4"/>

    <!-- ── Decorative bushes ── -->
    <circle cx="760" cy="296" r="15" fill="#fed7aa" opacity=".8"/>
    <circle cx="776" cy="291" r="11" fill="#fdba74" opacity=".55"/>
    <circle cx="748" cy="293" r="10" fill="#ffedd5"/>

    <circle cx="60" cy="296" r="9" fill="#fed7aa" opacity=".7"/>
    <circle cx="74" cy="292" r="13" fill="#ffedd5"/>
    <circle cx="50" cy="294" r="8" fill="#fdba74" opacity=".6"/>

    <!-- Sparkles -->
    <circle cx="440" cy="135" r="4" fill="#f97316" opacity=".28"/>
    <circle cx="425" cy="160" r="2.5" fill="#fb923c" opacity=".32"/>
    <circle cx="455" cy="168" r="3" fill="#ea580c" opacity=".24"/>
    <circle cx="320" cy="155" r="3" fill="#f97316" opacity=".26"/>

    <!-- Cloud left -->
    <ellipse cx="125" cy="108" rx="40" ry="16" fill="#fff7ed" opacity=".8"/>
    <ellipse cx="104" cy="116" rx="22" ry="13" fill="#fff7ed" opacity=".7"/>
    <ellipse cx="150" cy="116" rx="24" ry="12" fill="#fff7ed" opacity=".7"/>

    <!-- Cloud right -->
    <ellipse cx="685" cy="92" rx="34" ry="14" fill="#fff7ed" opacity=".75"/>
    <ellipse cx="666" cy="99" rx="19" ry="12" fill="#fff7ed" opacity=".65"/>
    <ellipse cx="708" cy="99" rx="20" ry="11" fill="#fff7ed" opacity=".65"/>

</svg>
</div>

<div class="page-footer">Nonprofit Members Portal &nbsp;&middot;&nbsp; Member Database Management</div>
</body>
</html>
