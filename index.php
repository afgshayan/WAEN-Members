<?php
/**
 * Nonprofit Members Portal â€” Root index guard
 *
 * - Not installed  â†’ redirect to /public/install/
 * - Installed      â†’ show configurable "access denied" page (reads from DB settings)
 *
 * This file sits at the project root so direct access to the root directory
 * always hits this file instead of exposing the directory listing.
 */

define('ROOT',  __DIR__);
define('LOCK',  ROOT . '/storage/installed.lock');
define('ENVF',  ROOT . '/.env');

// â”€â”€ Detect base URL from this file's location â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function baseUrl(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Path to this file relative to document root
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    // Remove /index.php from the end
    $base   = rtrim(dirname($script), '/');
    return $scheme . '://' . $host . $base;
}

// â”€â”€ Not installed yet â†’ go to web installer â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
if (!file_exists(LOCK)) {
    $url = baseUrl() . '/public/install/';
    header('Location: ' . $url, true, 302);
    exit;
}

// â”€â”€ Installed â†’ show configurable access-denied page â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$title   = 'Access Restricted';
$message = 'You do not have permission to access this area. Please use the application link provided by your administrator.';
$appUrl  = '';

// Try to read customised message from settings table
if (file_exists(ENVF)) {
    try {
        $env  = parseEnv(ENVF);
        $host = $env['DB_HOST']     ?? '127.0.0.1';
        $port = $env['DB_PORT']     ?? '3306';
        $db   = $env['DB_DATABASE'] ?? '';
        $user = $env['DB_USERNAME'] ?? 'root';
        $pass = $env['DB_PASSWORD'] ?? '';
        $appUrl     = rtrim($env['APP_URL'] ?? '', '/');

        if ($db) {
            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT]);

            $stmt = $pdo->query(
                "SELECT `key`, `value` FROM `settings`
                 WHERE `key` IN ('root_access_title','root_access_message')
                 LIMIT 2"
            );
            if ($stmt) {
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if ($row['key'] === 'root_access_title'   && !empty($row['value'])) $title   = $row['value'];
                    if ($row['key'] === 'root_access_message' && !empty($row['value'])) $message = $row['value'];
                }
            }
        }
    } catch (\Throwable) {
        // silently ignore â€” use defaults
    }
}

// â”€â”€ Minimal .env parser â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function parseEnv(string $path): array
{
    $result = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (!str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $val = trim($val, " \t\"'");
        $result[trim($key)] = $val;
    }
    return $result;
}

http_response_code(403);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($title) ?></title>
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
    position:relative;
}

/* â”€â”€ Logo bar â”€â”€ */
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

/* â”€â”€ Center text block â”€â”€ */
.content{
    text-align:center;
    padding:32px 24px 8px;
    position:relative;z-index:2;
}
.err-num{
    font-size:clamp(5.5rem,15vw,9rem);
    font-weight:900;
    line-height:1;
    letter-spacing:-4px;
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
    max-width:460px;margin:0 auto;
}

/* â”€â”€ Decorative birds â”€â”€ */
.birds{position:absolute;pointer-events:none;}
.birds-tl{top:18px;left:24px;opacity:.3;}
.birds-tr{top:26px;right:32px;opacity:.22;transform:scaleX(-1);}

/* â”€â”€ Illustration â”€â”€ */
.illustration-wrap{
    width:100%;max-width:820px;
    margin-top:auto;
    position:relative;z-index:1;
    flex-shrink:0;
}
.illustration-wrap svg{width:100%;display:block;}

/* â”€â”€ Footer â”€â”€ */
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
    <div class="err-num">403</div>
    <div class="err-title"><?= htmlspecialchars($title) ?></div>
    <p class="err-sub"><?= nl2br(htmlspecialchars($message)) ?></p>
</div>

<!-- â”€â”€ ILLUSTRATION SCENE â”€â”€ -->
<div class="illustration-wrap">
<svg viewBox="0 0 820 340" fill="none" xmlns="http://www.w3.org/2000/svg">

    <!-- Sky gradient bg -->
    <defs>
        <linearGradient id="skyGrad" x1="0" y1="0" x2="0" y2="340" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#fff7ed" stop-opacity="0"/>
            <stop offset="100%" stop-color="#fed7aa" stop-opacity=".6"/>
        </linearGradient>
        <linearGradient id="lockGrad" x1="490" y1="60" x2="680" y2="300" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#fb923c"/>
            <stop offset="100%" stop-color="#ea580c"/>
        </linearGradient>
        <linearGradient id="lockLight" x1="490" y1="60" x2="680" y2="300" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#fed7aa" stop-opacity=".7"/>
            <stop offset="100%" stop-color="#f97316" stop-opacity=".15"/>
        </linearGradient>
        <radialGradient id="glowGrad" cx="580" cy="200" r="120" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#f97316" stop-opacity=".18"/>
            <stop offset="100%" stop-color="#f97316" stop-opacity="0"/>
        </radialGradient>
        <filter id="softShadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#f97316" flood-opacity=".2"/>
        </filter>
    </defs>

    <rect width="820" height="340" fill="url(#skyGrad)"/>

    <!-- Glow behind lock -->
    <ellipse cx="580" cy="200" rx="130" ry="115" fill="url(#glowGrad)"/>

    <!-- â”€â”€ Background mountains â”€â”€ -->
    <ellipse cx="120" cy="320" rx="130" ry="40" fill="#ffedd5" opacity=".7"/>
    <ellipse cx="700" cy="318" rx="160" ry="42" fill="#ffedd5" opacity=".6"/>
    <ellipse cx="410" cy="322" rx="200" ry="30" fill="#fff7ed" opacity=".5"/>

    <!-- Distant building left -->
    <rect x="50" y="225" width="36" height="75" rx="3" fill="#ffedd5"/>
    <rect x="57" y="216" width="22" height="12" rx="2" fill="#fed7aa"/>
    <rect x="55" y="232" width="8" height="9" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="68" y="232" width="8" height="9" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="55" y="246" width="8" height="9" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="68" y="246" width="8" height="9" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="55" y="260" width="8" height="9" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="68" y="260" width="8" height="9" rx="1.5" fill="#fdba74" opacity=".7"/>

    <!-- Distant building right -->
    <rect x="730" y="235" width="30" height="65" rx="3" fill="#ffedd5"/>
    <rect x="736" y="226" width="18" height="12" rx="2" fill="#fed7aa"/>
    <rect x="734" y="242" width="7" height="8" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="745" y="242" width="7" height="8" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="734" y="255" width="7" height="8" rx="1.5" fill="#fdba74" opacity=".7"/>
    <rect x="745" y="255" width="7" height="8" rx="1.5" fill="#fdba74" opacity=".7"/>

    <!-- Ground line -->
    <line x1="0" y1="300" x2="820" y2="300" stroke="#fed7aa" stroke-width="2.5"/>

    <!-- â”€â”€ GIANT PADLOCK (center-right) â”€â”€ -->
    <!-- Lock body -->
    <rect x="510" y="155" width="150" height="130" rx="18" fill="url(#lockGrad)" filter="url(#softShadow)"/>
    <!-- Lock shackle (arc) -->
    <path d="M540 155 L540 115 Q585 75 630 115 L630 155" stroke="#ea580c" stroke-width="18" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M540 155 L540 115 Q585 75 630 115 L630 155" stroke="#fb923c" stroke-width="10" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
    <!-- Lock body highlight -->
    <rect x="510" y="155" width="150" height="130" rx="18" fill="url(#lockLight)"/>
    <!-- Keyhole circle -->
    <circle cx="585" cy="208" r="22" fill="#ea580c"/>
    <circle cx="585" cy="205" r="14" fill="#c2410c"/>
    <!-- Keyhole slot -->
    <rect x="580" y="211" width="10" height="20" rx="5" fill="#c2410c"/>
    <!-- Lock shine -->
    <rect x="520" y="162" width="50" height="8" rx="4" fill="rgba(255,255,255,.25)"/>

    <!-- Ground shadow of lock -->
    <ellipse cx="585" cy="300" rx="75" ry="12" fill="#fed7aa" opacity=".5"/>

    <!-- â”€â”€ PERSON (left, being turned away) â”€â”€ -->
    <!-- Shadow -->
    <ellipse cx="240" cy="300" rx="40" ry="8" fill="#fed7aa" opacity=".45"/>
    <!-- Legs -->
    <rect x="222" y="252" width="16" height="40" rx="8" fill="#fb923c"/>
    <rect x="242" y="252" width="16" height="40" rx="8" fill="#fb923c"/>
    <!-- Shoes -->
    <ellipse cx="230" cy="292" rx="14" ry="6" fill="#7c2d12"/>
    <ellipse cx="250" cy="292" rx="14" ry="6" fill="#7c2d12"/>
    <!-- Body -->
    <rect x="214" y="195" width="52" height="62" rx="14" fill="#fb923c"/>
    <!-- Collar / shirt detail -->
    <path d="M228 195 L240 208 L252 195" fill="#ea580c" opacity=".5"/>
    <!-- ID badge -->
    <rect x="225" y="210" width="25" height="32" rx="4" fill="#fff7ed" stroke="#fdba74" stroke-width="1.5"/>
    <circle cx="237" cy="220" r="5.5" fill="#fed7aa"/>
    <rect x="229" y="229" width="16" height="2.5" rx="1.2" fill="#fdba74"/>
    <rect x="231" y="234" width="12" height="2" rx="1" fill="#fcd9aa"/>
    <!-- Head -->
    <circle cx="240" cy="175" r="22" fill="#fdba74"/>
    <!-- Hair -->
    <path d="M218 173 Q218 152 240 152 Q262 152 262 173" fill="#7c2d12"/>
    <!-- Eyes (looking worried toward lock) -->
    <circle cx="233" cy="173" r="3" fill="#431407"/>
    <circle cx="247" cy="173" r="3" fill="#431407"/>
    <!-- Worried raised eyebrows -->
    <path d="M230 166 Q233 163 236 165" stroke="#431407" stroke-width="1.5" fill="none" stroke-linecap="round"/>
    <path d="M244 165 Q247 163 250 166" stroke="#431407" stroke-width="1.5" fill="none" stroke-linecap="round"/>
    <!-- Mouth (open, worried) -->
    <path d="M234 183 Q240 187 246 183" stroke="#431407" stroke-width="2" fill="none" stroke-linecap="round"/>
    <!-- Arms: left arm raised pointing at lock -->
    <path d="M214 215 Q190 200 175 185" stroke="#fb923c" stroke-width="13" stroke-linecap="round" fill="none"/>
    <!-- Right arm down -->
    <path d="M266 215 Q278 230 282 245" stroke="#fb923c" stroke-width="13" stroke-linecap="round" fill="none"/>
    <!-- Hand pointing -->
    <circle cx="172" cy="182" r="9" fill="#fdba74"/>

    <!-- â”€â”€ X / DENIED SIGN floating between person and lock â”€â”€ -->
    <circle cx="390" cy="195" r="32" fill="#fff7ed" stroke="#f97316" stroke-width="3"/>
    <line x1="375" y1="180" x2="405" y2="210" stroke="#f97316" stroke-width="5" stroke-linecap="round"/>
    <line x1="405" y1="180" x2="375" y2="210" stroke="#f97316" stroke-width="5" stroke-linecap="round"/>

    <!-- â”€â”€ Small decorative bushes â”€â”€ -->
    <circle cx="760" cy="295" r="16" fill="#fed7aa" opacity=".8"/>
    <circle cx="778" cy="290" r="12" fill="#fdba74" opacity=".6"/>
    <circle cx="748" cy="292" r="10" fill="#ffedd5" opacity=".9"/>

    <circle cx="60" cy="297" r="10" fill="#fed7aa" opacity=".7"/>
    <circle cx="74" cy="293" r="14" fill="#ffedd5"/>
    <circle cx="50" cy="295" r="8" fill="#fdba74" opacity=".6"/>

    <!-- Sparkle dots -->
    <circle cx="450" cy="130" r="4" fill="#f97316" opacity=".3"/>
    <circle cx="435" cy="155" r="2.5" fill="#fb923c" opacity=".35"/>
    <circle cx="460" cy="165" r="3" fill="#ea580c" opacity=".25"/>
    <circle cx="330" cy="150" r="3" fill="#f97316" opacity=".28"/>
    <circle cx="310" cy="175" r="2" fill="#fb923c" opacity=".3"/>

    <!-- Cloud left -->
    <ellipse cx="130" cy="110" rx="38" ry="16" fill="#fff7ed" opacity=".8"/>
    <ellipse cx="110" cy="116" rx="20" ry="13" fill="#fff7ed" opacity=".7"/>
    <ellipse cx="155" cy="116" rx="22" ry="12" fill="#fff7ed" opacity=".7"/>

    <!-- Cloud right -->
    <ellipse cx="690" cy="95" rx="32" ry="13" fill="#fff7ed" opacity=".75"/>
    <ellipse cx="672" cy="100" rx="18" ry="11" fill="#fff7ed" opacity=".65"/>
    <ellipse cx="712" cy="100" rx="19" ry="11" fill="#fff7ed" opacity=".65"/>

</svg>
</div>

<div class="page-footer">Nonprofit Members Portal &nbsp;&middot;&nbsp; Member Database Management</div>
</body>
</html>
