<!DOCTYPE html>
<html lang="km">
<head>
    @php
    // Translation Logic
    $lang = $locale ?? 'km';
    $t = function($km, $en) use ($lang) {
        return $lang === 'en' ? $en : $km;
    };
    $eventType = $event->type ?? 'wedding';
    $titleTranslations = [
        'wedding'           => ['km' => 'លិខិតអញ្ជើញអាពាហ៍ពិពាហ៍',    'en' => 'Wedding Invitation'],
        'engagement'        => ['km' => 'លិខិតអញ្ជើញពិធីភ្ជាប់ពាក្យ',  'en' => 'Engagement Invitation'],
        'birthday'          => ['km' => 'លិខិតអញ្ជើញខួបកំណើត',          'en' => 'Birthday Invitation'],
        'handtied_ceremony' => ['km' => 'លិខិតអញ្ជើញពិធីចងដៃ',          'en' => 'Hand-Tied Ceremony Invitation'],
        'other'             => ['km' => 'លិខិតអញ្ជើញ',                   'en' => 'Invitation'],
    ];
    $inviteTitle = $titleTranslations[$eventType][$lang] ?? $titleTranslations['other'][$lang];
    $pageTitle   = ($event->name ?? '') . ' — ' . $inviteTitle;
    $coverImage  = $event->cover_image ? asset('storage/' . $event->cover_image) : null;

    // ─── DYNAMIC COLOR ENGINE (100% Derived from API / Database) ───
    $sanitizeHex = function($hex, $fallback) {
        $hex = trim(ltrim($hex ?? '', '#'));
        if (preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return '#' . strtolower($hex);
        }
        if (preg_match('/^[0-9a-fA-F]{3}$/', $hex)) {
            return '#' . strtolower($hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]);
        }
        return $fallback;
    };

    $hexToRgb = function($hex) {
        $hex = ltrim($hex, '#');
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    };

    $mixHex = function($hex1, $hex2, $weight) use ($hexToRgb) {
        [$r1, $g1, $b1] = $hexToRgb($hex1);
        [$r2, $g2, $b2] = $hexToRgb($hex2);
        $w = max(0, min(100, $weight)) / 100;
        $r = round($r1 * (1 - $w) + $r2 * $w);
        $g = round($g1 * (1 - $w) + $g2 * $w);
        $b = round($b1 * (1 - $w) + $b2 * $w);
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    };

    $isLight = function($hex) use ($hexToRgb) {
        [$r, $g, $b] = $hexToRgb($hex);
        return ((0.299 * $r) + (0.587 * $g) + (0.114 * $b)) >= 145;
    };

    // Primary accent color from API
    $themeColor = $sanitizeHex($event->theme_color ?? null, '#C59B27');
    // Page background color from API
    $bgColor    = $sanitizeHex($event->bg_color ?? null, '#FAF6EE');

    [$pR, $pG, $pB] = $hexToRgb($themeColor);
    $primaryRgb = "{$pR}, {$pG}, {$pB}";

    [$bgR, $bgG, $bgB] = $hexToRgb($bgColor);
    $bgRgb = "{$bgR}, {$bgG}, {$bgB}";

    $bgIsLight = $isLight($bgColor);

    // Derived dynamic palette strictly from API colors:
    $primaryLight    = $mixHex($themeColor, '#ffffff', 84); // pastel tint 16%
    $primaryTint     = $mixHex($themeColor, '#ffffff', 94); // soft background tint 6%
    $primaryDark     = $mixHex($themeColor, '#110508', 60); // dark heading shade
    $primaryDeep     = $mixHex($themeColor, '#0a0305', 78); // hero background deep shade
    $primaryBright   = $mixHex($themeColor, '#ffffff', 35); // radiant highlight

    $textColor       = $bgIsLight ? '#261b17' : '#f8fafc';
    $textMuted       = $bgIsLight ? '#6b584a' : '#94a3b8';
    $cardBg          = $bgIsLight ? '#ffffff' : $mixHex($bgColor, '#ffffff', 6);
    $headingColor    = $bgIsLight ? $primaryDark : $primaryBright;

    $translations = [
        'auspicious_blessing' => $t('សិរីសួស្តី ជ័យមង្គល វិបុលសុខ មហាប្រសើរ', 'Auspicious Blessings of Love & Joy'),
        'wedding_invitation'  => $t('សិរីមង្គលអាពាហ៍ពិពាហ៍', 'Wedding Invitation'),
        'respectfully_to'     => $t('សូមគោរពអញ្ជើញ', 'Respectfully Invited to'),
        'guest_honor_title'   => $t('ឯកឧត្តម លោកជំទាវ លោក លោកស្រី អ្នកនាងកញ្ញា', 'Honored Guests & Dignitaries'),
        'invite_msg'          => $t('យើងខ្ញុំមានកិត្តិយសសូមគោរពអញ្ជើញ ឯកឧត្តម លោកឧកញ៉ា លោកជំទាវ លោក លោកស្រី អ្នកនាងកញ្ញា អញ្ជើញចូលរួមជាអធិបតី និងជាភ្ញៀវកិត្តិយស ដើម្បីប្រសិទ្ធិពរជ័យសិរីសួស្តី ជ័យមង្គល ក្នុងពិធីអាពាហ៍ពិពាហ៍ របស់យើងខ្ញុំទាំងពីរ។', 'We cordially invite you to celebrate our wedding day. Your presence will bring us great joy and blessings.'),
        'open_invite'         => $t('បើកការអញ្ជើញ', 'Open Invitation'),
        'tap_to_open'         => $t('ចុចត្រង់នេះដើម្បីបើកសំបុត្រ', 'Tap here to open invitation'),
        'scroll_down'         => $t('អូសចុះក្រោម', 'Scroll Down'),
        'groom_title'         => $t('កូនប្រុសនាម', 'Groom'),
        'bride_title'         => $t('កូនស្រីនាម', 'Bride'),
        'and'                 => $t('និង', '&'),
        'gallery_title'       => $t('កម្រងរូបភាពអនុស្សាវរីយ៍', 'Wedding Gallery'),
        'event_info'          => $t('កម្មវិធីសិរីមង្គលអាពាហ៍ពិពាហ៍', 'Ceremony Schedule'),
        'location_label'      => $t('ទីតាំងប្រារព្ធពិធី', 'Wedding Venue'),
        'open_maps'           => $t('បើកមើលផែនទី Google Maps', 'Open in Google Maps'),
        'gift_qr'             => $t('ចំណងដៃតាម QR Code', 'Wedding Gift QR Code'),
        'rsvp'                => $t('បញ្ជាក់ការចូលរួម', 'Confirm Attendance (RSVP)'),
        'rsvp_intro'          => $t('វត្តមានដ៏ខ្ពង់ខ្ពស់របស់លោកអ្នក ជាសក្ខីភាព និងជាសុភមង្គលដ៏ឧត្តុង្គឧត្តមសម្រាប់យើងខ្ញុំ។', 'Your presence is our greatest honor and happiness.'),
        'attending'           => $t('ចូលរួម', 'Attending'),
        'not_attending'       => $t('មិនអាចចូលរួម', 'Decline'),
        'send_wishes'         => $t('ផ្ញើពាក្យប្រសិទ្ធិពរជ័យ', 'Send Your Blessings'),
        'wishes_placeholder'  => $t('សូមសរសេរពាក្យជូនពរ និងប្រសិទ្ធិពរជ័យជូនដល់គូស្វាមីភរិយាថ្មី…', 'Write your blessings for the couple...'),
        'wishes_btn'          => $t('ផ្ញើពាក្យជូនពរ', 'Submit Blessing'),
        'wishes_success'      => $t('សូមអរគុណសម្រាប់ពាក្យប្រសិទ្ធិពរជ័យដ៏វិសេសវិសាលរបស់អ្នក!', 'Thank you for your warm wishes!'),
        'guest_comments'      => $t('ពាក្យជូនពរពីភ្ញៀវកិត្តិយស', 'Blessings from Honored Guests'),
        'days'                => $t('ថ្ងៃ', 'Days'),
        'hours'               => $t('ម៉ោង', 'Hours'),
        'minutes'             => $t('នាទី', 'Mins'),
        'seconds'             => $t('វិនាទី', 'Secs'),
        'guest'               => $t('ភ្ញៀវកិត្តិយស', 'Honored Guest'),
        'greetings'           => $t('ប្រសិទ្ធិពរជ័យ', 'Greetings & Blessings')
    ];
    @endphp

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>

    {{-- SEO & Open Graph Meta --}}
    <meta name="description" content="{{ $translations['invite_msg'] }}">
    <meta name="robots" content="noindex, nofollow">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $translations['invite_msg'] }}">
    <meta property="og:site_name" content="{{ $inviteTitle }}">
    @if($coverImage)
    <meta property="og:image" content="{{ $coverImage }}">
    <meta property="og:image:alt" content="{{ $pageTitle }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    @endif

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Fancybox Lightbox --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.css"/>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.umd.js"></script>

    {{-- Swiper.js Slider --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Moul&family=Moulpali&family=Kantumruy+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Cinzel:wght@500;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: {{ $themeColor }};
            --primary-rgb: {{ $primaryRgb }};
            --primary-light: {{ $primaryLight }};
            --primary-tint: {{ $primaryTint }};
            --primary-dark: {{ $primaryDark }};
            --primary-deep: {{ $primaryDeep }};
            --primary-bright: {{ $primaryBright }};
            --primary-heading: {{ $headingColor }};
            --bg-color: {{ $bgColor }};
            --bg-rgb: {{ $bgRgb }};
            --card-bg: {{ $cardBg }};
            --text-color: {{ $textColor }};
            --text-muted: {{ $textMuted }};
            --border-primary: rgba({{ $primaryRgb }}, 0.35);
            --border-light: rgba({{ $primaryRgb }}, 0.18);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Kantumruy Pro', -apple-system, sans-serif;
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(circle at 50% 0%, rgba({{ $primaryRgb }}, 0.08) 0%, transparent 55%),
                radial-gradient(circle at 85% 50%, rgba({{ $primaryRgb }}, 0.04) 0%, transparent 40%),
                radial-gradient(circle at 15% 85%, rgba({{ $primaryRgb }}, 0.05) 0%, transparent 45%);
            background-attachment: fixed;
            color: var(--text-color);
            overflow-x: hidden;
            line-height: 1.7;
        }

        .f-moul { 
            font-family: 'Moul', 'Khmer OS Muol', {{ $lang == 'km' ? 'cursive, serif' : "'Cinzel', 'Playfair Display', Georgia, serif" }}; 
            font-weight: normal; 
        }
        .f-cinzel { font-family: 'Cinzel', serif; letter-spacing: 0.12em; }
        .f-heading {
            font-family: {{ $lang == 'km' ? "'Moul', 'Khmer OS Muol', cursive, serif" : "'Cinzel', 'Playfair Display', Georgia, serif" }};
            letter-spacing: {{ $lang == 'km' ? 'normal' : '0.04em' }};
        }

        /* Dynamic Classes Driven by API Colors */
        .theme-heading { color: var(--primary-heading) !important; }
        .theme-primary-text { color: var(--primary) !important; }
        .theme-dark-text { color: var(--primary-dark) !important; }
        .theme-bright-text { color: var(--primary-bright) !important; }
        .theme-muted-text { color: var(--text-muted) !important; }
        .theme-light-bg { background-color: var(--primary-light) !important; }
        .theme-tint-bg { background-color: var(--primary-tint) !important; }
        .theme-border { border-color: var(--border-primary) !important; }
        .theme-border-light { border-color: var(--border-light) !important; }

        .gold-gradient-text, .theme-gradient-text {
            background: linear-gradient(135deg, var(--primary-bright) 0%, var(--primary) 45%, var(--primary-dark) 80%, var(--primary-bright) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gold-leaf-text {
            background: linear-gradient(120deg, var(--primary) 0%, var(--primary-bright) 35%, var(--primary-dark) 70%, var(--primary-bright) 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: goldShine 6s linear infinite;
        }
        @keyframes goldShine {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        .khmer-card {
            background: var(--card-bg);
            border: 1.5px solid var(--border-primary);
            border-radius: 20px;
            box-shadow: 0 12px 36px -8px rgba({{ $primaryRgb }}, 0.12);
        }

        /* Envelope Opener */
        #opener {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(14px, 3vh, 32px) 14px clamp(80px, 10vh, 105px);
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(circle at 50% 25%, rgba({{ $primaryRgb }}, 0.12) 0%, transparent 65%),
                radial-gradient(circle at 50% 85%, rgba({{ $primaryRgb }}, 0.08) 0%, transparent 55%);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            transition: opacity 0.55s cubic-bezier(0.16, 1, 0.3, 1), transform 0.55s cubic-bezier(0.16, 1, 0.3, 1), filter 0.55s ease;
        }
        #opener.closing {
            opacity: 0;
            transform: scale(1.04) translateY(-14px);
            filter: blur(6px);
            pointer-events: none;
        }

        /* Atmospheric Photo Backdrop for Opener (Mobile & Desktop) */
        .opener-bg-photo {
            position: fixed;
            inset: -10px;
            background-size: cover;
            background-position: center 20%;
            background-repeat: no-repeat;
            filter: blur(1.5px) brightness(0.92) saturate(1.08);
            transform: scale(1.03);
            z-index: 0;
            pointer-events: none;
        }
        .opener-bg-tint {
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(circle at 50% 30%, rgba(0, 0, 0, 0.06) 0%, rgba(0, 0, 0, 0.38) 100%),
                linear-gradient(180deg, rgba({{ $bgRgb }}, 0.12) 0%, rgba({{ $bgRgb }}, 0.28) 100%);
            z-index: 1;
            pointer-events: none;
        }

        @if($coverImage)
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url('{{ $coverImage }}');
            background-size: cover;
            background-position: center center;
            background-attachment: fixed;
            opacity: {{ $bgIsLight ? '0.045' : '0.075' }};
            pointer-events: none;
            z-index: -1;
        }
        @endif

        /* Authentic Khmer Royal Wedding Card */
        .royal-envelope-card {
            position: relative;
            z-index: 10;
            max-width: 430px;
            width: 100%;
            margin: auto;
            background: {{ $bgIsLight ? 'rgba(255, 255, 255, 0.78)' : 'rgba(20, 26, 35, 0.80)' }};
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1.5px solid rgba(255, 255, 255, 0.85);
            border-radius: 28px;
            padding: clamp(16px, 2.5vh, 28px) clamp(14px, 3.8vw, 24px) clamp(16px, 2.3vh, 24px);
            text-align: center;
            box-shadow: 
                0 25px 60px -10px rgba(0, 0, 0, 0.32),
                0 0 0 1px rgba(255, 255, 255, 0.85) inset,
                0 0 45px rgba({{ $primaryRgb }}, 0.25);
            animation: cardFloatIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }
        @keyframes cardFloatIn {
            0% { opacity: 0; transform: translateY(24px) scale(0.96); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        .royal-card-inner-frame {
            position: absolute;
            inset: 10px;
            border: 1px solid var(--border-primary);
            border-radius: 20px;
            pointer-events: none;
        }
        .royal-card-inner-frame::after {
            content: '';
            position: absolute;
            inset: 4px;
            border: 1px dashed var(--border-light);
            border-radius: 16px;
        }

        /* Corner Kbach Flourishes */
        .kbach-corner {
            position: absolute;
            width: 28px;
            height: 28px;
            color: var(--primary);
            pointer-events: none;
            z-index: 2;
            opacity: 0.85;
        }
        .kbach-corner.top-left { top: 12px; left: 12px; }
        .kbach-corner.top-right { top: 12px; right: 12px; transform: scaleX(-1); }
        .kbach-corner.bottom-left { bottom: 12px; left: 12px; transform: scaleY(-1); }
        .kbach-corner.bottom-right { bottom: 12px; right: 12px; transform: scale(-1); }

        /* Wedding Couple Announcement Plaque */
        .wedding-couple-plaque {
            position: relative;
            background: linear-gradient(135deg, rgba({{ $primaryRgb }}, 0.05) 0%, rgba({{ $primaryRgb }}, 0.11) 100%);
            border: 1.5px solid var(--border-primary);
            border-radius: 18px;
            padding: 12px 10px;
            margin: 12px 0;
            box-shadow: 0 4px 16px rgba({{ $primaryRgb }}, 0.08);
        }
        .wedding-couple-plaque::before {
            content: '';
            position: absolute;
            inset: 3px;
            border: 1px dashed var(--border-light);
            border-radius: 14px;
            pointer-events: none;
        }

        /* Prominent Wedding Opener Button (✦ បើកការអញ្ជើញ ✦) */
        .royal-open-invite-btn {
            position: relative;
            width: 100%;
            max-width: 295px;
            margin: 6px auto 0;
            padding: 12px 24px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--primary-bright) 0%, var(--primary) 50%, var(--primary-dark) 100%);
            color: #ffffff;
            cursor: pointer;
            box-shadow: 
                0 10px 26px -4px rgba({{ $primaryRgb }}, 0.55),
                0 0 0 3px var(--card-bg),
                0 0 0 4.5px var(--border-primary);
            overflow: hidden;
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
            animation: inviteBtnBreath 2.8s infinite ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }

        .royal-open-invite-btn:hover {
            transform: translateY(-2px) scale(1.025);
            box-shadow: 
                0 14px 32px -4px rgba({{ $primaryRgb }}, 0.70),
                0 0 0 3px var(--card-bg),
                0 0 0 5.5px var(--primary-bright);
        }

        .royal-open-invite-btn:active {
            transform: scale(0.97);
        }

        .btn-shimmer-sweep {
            position: absolute;
            top: 0;
            left: -120%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.45), transparent);
            transform: skewX(-22deg);
            animation: shimmerSweep 3.2s infinite ease-in-out;
            pointer-events: none;
        }

        @keyframes shimmerSweep {
            0%, 35% { left: -120%; }
            70%, 100% { left: 180%; }
        }

        @keyframes inviteBtnBreath {
            0%, 100% { 
                transform: scale(1);
                box-shadow: 0 10px 26px -4px rgba({{ $primaryRgb }}, 0.55), 0 0 0 3px var(--card-bg), 0 0 0 4.5px var(--border-primary);
            }
            50% { 
                transform: scale(1.03);
                box-shadow: 0 14px 34px -2px rgba({{ $primaryRgb }}, 0.78), 0 0 0 3px var(--card-bg), 0 0 0 6px rgba({{ $primaryRgb }}, 0.85);
            }
        }

        .btn-main-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 16px;
            line-height: 1.3;
            font-family: 'Moul', cursive;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.35);
        }

        .btn-sub-row {
            font-family: 'Cinzel', serif;
            font-size: 9px;
            letter-spacing: 0.18em;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.95);
            margin-top: 2px;
            text-transform: uppercase;
        }

        .tap-hint-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--primary-heading);
            margin-top: 8px;
            padding: 4px 12px;
            background: var(--primary-tint);
            border-radius: 99px;
            border: 1px solid var(--border-primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .bounce-hand {
            display: inline-block;
            animation: handBounce 1.5s infinite ease-in-out;
        }

        @keyframes handBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        .hero-banner {
            position: relative;
            min-height: 95vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: clamp(35px, 6vh, 60px) 16px 95px;
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(circle at 50% 25%, rgba({{ $primaryRgb }}, 0.16) 0%, transparent 60%),
                radial-gradient(circle at 15% 75%, rgba({{ $primaryRgb }}, 0.10) 0%, transparent 50%),
                radial-gradient(circle at 85% 75%, rgba({{ $primaryRgb }}, 0.12) 0%, transparent 50%);
            background-size: cover;
            background-position: center 20%;
            background-repeat: no-repeat;
            overflow: hidden;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, 
                rgba(255, 255, 255, 0.20) 0%, 
                rgba({{ $bgRgb }}, 0.25) 35%, 
                rgba({{ $bgRgb }}, 0.70) 75%, 
                var(--bg-color) 100%
            );
            z-index: 1;
            pointer-events: none;
        }

        /* Fresh Luminous Frosted Pearl Glass Plaque for Hero with Ambient Floating Breath */
        .hero-invitation-glass {
            position: relative;
            z-index: 10;
            max-width: 485px;
            width: 100%;
            margin: auto;
            background: {{ $bgIsLight ? 'rgba(255, 255, 255, 0.90)' : 'rgba(20, 26, 35, 0.90)' }};
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.95);
            border-radius: 34px;
            padding: clamp(24px, 3.8vh, 36px) clamp(16px, 4vw, 28px);
            box-shadow: 
                0 25px 65px -10px rgba(0, 0, 0, 0.18),
                0 0 0 1px rgba(255, 255, 255, 0.95) inset,
                0 0 40px rgba({{ $primaryRgb }}, 0.22);
            animation: heroPlaqueFloat 6s ease-in-out infinite;
        }
        .hero-invitation-glass::before {
            content: '';
            position: absolute;
            inset: 8px;
            border: 1px dashed rgba({{ $primaryRgb }}, 0.40);
            border-radius: 26px;
            pointer-events: none;
        }

        @keyframes heroPlaqueFloat {
            0%, 100% {
                transform: translateY(0);
                box-shadow: 0 25px 65px -10px rgba(0, 0, 0, 0.18), 0 0 0 1px rgba(255, 255, 255, 0.95) inset, 0 0 40px rgba({{ $primaryRgb }}, 0.22);
            }
            50% {
                transform: translateY(-6px);
                box-shadow: 0 34px 75px -12px rgba(0, 0, 0, 0.22), 0 0 0 1.5px rgba(255, 255, 255, 1) inset, 0 0 55px rgba({{ $primaryRgb }}, 0.32);
            }
        }

        /* Ambient Sparkling Stars in Hero Plaque */
        .hero-sparkle {
            position: absolute;
            color: var(--primary);
            font-size: 13px;
            pointer-events: none;
            user-select: none;
            z-index: 2;
        }
        .hero-sparkle-1 { top: 16px; left: 18px; animation: sparkleTwinkle 3.2s infinite ease-in-out 0.2s; }
        .hero-sparkle-2 { top: 18px; right: 18px; animation: sparkleTwinkle 3.8s infinite ease-in-out 1.2s; }
        .hero-sparkle-3 { bottom: 22px; left: 20px; animation: sparkleTwinkle 4.2s infinite ease-in-out 0.7s; }
        .hero-sparkle-4 { bottom: 24px; right: 20px; animation: sparkleTwinkle 3.5s infinite ease-in-out 1.8s; }

        @keyframes sparkleTwinkle {
            0%, 100% { opacity: 0.22; transform: scale(0.8) rotate(0deg); }
            50% { opacity: 0.95; transform: scale(1.35) rotate(45deg); filter: drop-shadow(0 0 6px var(--primary-bright)); }
        }

        /* Solitaire Diamond Sparkle Twinkle */
        .hero-diamond-twinkle {
            transform-origin: 78px 4px;
            animation: diamondGlint 2.8s infinite ease-in-out;
        }
        @keyframes diamondGlint {
            0%, 100% { transform: scale(0.85) rotate(0deg); opacity: 0.6; }
            50% { transform: scale(1.4) rotate(45deg); opacity: 1; filter: drop-shadow(0 0 6px #ffffff) drop-shadow(0 0 12px var(--primary-bright)); }
        }

        /* Auspicious Blessing - Royal & High Contrast */
        .hero-blessing-text {
            font-family: {{ $lang == 'km' ? "'Moul', 'Khmer OS Muol', cursive, serif" : "'Cinzel', 'Playfair Display', Georgia, serif" }};
            font-weight: 700;
            color: var(--primary-dark);
            letter-spacing: {{ $lang == 'km' ? '0.03em' : '0.12em' }};
            text-shadow: 0 1px 3px rgba(var(--primary-rgb), 0.20);
        }

        /* Main Wedding Title with Continuous Royal Shimmer */
        .hero-wedding-title {
            font-family: {{ $lang == 'km' ? "'Moul', 'Khmer OS Muol', cursive, serif" : "'Cinzel', 'Playfair Display', Georgia, serif" }};
            font-weight: 700;
            color: var(--primary-dark);
            letter-spacing: {{ $lang == 'km' ? '0.02em' : '0.06em' }};
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
            background: linear-gradient(
                120deg, 
                var(--primary-dark) 0%, 
                var(--primary-dark) 35%, 
                var(--primary-bright) 50%, 
                var(--primary-dark) 65%, 
                var(--primary-dark) 100%
            );
            background-size: 250% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: royalTitleShimmer 5s ease-in-out infinite;
        }
        @keyframes royalTitleShimmer {
            0%, 25% { background-position: 100% 0; }
            75%, 100% { background-position: -100% 0; }
        }

        /* Couple Names - Royal Dignity & Luxury Metallic Gleam */
        .hero-couple-name {
            font-family: {{ $lang == 'km' ? "'Moul', 'Khmer OS Muol', cursive, serif" : "'Cinzel', 'Playfair Display', Georgia, serif" }};
            font-weight: 700;
            color: var(--primary-dark);
            position: relative;
            display: inline-block;
            letter-spacing: {{ $lang == 'km' ? '0.02em' : '0.06em' }};
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.15));
            background: linear-gradient(
                120deg, 
                var(--primary-dark) 0%, 
                var(--primary-dark) 35%, 
                var(--primary-bright) 50%, 
                var(--primary-dark) 65%, 
                var(--primary-dark) 100%
            );
            background-size: 250% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: royalCoupleGleam 4.5s ease-in-out infinite;
        }
        @keyframes royalCoupleGleam {
            0%, 20% { background-position: 100% 0; }
            70%, 100% { background-position: -100% 0; }
        }

        /* Elegant Khmer Wedding Invitation Message Card */
        .invite-msg-badge {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: {{ $bgIsLight ? 'linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(var(--primary-rgb), 0.12))' : 'rgba(255, 255, 255, 0.08)' }};
            border: 1.5px solid var(--border-primary);
            box-shadow: 
                0 8px 24px -4px rgba(var(--primary-rgb), 0.25),
                0 0 0 2px rgba(255, 255, 255, 0.8) inset;
            transition: transform 0.3s ease;
        }
        .invite-msg-badge:hover {
            transform: scale(1.05);
        }

        .invite-msg-body {
            font-family: {{ $lang == 'km' ? "'Kantumruy Pro', -apple-system, sans-serif" : "'Playfair Display', Georgia, serif" }};
            font-size: clamp(14.5px, 2.2vw, 16px);
            font-weight: {{ $lang == 'km' ? '500' : '400' }};
            line-height: {{ $lang == 'km' ? '2.0' : '2.0' }};
            letter-spacing: {{ $lang == 'km' ? '0.015em' : '0.03em' }};
            color: var(--primary-heading);
            max-width: 580px;
            margin: 0 auto;
            text-wrap: pretty;
        }
        .iframe-container{
            iframe{
                width: 100%;
                height: 100%;
            }
        }
      
        /* Romantic Ampersand Heartbeat Emblem */
        .hero-ampersand-badge {
            position: relative;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-bright) 0%, var(--primary) 50%, var(--primary-dark) 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 4px 14px rgba(var(--primary-rgb), 0.40),
                0 0 0 2.5px rgba(255, 255, 255, 0.95),
                0 0 0 4px var(--border-primary);
            animation: royalHeartbeat 2.6s infinite ease-in-out;
        }
        @keyframes royalHeartbeat {
            0%, 100% { transform: scale(1); }
            12% { transform: scale(1.15); box-shadow: 0 6px 18px rgba(var(--primary-rgb), 0.55), 0 0 0 2.5px rgba(255, 255, 255, 1), 0 0 0 5px var(--primary-bright); }
            24% { transform: scale(1.02); }
            36% { transform: scale(1.12); box-shadow: 0 5px 16px rgba(var(--primary-rgb), 0.50), 0 0 0 2.5px rgba(255, 255, 255, 1), 0 0 0 4.5px var(--primary-bright); }
            60% { transform: scale(1); }
        }

        /* Elegant Date Badge */
        .hero-date-badge {
            background: {{ $bgIsLight ? 'rgba(255, 255, 255, 0.95)' : 'rgba(15, 23, 42, 0.90)' }};
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1.5px solid var(--border-primary);
            box-shadow: 0 4px 16px -2px rgba(var(--primary-rgb), 0.20), 0 0 0 1px rgba(255, 255, 255, 0.8) inset;
        }

        /* Countdown Boxes with Golden Bar Accent & Rhythmic Seconds Tick */
        .countdown-box-royal {
            position: relative;
            background: {{ $bgIsLight ? 'rgba(255, 255, 255, 0.95)' : 'rgba(15, 23, 42, 0.90)' }};
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1.5px solid var(--border-primary);
            border-radius: 20px;
            padding: 11px 12px;
            min-width: 72px;
            box-shadow: 
                0 8px 20px -4px rgba(0, 0, 0, 0.10), 
                0 0 20px rgba({{ $primaryRgb }}, 0.16),
                0 0 0 1px rgba(255, 255, 255, 0.85) inset;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            overflow: hidden;
        }
        .countdown-box-royal::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-bright), var(--primary), var(--primary-dark));
            border-radius: 20px 20px 0 0;
        }
        .countdown-box-seconds #cd-seconds {
            color: var(--primary) !important;
            animation: secondPulse 1s infinite ease-in-out;
        }
        @keyframes secondPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); filter: drop-shadow(0 0 4px rgba(var(--primary-rgb), 0.4)); }
        }

        #leaves-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 10001;
        }

        #wedding-page {
            padding-bottom: 85px;
        }

        .khmer-timeline-item {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 14px 0;
        }
        .khmer-timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 20px;
            top: 50px;
            bottom: -6px;
            width: 2px;
            background: linear-gradient(180deg, var(--primary), var(--border-light));
        }
        .khmer-timeline-icon {
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-bright), var(--primary), var(--primary-dark));
            border: 2px solid var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            z-index: 1;
            box-shadow: 0 4px 12px rgba({{ $primaryRgb }}, 0.3);
        }

        .btn-gold-royal {
            background: linear-gradient(135deg, var(--primary-bright) 0%, var(--primary) 50%, var(--primary-dark) 100%);
            color: #ffffff;
            font-weight: 700;
            border: 1px solid var(--border-primary);
            box-shadow: 0 6px 20px rgba({{ $primaryRgb }}, 0.35);
            transition: all 0.3s ease;
        }
        .btn-gold-royal:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 26px rgba({{ $primaryRgb }}, 0.45);
        }

        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        .reveal.in { opacity: 1; transform: translateY(0); }

        .floating-action-btn {
            position: fixed;
            z-index: 9990;
            border-radius: 99px;
            background: linear-gradient(135deg, var(--primary-bright), var(--primary));
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.28);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        @media (max-width: 640px) {
            #opener {
                padding: clamp(10px, 2vh, 20px) 14px clamp(55px, 8vh, 75px);
            }
            .opener-bg-photo {
                background-position: center 18%;
                filter: blur(1px) brightness(0.94) saturate(1.08);
            }
            .royal-envelope-card {
                max-width: 380px;
                width: calc(100vw - 28px);
                padding: clamp(14px, 2.2vh, 22px) clamp(12px, 3.5vw, 18px);
            }
        }
    </style>
</head>
<body>

<canvas id="leaves-canvas" aria-hidden="true"></canvas>

{{-- ═══════════════════════════════════════════════
     THEME 1: ROYAL ENVELOPE OPENER
══════════════════════════════════════════════ --}}
<div id="opener" role="dialog" aria-modal="true" aria-label="Royal Invitation Opener">
    @if($coverImage)
    <div class="opener-bg-photo" style="background-image: url('{{ $coverImage }}');"></div>
    <div class="opener-bg-tint"></div>
    @endif

    <div class="royal-envelope-card">
        <!-- 4 Authentic Khmer Kbach Ornamental Corners -->
        <svg class="kbach-corner top-left" viewBox="0 0 40 40" fill="none" stroke="currentColor">
            <path d="M4 36 V12 C4 7.5, 7.5 4, 12 4 H36" stroke-width="1.6"/>
            <path d="M8 32 V14 C8 10.7, 10.7 8, 14 8 H32" stroke-width="0.8" stroke-dasharray="2 2"/>
            <circle cx="12" cy="12" r="2.5" fill="currentColor"/>
            <path d="M4 12 Q12 12, 12 4" stroke-width="1.2"/>
        </svg>
        <svg class="kbach-corner top-right" viewBox="0 0 40 40" fill="none" stroke="currentColor">
            <path d="M4 36 V12 C4 7.5, 7.5 4, 12 4 H36" stroke-width="1.6"/>
            <path d="M8 32 V14 C8 10.7, 10.7 8, 14 8 H32" stroke-width="0.8" stroke-dasharray="2 2"/>
            <circle cx="12" cy="12" r="2.5" fill="currentColor"/>
            <path d="M4 12 Q12 12, 12 4" stroke-width="1.2"/>
        </svg>
        <svg class="kbach-corner bottom-left" viewBox="0 0 40 40" fill="none" stroke="currentColor">
            <path d="M4 36 V12 C4 7.5, 7.5 4, 12 4 H36" stroke-width="1.6"/>
            <path d="M8 32 V14 C8 10.7, 10.7 8, 14 8 H32" stroke-width="0.8" stroke-dasharray="2 2"/>
            <circle cx="12" cy="12" r="2.5" fill="currentColor"/>
            <path d="M4 12 Q12 12, 12 4" stroke-width="1.2"/>
        </svg>
        <svg class="kbach-corner bottom-right" viewBox="0 0 40 40" fill="none" stroke="currentColor">
            <path d="M4 36 V12 C4 7.5, 7.5 4, 12 4 H36" stroke-width="1.6"/>
            <path d="M8 32 V14 C8 10.7, 10.7 8, 14 8 H32" stroke-width="0.8" stroke-dasharray="2 2"/>
            <circle cx="12" cy="12" r="2.5" fill="currentColor"/>
            <path d="M4 12 Q12 12, 12 4" stroke-width="1.2"/>
        </svg>

        <div class="royal-card-inner-frame"></div>

        <!-- Authentic Khmer Wedding Emblem: Interlocking Rings, Diamond Solitaire & Kbach Flourish -->
        <svg class="w-28 h-14 mx-auto mb-1" viewBox="0 0 130 64" fill="none">
            <defs>
                <linearGradient id="crestGold1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="var(--primary-bright)"/>
                    <stop offset="50%" stop-color="var(--primary)"/>
                    <stop offset="100%" stop-color="var(--primary-dark)"/>
                </linearGradient>
                <linearGradient id="diamondGlow1" x1="50%" y1="0%" x2="50%" y2="100%">
                    <stop offset="0%" stop-color="#ffffff"/>
                    <stop offset="60%" stop-color="var(--primary-bright)"/>
                    <stop offset="100%" stop-color="var(--primary)"/>
                </linearGradient>
                <filter id="sparkleGlow1" x="-20%" y="-20%" width="140%" height="140%">
                    <feGaussianBlur stdDeviation="1.2" result="blur" />
                    <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                </filter>
            </defs>

            <!-- Khmer Ornamental Wings / Lotus Laurel (Bottom & Flanks) -->
            <path d="M65 52 C50 56, 32 54, 18 44 C26 42, 36 43, 45 47" stroke="url(#crestGold1)" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M65 52 C80 56, 98 54, 112 44 C104 42, 94 43, 85 47" stroke="url(#crestGold1)" stroke-width="1.8" stroke-linecap="round"/>
            
            <!-- Outer Wing Filigree -->
            <path d="M22 42 C14 36, 12 28, 8 20 C14 26, 24 30, 32 34" stroke="url(#crestGold1)" stroke-width="1.3" stroke-linecap="round"/>
            <path d="M108 42 C116 36, 118 28, 122 20 C116 26, 106 30, 98 34" stroke="url(#crestGold1)" stroke-width="1.3" stroke-linecap="round"/>
            
            <!-- Wing Pearls -->
            <circle cx="8" cy="20" r="2" fill="var(--primary-bright)"/>
            <circle cx="122" cy="20" r="2" fill="var(--primary-bright)"/>
            <circle cx="65" cy="56" r="2.2" fill="var(--primary-bright)"/>

            <!-- Left Wedding Ring (Groom's Ring) -->
            <ellipse cx="52" cy="34" rx="15" ry="17" stroke="url(#crestGold1)" stroke-width="3.5" fill="none" />
            <ellipse cx="52" cy="34" rx="15" ry="17" stroke="rgba(255,255,255,0.7)" stroke-width="0.8" stroke-dasharray="8 30" fill="none" />

            <!-- Right Wedding Ring (Bride's Ring with Diamond Crown) -->
            <ellipse cx="78" cy="34" rx="15" ry="17" stroke="url(#crestGold1)" stroke-width="3.5" fill="none" />
            <ellipse cx="78" cy="34" rx="15" ry="17" stroke="rgba(255,255,255,0.7)" stroke-width="0.8" stroke-dasharray="8 30" fill="none" />

            <!-- Ring Interlocking Front Arc -->
            <path d="M63 26 C67 31, 67 38, 63 43" stroke="url(#crestGold1)" stroke-width="3.6" fill="none" stroke-linecap="round"/>

            <!-- Solitaire Diamond Crown on Bride's Ring -->
            <path d="M72 17 L78 20 L84 17" stroke="url(#crestGold1)" stroke-width="1.6" fill="none"/>
            <path d="M78 18 L78 21" stroke="url(#crestGold1)" stroke-width="1.6"/>
            
            <!-- Brilliant Cut Solitaire Diamond Gem -->
            <polygon points="78,4 85,11 83,17 73,17 71,11" fill="url(#diamondGlow1)" stroke="url(#crestGold1)" stroke-width="1.2"/>
            <line x1="71" y1="11" x2="85" y2="11" stroke="rgba(255,255,255,0.9)" stroke-width="0.8"/>
            <line x1="78" y1="4" x2="75" y2="11" stroke="rgba(255,255,255,0.9)" stroke-width="0.7"/>
            <line x1="78" y1="4" x2="81" y2="11" stroke="rgba(255,255,255,0.9)" stroke-width="0.7"/>
            <line x1="75" y1="11" x2="78" y2="17" stroke="rgba(255,255,255,0.7)" stroke-width="0.7"/>
            <line x1="81" y1="11" x2="78" y2="17" stroke="rgba(255,255,255,0.7)" stroke-width="0.7"/>

            <!-- Radiant 4-Point Sparkle on Diamond Tip -->
            <path d="M78 0 L79.5 3.5 L83 4 L79.5 4.5 L78 8 L76.5 4.5 L73 4 L76.5 3.5 Z" fill="#ffffff" filter="url(#sparkleGlow1)"/>
            <circle cx="78" cy="4" r="1.2" fill="#ffffff"/>

            <!-- Auspicious Crown Apex Motif over Center -->
            <path d="M62 13 C64 9, 66 9, 68 13" stroke="url(#crestGold1)" stroke-width="1.2" stroke-linecap="round"/>
            <circle cx="65" cy="8" r="1.5" fill="var(--primary-bright)"/>
        </svg>

        <!-- Auspicious Blessing -->
        <p class="f-moul text-[11px] md:text-xs theme-heading mb-1 tracking-wide">{{ $translations['auspicious_blessing'] }}</p>

        <!-- Main Title -->
        <h1 class="f-moul text-base md:text-lg theme-heading mb-2 leading-relaxed">
            {{ $translations['wedding_invitation'] }}
        </h1>

        <!-- Ornate Divider -->
        <div class="flex items-center justify-center gap-2 my-2">
            <span class="h-[1px] w-12 bg-gradient-to-r from-transparent to-[var(--primary)]"></span>
            <span class="text-xs theme-primary-text">⚜️</span>
            <span class="h-[1px] w-12 bg-gradient-to-l from-transparent to-[var(--primary)]"></span>
        </div>

        <!-- Honor Salutation -->
        <p class="text-xs theme-muted-text mb-1 font-medium">{{ $translations['respectfully_to'] }}</p>
        
        <!-- Guest Nameplate -->
        <div class="inline-block px-4 py-1.5 rounded-full bg-[var(--primary)]/10 border border-[var(--border-primary)] mb-3 shadow-sm">
            <h2 id="opener-guest-name" class="f-moul text-sm md:text-base theme-heading leading-snug">
                {{ $guest->name ?? $translations['guest_honor_title'] }}
            </h2>
        </div>

        <!-- Invitation Message -->
        <p class="text-xs leading-relaxed px-2 mb-3 theme-muted-text">
            {!! nl2br(e($translations['invite_msg'])) !!}
        </p>

        <!-- Wedding Couple Announcement Plaque -->
        <div class="wedding-couple-plaque">
            <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-2">
                <div class="text-center">
                    <span class="text-[10px] theme-muted-text block uppercase tracking-wider">{{ $translations['groom_title'] }}</span>
                    <div class="f-moul text-sm md:text-base theme-heading mt-0.5">
                        {{ $lang == 'km' ? $event->groom_name : $event->groom_name_en }}
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center px-1">
                    <span class="w-6 h-6 rounded-full border border-[var(--border-primary)] bg-[var(--card-bg)] flex items-center justify-center text-[10px] f-moul theme-primary-text shadow-sm">&amp;</span>
                </div>

                <div class="text-center">
                    <span class="text-[10px] theme-muted-text block uppercase tracking-wider">{{ $translations['bride_title'] }}</span>
                    <div class="f-moul text-sm md:text-base theme-heading mt-0.5">
                        {{ $lang == 'km' ? $event->bride_name : $event->bride_name_en }}
                    </div>
                </div>
            </div>
        </div>

        @if($event->date ?? false)
        <p class="text-xs theme-muted-text mb-4 font-medium">
            🗓 {{ \Carbon\Carbon::parse($event->date)->translatedFormat('l, d F Y') }}
        </p>
        @endif

        <!-- Prominent Wedding Opener Trigger (✦ បើកការអញ្ជើញ ✦) -->
        <div class="text-center pt-1" onclick="openWeddingPage(event)">
            <button type="button" class="royal-open-invite-btn" onclick="openWeddingPage(event)" aria-label="{{ $translations['open_invite'] }}">
                <div class="btn-shimmer-sweep"></div>
                <div class="btn-main-row">
                    <span class="text-xs">✦</span>
                    <span>{{ $translations['open_invite'] }}</span>
                    <span class="text-xs">✦</span>
                </div>
                <div class="btn-sub-row">
                    OPEN INVITATION
                </div>
            </button>
            <div class="tap-hint-pill">
                <span class="bounce-hand">👆</span>
                <span>{{ $translations['tap_to_open'] }}</span>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════
     MAIN PAGE CONTENT
═══════════════════════════════════════════════ --}}
<div id="wedding-page" style="display: none;">

    @if($music ?? ($event->music->value ?? false))
    <audio id="bg-music" loop preload="auto">
        <source src="{{ asset('storage/' . ($music ?? ($event->music->value ?? ''))) }}" type="audio/mpeg">
    </audio>
    @endif

    <button id="music-btn" onclick="toggleMusic()" class="floating-action-btn" style="bottom: 24px; right: 18px; width: 44px; height: 44px;">
        <span class="text-lg">🎵</span>
    </button>

    @if($guest)
    <button id="gift-btn" onclick="openGiftModal()" class="floating-action-btn px-4 py-2 text-xs font-bold gap-1.5" style="bottom: 78px; right: 18px; height: 38px;">
        <span id="gift-btn-icon">{{ $guest->donation ? '✓' : '🎁' }}</span>
        <span id="gift-btn-text" class="f-moul text-[11px] font-normal">{{ $guest->donation ? $t('បានជូនចំណងដៃ', 'Gifted') : $t('ចំណងដៃ', 'Gift') }}</span>
    </button>
    @endif

    {{-- HERO --}}
    <section class="hero-banner" @if($event->cover_image) style="background-image: url('{{ asset('storage/' . $event->cover_image) }}');" @endif>
        <div class="hero-overlay"></div>

        <div class="hero-invitation-glass">
            <!-- Ambient Sparkling Stars -->
            <div class="hero-sparkle hero-sparkle-1">✦</div>
            <div class="hero-sparkle hero-sparkle-2">✦</div>
            <div class="hero-sparkle hero-sparkle-3">✦</div>
            <div class="hero-sparkle hero-sparkle-4">✦</div>

            <!-- Authentic Khmer Wedding Emblem: Interlocking Rings & Solitaire Diamond -->
            <svg class="w-24 h-12 mx-auto mb-2" viewBox="0 0 130 64" fill="none">
                <defs>
                    <linearGradient id="crestGoldHero" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="var(--primary-bright)"/>
                        <stop offset="50%" stop-color="var(--primary)"/>
                        <stop offset="100%" stop-color="var(--primary-dark)"/>
                    </linearGradient>
                    <linearGradient id="diamondGlowHero" x1="50%" y1="0%" x2="50%" y2="100%">
                        <stop offset="0%" stop-color="#ffffff"/>
                        <stop offset="60%" stop-color="var(--primary-bright)"/>
                        <stop offset="100%" stop-color="var(--primary)"/>
                    </linearGradient>
                    <filter id="sparkleGlowHero" x="-40%" y="-40%" width="180%" height="180%">
                        <feGaussianBlur stdDeviation="1" result="blur" />
                        <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                    </filter>
                </defs>

                <!-- Khmer Ornamental Wings / Lotus Laurel (Bottom & Flanks) -->
                <path d="M65 52 C50 56, 32 54, 18 44 C26 42, 36 43, 45 47" stroke="url(#crestGoldHero)" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M65 52 C80 56, 98 54, 112 44 C104 42, 94 43, 85 47" stroke="url(#crestGoldHero)" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M22 42 C14 36, 12 28, 8 20 C14 26, 24 30, 32 34" stroke="url(#crestGoldHero)" stroke-width="1.3" stroke-linecap="round"/>
                <path d="M108 42 C116 36, 118 28, 122 20 C116 26, 106 30, 98 34" stroke="url(#crestGoldHero)" stroke-width="1.3" stroke-linecap="round"/>
                <circle cx="8" cy="20" r="2" fill="var(--primary-bright)"/>
                <circle cx="122" cy="20" r="2" fill="var(--primary-bright)"/>
                <circle cx="65" cy="56" r="2.2" fill="var(--primary-bright)"/>

                <!-- Left Wedding Ring (Groom's Ring) -->
                <ellipse cx="52" cy="34" rx="15" ry="17" stroke="url(#crestGoldHero)" stroke-width="3.5" fill="none" />
                <ellipse cx="52" cy="34" rx="15" ry="17" stroke="rgba(255,255,255,0.7)" stroke-width="0.8" stroke-dasharray="8 30" fill="none" />

                <!-- Right Wedding Ring (Bride's Ring with Diamond Crown) -->
                <ellipse cx="78" cy="34" rx="15" ry="17" stroke="url(#crestGoldHero)" stroke-width="3.5" fill="none" />
                <ellipse cx="78" cy="34" rx="15" ry="17" stroke="rgba(255,255,255,0.7)" stroke-width="0.8" stroke-dasharray="8 30" fill="none" />

                <!-- Ring Interlocking Front Arc -->
                <path d="M63 26 C67 31, 67 38, 63 43" stroke="url(#crestGoldHero)" stroke-width="3.6" fill="none" stroke-linecap="round"/>

                <!-- Solitaire Diamond Crown on Bride's Ring -->
                <path d="M72 17 L78 20 L84 17" stroke="url(#crestGoldHero)" stroke-width="1.6" fill="none"/>
                <path d="M78 18 L78 21" stroke="url(#crestGoldHero)" stroke-width="1.6"/>
                <polygon points="78,4 85,11 83,17 73,17 71,11" fill="url(#diamondGlowHero)" stroke="url(#crestGoldHero)" stroke-width="1.2"/>
                <line x1="71" y1="11" x2="85" y2="11" stroke="rgba(255,255,255,0.9)" stroke-width="0.8"/>
                <line x1="78" y1="4" x2="75" y2="11" stroke="rgba(255,255,255,0.9)" stroke-width="0.7"/>
                <line x1="78" y1="4" x2="81" y2="11" stroke="rgba(255,255,255,0.9)" stroke-width="0.7"/>
                <line x1="75" y1="11" x2="78" y2="17" stroke="rgba(255,255,255,0.7)" stroke-width="0.7"/>
                <line x1="81" y1="11" x2="78" y2="17" stroke="rgba(255,255,255,0.7)" stroke-width="0.7"/>

                <!-- Radiant 4-Point Animated Sparkle on Diamond Tip -->
                <g class="hero-diamond-twinkle">
                    <circle cx="78" cy="4" r="5" fill="rgba(255,255,255,0.6)" filter="url(#sparkleGlowHero)"/>
                    <path d="M78 0 L79.5 3.5 L83 4 L79.5 4.5 L78 8 L76.5 4.5 L73 4 L76.5 3.5 Z" fill="#ffffff"/>
                    <circle cx="78" cy="4" r="1.4" fill="#ffffff"/>
                </g>

                <!-- Auspicious Crown Apex Motif over Center -->
                <path d="M62 13 C64 9, 66 9, 68 13" stroke="url(#crestGoldHero)" stroke-width="1.2" stroke-linecap="round"/>
                <circle cx="65" cy="8" r="1.5" fill="var(--primary-bright)"/>
            </svg>

            <!-- Auspicious Blessing - Bold & High Contrast -->
            <p class="hero-blessing-text text-xs sm:text-sm tracking-widest font-black mb-1 uppercase">
                {{ $translations['auspicious_blessing'] }}
            </p>

            <!-- Main Wedding Title with Continuous Royal Shimmer -->
            <h1 class="hero-wedding-title text-2xl sm:text-3xl md:text-4xl mb-3 leading-snug">
                {{ $translations['wedding_invitation'] }}
            </h1>

            <div class="flex items-center justify-center gap-2 my-3">
                <span class="h-[1.5px] w-12 bg-gradient-to-r from-transparent to-[var(--primary)]"></span>
                <span class="text-xs text-[var(--primary)]">✦</span>
                <span class="h-[1.5px] w-12 bg-gradient-to-l from-transparent to-[var(--primary)]"></span>
            </div>

            <!-- Couple Section with High-Contrast Bold Typography and Shimmer -->
            <div class="my-4 space-y-2">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-[var(--primary-tint)] border border-[var(--border-primary)] shadow-2xs mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--primary)]"></span>
                        <span class="text-[11px] md:text-xs uppercase tracking-widest font-black text-[var(--primary-dark)]">
                            {{ $translations['groom_title'] }}
                        </span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--primary)]"></span>
                    </div>
                    <div>
                        <div class="hero-couple-name text-3xl sm:text-4xl md:text-4xl leading-tight">
                            {{ $lang == 'km' ? $event->groom_name : $event->groom_name_en }}
                        </div>
                    </div>
                </div>

                <div class="py-1 flex items-center justify-center">
                    <div class="hero-ampersand-badge" title="{{ $translations['and'] }}">
                        <span class="relative z-10 f-heading text-sm md:text-base font-black text-white leading-none">&amp;</span>
                    </div>
                </div>

                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-[var(--primary-tint)] border border-[var(--border-primary)] shadow-2xs mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--primary)]"></span>
                        <span class="text-[11px] md:text-xs uppercase tracking-widest font-black text-[var(--primary-dark)]">
                            {{ $translations['bride_title'] }}
                        </span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--primary)]"></span>
                    </div>
                    <div>
                        <div class="hero-couple-name text-3xl sm:text-4xl md:text-4xl leading-tight">
                            {{ $lang == 'km' ? $event->bride_name : $event->bride_name_en }}
                        </div>
                    </div>
                </div>
            </div>

            @if($event->date ?? false)
            <div class="hero-date-badge inline-flex items-center gap-2 px-5 py-2 rounded-full mt-2 shadow-md">
                <span class="text-sm">🗓️</span>
                <span class="text-xs md:text-sm font-extrabold text-[var(--primary-dark)] tracking-wide">
                    {{ \Carbon\Carbon::parse($event->date)->translatedFormat('l, d F Y') }}
                </span>
            </div>

            <div id="countdown" class="flex justify-center gap-2 md:gap-3 mt-4">
                @foreach(['days', 'hours', 'minutes', 'seconds'] as $key)
                <div class="countdown-box-royal {{ $key === 'seconds' ? 'countdown-box-seconds' : '' }}">
                    <div id="cd-{{ $key }}" class="f-cinzel text-2xl md:text-3xl font-black text-slate-900 drop-shadow-xs">00</div>
                    <div class="text-[10px] md:text-[11px] uppercase font-black text-slate-700 mt-1 tracking-wider">{{ $translations[$key] }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-10 cursor-pointer flex flex-col items-center gap-1 opacity-90 hover:opacity-100 transition-opacity" onclick="document.getElementById('invitation-section').scrollIntoView({ behavior: 'smooth' })">
            <span class="f-heading text-[10.5px] font-bold uppercase tracking-widest theme-heading drop-shadow-sm">{{ $translations['scroll_down'] }}</span>
            <span class="animate-bounce text-sm theme-primary-text leading-none">⌄</span>
        </div>
    </section>

    {{-- INVITATION MESSAGE --}}
    <section id="invitation-section" class="pt-10 px-4">
        <div class="max-w-3xl mx-auto khmer-card p-6 md:p-10 text-center reveal">
            <!-- Authentic Khmer Royal Wedding Logo Badge -->
            <div class="invite-msg-badge" title="{{ $translations['wedding_invitation'] }}">
                <svg class="w-9 h-9" viewBox="0 0 56 50" fill="none">
                    <defs>
                        <linearGradient id="msgLogoGold" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="var(--primary-bright)"/>
                            <stop offset="50%" stop-color="var(--primary)"/>
                            <stop offset="100%" stop-color="var(--primary-dark)"/>
                        </linearGradient>
                        <linearGradient id="msgDiamondGlow" x1="50%" y1="0%" x2="50%" y2="100%">
                            <stop offset="0%" stop-color="#ffffff"/>
                            <stop offset="60%" stop-color="var(--primary-bright)"/>
                            <stop offset="100%" stop-color="var(--primary)"/>
                        </linearGradient>
                    </defs>

                    <!-- Lotus Laurel Wings Flanking Base -->
                    <path d="M12 38 C18 34, 23 35, 28 39 C33 35, 38 34, 44 38" stroke="url(#msgLogoGold)" stroke-width="1.6" stroke-linecap="round"/>
                    <path d="M16 42 C22 39, 25 40, 28 42 C31 40, 34 39, 40 42" stroke="url(#msgLogoGold)" stroke-width="1.2" stroke-linecap="round"/>
                    <circle cx="28" cy="42" r="1.5" fill="var(--primary-bright)"/>

                    <!-- Left Ring (Groom's Ring) -->
                    <ellipse cx="22" cy="26" rx="9" ry="10.5" stroke="url(#msgLogoGold)" stroke-width="2.6" fill="none"/>
                    <ellipse cx="22" cy="26" rx="9" ry="10.5" stroke="rgba(255,255,255,0.7)" stroke-width="0.6" stroke-dasharray="5 15" fill="none"/>

                    <!-- Right Ring (Bride's Ring with Diamond Crown) -->
                    <ellipse cx="34" cy="26" rx="9" ry="10.5" stroke="url(#msgLogoGold)" stroke-width="2.6" fill="none"/>
                    <ellipse cx="34" cy="26" rx="9" ry="10.5" stroke="rgba(255,255,255,0.7)" stroke-width="0.6" stroke-dasharray="5 15" fill="none"/>

                    <!-- Ring Interlocking Front Arc -->
                    <path d="M28.5 20 C30.5 23, 30.5 29, 28.5 32" stroke="url(#msgLogoGold)" stroke-width="2.8" stroke-linecap="round"/>

                    <!-- Solitaire Diamond Crown on Bride Ring -->
                    <path d="M30 15 L34 17 L38 15" stroke="url(#msgLogoGold)" stroke-width="1.3" fill="none"/>
                    <polygon points="34,6 39,11 37,15 31,15 29,11" fill="url(#msgDiamondGlow)" stroke="url(#msgLogoGold)" stroke-width="1"/>
                    <line x1="29" y1="11" x2="39" y2="11" stroke="rgba(255,255,255,0.9)" stroke-width="0.7"/>

                    <!-- Radiant Diamond Sparkle Twinkle -->
                    <g class="hero-diamond-twinkle">
                        <circle cx="34" cy="6" r="3.5" fill="rgba(255,255,255,0.6)"/>
                        <path d="M34 2 L35.2 4.8 L38 6 L35.2 7.2 L34 10 L32.8 7.2 L30 6 L32.8 4.8 Z" fill="#ffffff"/>
                        <circle cx="34" cy="6" r="1.1" fill="#ffffff"/>
                    </g>
                </svg>
            </div>

            <!-- Invitation Message with Generous Line-Height and Clean Font Weight -->
            <p class="invite-msg-body mb-4 f-moul">
                {{ $translations['invite_msg'] }}
            </p>

            <!-- Royal Ornamental Divider -->
            <div class="flex items-center justify-center gap-2 my-4">
                <span class="h-[1.5px] w-14 bg-gradient-to-r from-transparent to-[var(--primary)]"></span>
                <span class="text-xs text-[var(--primary)]">✦</span>
                <span class="h-[1.5px] w-14 bg-gradient-to-l from-transparent to-[var(--primary)]"></span>
            </div>

            @if($event->love_quote ?? false)
            <p class="text-xs md:text-sm italic font-medium mt-4 theme-muted-text">« {{ $event->love_quote }} »</p>
            @endif
        </div>
    </section>

      {{-- GALLERY --}}
    @if(($event->portfolios ?? false) && count($event->portfolios) > 0)
    <section class="py-8 px-4">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-6 reveal">
                <span class="text-xl theme-primary-text">🪷</span>
                <h2 class="f-moul text-base theme-heading mt-1">{{ $translations['gallery_title'] }}</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 reveal">
                @foreach($event->portfolios as $i => $photo)
                <a href="{{ asset('storage/' . $photo) }}" data-fancybox="gallery" class="rounded-xl overflow-hidden border shadow-sm block aspect-square theme-border theme-tint-bg">
                    <img src="{{ asset('storage/' . $photo) }}" alt="Photo {{ $i + 1 }}" class="w-full h-full object-cover" loading="lazy">
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- SCHEDULE --}}
    @if(!empty($event->schedules) && count($event->schedules))
    <section class="py-6 px-4">
        <div class="max-w-3xl mx-auto khmer-card p-6 md:p-8 reveal">
            <div class="text-center mb-6">
                <span class="text-xl theme-primary-text">⚜️</span>
                <h2 class="f-moul text-base theme-heading mt-1">{{ $translations['event_info'] }}</h2>
            </div>
            <div class="space-y-1">
                @foreach($event->schedules as $i => $schedule)
                @php
                    $rawTime = $schedule['time'] ?? null;
                    $hour    = $rawTime ? (int) explode(':', $rawTime)[0] : null;
                    $isPM    = $hour !== null && $hour >= 12;
                    if ($rawTime) {
                        $h12         = $hour === 0 ? 12 : ($hour > 12 ? $hour - 12 : $hour);
                        $min         = explode(':', $rawTime)[1] ?? '00';
                        $period      = $lang === 'en' ? ($isPM ? 'PM' : 'AM') : ($isPM ? ($hour >= 17 ? 'ល្ងាច' : 'រសៀល') : 'ព្រឹក');
                        $timeDisplay = ($lang === 'en' ? '' : 'ម៉ោង ') . $h12 . ':' . $min . ' ' . $period;
                    } else {
                        $timeDisplay = '-';
                    }
                @endphp
                <div class="khmer-timeline-item">
                    <div class="khmer-timeline-icon">{{ $i + 1 }}</div>
                    <div class="flex-1 pt-1">
                        <div class="text-xs font-bold theme-primary-text">{{ $timeDisplay }}</div>
                        <div class="f-moul text-xs mt-0.5" style="color: var(--text-color)">
                            {{ $schedule['label_' . $lang] ?? ($schedule['label_kh'] ?? ($schedule['label_km'] ?? '')) }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($event->address)
            <div class="mt-8 pt-6 border-t theme-border-light text-center">
                <span class="text-xs font-bold block mb-1 theme-heading">📍 {{ $translations['location_label'] }}</span>
                <p class="text-xs font-medium theme-muted-text">{{ $event->address }}</p>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- GOOGLE MAPS --}}
    @if($event->google_map ?? false)
    <section class="py-6 px-4">
        <div class="max-w-3xl mx-auto khmer-card google-map p-5 reveal">
            <h3 class="f-moul text-sm text-center mb-4 theme-heading">{{ $translations['location_label'] }}</h3>
            @if(str_contains($event->google_map, '<iframe'))
            <div class="rounded-xl overflow-hidden border aspect-video w-full theme-border iframe-container">
                {!! $event->google_map !!}
            </div>
            @else
            <div class="text-center py-4">
                <a href="{{ $event->google_map }}" target="_blank" rel="noopener" class="btn-gold-royal inline-flex items-center gap-2 px-6 py-3 rounded-full text-xs">
                    🗺️ {{ $translations['open_maps'] }}
                </a>
            </div>
            @endif
        </div>
    </section>
    @endif

  

    {{-- RSVP & WISHES --}}
    <section class="py-8 px-4">
        <div class="max-w-lg mx-auto khmer-card p-6 md:p-8 text-center reveal">
            <h2 class="f-moul text-sm theme-heading mb-2">{{ $translations['rsvp'] }}</h2>
            <p class="text-xs mb-5 theme-muted-text">{{ $translations['rsvp_intro'] }}</p>

            <div class="flex gap-3 justify-center mb-4">
                <button id="btn-yes" onclick="rsvpReply('yes')" class="flex-1 py-3 rounded-xl border-2 font-bold text-xs transition" style="{{ ($guest && $guest->is_attending === 'yes') ? 'background: var(--primary); color: #ffffff; border-color: var(--primary);' : 'border-color: var(--primary); color: var(--primary-heading);' }}">
                    ✓ {{ $translations['attending'] }}
                </button>
                <button id="btn-no" onclick="rsvpReply('no')" class="flex-1 py-3 rounded-xl border border-stone-300 text-stone-500 text-xs hover:bg-stone-100 transition {{ ($guest && $guest->is_attending === 'no') ? 'bg-stone-200 text-stone-700' : '' }}">
                    ✗ {{ $translations['not_attending'] }}
                </button>
            </div>
            <div id="rsvp-msg" class="text-xs p-3 rounded-lg border hidden font-medium theme-tint-bg theme-border theme-heading"></div>

            <div class="mt-8 pt-6 border-t theme-border-light">
                <h3 class="f-moul text-xs theme-heading mb-3">{{ $translations['send_wishes'] }}</h3>
                <textarea id="wishes-text" rows="3" class="w-full p-3 text-xs border rounded-xl outline-none theme-border" style="background: var(--card-bg); color: var(--text-color);" placeholder="{{ $translations['wishes_placeholder'] }}"></textarea>
                <button onclick="sendWishes()" class="btn-gold-royal w-full py-2.5 rounded-xl text-xs mt-2.5">
                    {{ $translations['wishes_btn'] }}
                </button>
                <p id="wishes-sent" class="text-xs mt-2 hidden font-semibold theme-heading">
                    💛 {{ $translations['wishes_success'] }}
                </p>
            </div>
        </div>
    </section>

    {{-- WISHES SLIDER --}}
    @if(isset($wishes) && count($wishes) > 0)
    <section class="py-8 px-4 overflow-hidden">
        <div class="max-w-3xl mx-auto">
            <h2 class="f-moul text-sm text-center mb-6 reveal theme-heading">{{ $translations['guest_comments'] }}</h2>
            <div class="swiper wishes-swiper reveal">
                <div class="swiper-wrapper">
                    @foreach($wishes as $wish)
                    <div class="swiper-slide p-2">
                        <div class="rounded-2xl p-5 border shadow-sm text-center theme-border" style="background: var(--card-bg);">
                            <h4 class="f-moul text-xs theme-heading mb-1">{{ $wish->name ?? $translations['guest'] }}</h4>
                            <p class="text-xs italic my-2 theme-muted-text">« {{ $wish->note }} »</p>
                            <small class="text-[10px] block mt-2 opacity-60">{{ \Carbon\Carbon::parse($wish->created_at)->translatedFormat('d F Y') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- FOOTER --}}
    <footer class="py-8 px-4 text-center border-t theme-border-light">
        <p class="f-moul text-sm theme-heading">
            {{ $lang == 'km' ? $event->groom_name : $event->groom_name_en }} &amp; {{ $lang == 'km' ? $event->bride_name : $event->bride_name_en }}
        </p>
    </footer>

    {{-- GIFT MODAL --}}
    @if($guest)
    <div id="gift-modal" role="dialog" aria-modal="true" style="display:none; position:fixed; inset:0; z-index:99998; align-items:center; justify-content:center; padding:16px;">
        <div onclick="closeGiftModal()" style="position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(6px);"></div>
        <div class="relative max-w-sm w-full rounded-2xl border-2 p-6 shadow-2xl z-10 max-h-[90vh] overflow-y-auto theme-border" style="background: var(--card-bg);">
            <button onclick="closeGiftModal()" class="absolute top-3 right-3 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold theme-light-bg theme-heading">✕</button>
            <div class="text-center mb-4">
                <h3 class="f-moul text-sm theme-heading">🎁 {{ $translations['gift_qr'] }}</h3>
            </div>
            @if($event->qr_code ?? false)
            <div class="text-center mb-4">
                <img src="{{ asset('storage/' . $event->qr_code) }}" alt="QR" class="w-40 h-40 object-contain mx-auto border p-2 rounded-xl theme-border">
                <a href="{{ asset('storage/' . $event->qr_code) }}" download="wedding-qr.png" class="inline-block mt-2 text-[11px] font-bold border px-3 py-1 rounded-full theme-border theme-tint-bg theme-heading">
                    ⬇ {{ $t('ទាញយក QR Code', 'Download QR Code') }}
                </a>
            </div>
            @endif
            <form id="gift-form" onsubmit="submitDonation(event)" class="space-y-3 text-xs">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-semibold mb-1 theme-muted-text">{{ $t('របៀបបង់', 'Payment') }}</label>
                        <select id="gift-payment" class="w-full p-2 border rounded-lg text-xs theme-border" style="background: var(--card-bg); color: var(--text-color);">
                            <option value="cash">{{ __('messages.payment_cash') ?? 'Cash' }}</option>
                            <option value="qr_code">{{ __('messages.payment_qr_code') ?? 'QR Code' }}</option>
                            <option value="other">{{ __('messages.payment_other') ?? 'Other' }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold mb-1 theme-muted-text">{{ $t('រូបិយប័ណ្ណ', 'Currency') }}</label>
                        <select id="gift-cash-method" class="w-full p-2 border rounded-lg text-xs theme-border" style="background: var(--card-bg); color: var(--text-color);">
                            <option value="usd">USD ($)</option>
                            <option value="khr">KHR (៛)</option>
                            <option value="both">{{ __('messages.both') ?? 'Both' }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold mb-1 theme-muted-text">{{ __('messages.amount_usd') ?? 'Amount (USD)' }}</label>
                        <input type="number" id="gift-usd" class="w-full p-2 border rounded-lg text-xs theme-border" style="background: var(--card-bg); color: var(--text-color);" value="0" min="0">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold mb-1 theme-muted-text">{{ __('messages.amount_khr') ?? 'Amount (KHR)' }}</label>
                        <input type="number" id="gift-khr" class="w-full p-2 border rounded-lg text-xs theme-border" style="background: var(--card-bg); color: var(--text-color);" value="0" min="0">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold mb-1 theme-muted-text">{{ __('messages.note') ?? 'Note' }}</label>
                    <textarea id="gift-note" rows="2" class="w-full p-2 border rounded-lg text-xs theme-border" style="background: var(--card-bg); color: var(--text-color);" placeholder="{{ $t('ពាក្យជូនពរ...', 'Note...') }}"></textarea>
                </div>
                <button type="submit" id="gift-submit" class="btn-gold-royal w-full py-2.5 rounded-xl text-xs font-bold">
                    <span id="gift-submit-label">{{ $t('ផ្ញើចំណងដៃ', 'Submit Gift') }}</span>
                </button>
            </form>
            <div id="gift-success-msg" class="hidden text-center py-4 text-xs font-bold theme-heading">
                💛 {{ $t('សូមអរគុណសម្រាប់ចំណងដៃរបស់អ្នក!', 'Thank you for your gift!') }}
            </div>
        </div>
    </div>
    @endif

</div>

<script>
// ─── LIVE PARTICLES & GOLD SHIMMER ENGINE ───
let spawnParticleBurst = null;

(function initLiveAtmosphere() {
    const canvas = document.getElementById('leaves-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    // Dynamic palette strictly derived from API colors
    const primaryRgb   = "{{ $primaryRgb }}";
    const primaryHex   = "{{ $themeColor }}";
    const brightHex    = "{{ $primaryBright }}";
    const lightHex     = "{{ $primaryLight }}";
    const tintHex      = "{{ $primaryTint }}";

    const petalPalette = [
        brightHex,
        primaryHex,
        lightHex,
        `rgba(${primaryRgb}, 0.90)`,
        `rgba(${primaryRgb}, 0.65)`,
        `rgba(${primaryRgb}, 0.40)`,
        '#FFFFFF',
        'rgba(255, 255, 255, 0.85)'
    ];

    // Falling wedding flower petals & diamond stardust
    const particles = Array.from({ length: 34 }, () => ({
        x: Math.random() * window.innerWidth,
        y: Math.random() * window.innerHeight,
        size: 3.5 + Math.random() * 5.5,
        speedY: 0.5 + Math.random() * 1.1,
        speedX: (Math.random() - 0.5) * 0.5,
        swaySpeed: 0.02 + Math.random() * 0.03,
        swayOffset: Math.random() * Math.PI * 2,
        rotation: Math.random() * 360,
        rotSpeed: (Math.random() - 0.5) * 2.5,
        color: petalPalette[Math.floor(Math.random() * petalPalette.length)],
        alpha: 0.35 + Math.random() * 0.55,
        isSparkle: Math.random() > 0.65
    }));

    // Burst particles on tap
    let burstParticles = [];
    spawnParticleBurst = function(x, y) {
        for (let i = 0; i < 45; i++) {
            const angle = Math.random() * Math.PI * 2;
            const velocity = 2.5 + Math.random() * 6.5;
            burstParticles.push({
                x: x,
                y: y,
                vx: Math.cos(angle) * velocity,
                vy: Math.sin(angle) * velocity,
                size: 3 + Math.random() * 4.5,
                alpha: 1,
                decay: 0.015 + Math.random() * 0.02,
                color: petalPalette[Math.floor(Math.random() * petalPalette.length)],
                rotation: Math.random() * 360,
                rotSpeed: (Math.random() - 0.5) * 6
            });
        }
    };

    let step = 0;
    function loop() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        step++;

        // Draw regular drifting flower petals & sparkles
        particles.forEach(p => {
            p.y += p.speedY;
            p.x += Math.sin(step * p.swaySpeed + p.swayOffset) * 0.65 + p.speedX;
            p.rotation += p.rotSpeed;

            if (p.y > canvas.height + 25) {
                p.y = -20;
                p.x = Math.random() * canvas.width;
            }

            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate((p.rotation * Math.PI) / 180);
            ctx.globalAlpha = p.alpha;
            ctx.fillStyle = p.color;

            if (p.isSparkle) {
                // 4-pointed diamond sparkle
                const s = p.size * 0.85;
                ctx.beginPath();
                ctx.moveTo(0, -s);
                ctx.quadraticCurveTo(0, 0, s, 0);
                ctx.quadraticCurveTo(0, 0, 0, s);
                ctx.quadraticCurveTo(0, 0, -s, 0);
                ctx.quadraticCurveTo(0, 0, 0, -s);
                ctx.fill();
            } else {
                // Realistic curved wedding flower petal (ស្រទាប់ផ្កា)
                const s = p.size;
                ctx.beginPath();
                ctx.moveTo(0, -s * 1.3);
                ctx.bezierCurveTo(s * 0.85, -s * 0.5, s * 0.85, s * 0.7, 0, s * 1.3);
                ctx.bezierCurveTo(-s * 0.85, s * 0.7, -s * 0.85, -s * 0.5, 0, -s * 1.3);
                ctx.fill();
            }
            ctx.restore();
        });

        // Draw burst particles
        for (let i = burstParticles.length - 1; i >= 0; i--) {
            const b = burstParticles[i];
            b.x += b.vx;
            b.y += b.vy;
            b.vy += 0.12; // gentle gravity
            b.rotation += b.rotSpeed || 0;
            b.alpha -= b.decay;

            if (b.alpha <= 0) {
                burstParticles.splice(i, 1);
                continue;
            }

            ctx.save();
            ctx.translate(b.x, b.y);
            ctx.rotate(((b.rotation || 0) * Math.PI) / 180);
            ctx.globalAlpha = b.alpha;
            ctx.fillStyle = b.color;
            ctx.beginPath();
            ctx.ellipse(0, 0, b.size * 0.65, b.size * 1.2, 0, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }

        requestAnimationFrame(loop);
    }
    loop();
})();

window.openWeddingPage = function(event) {
    if (event) {
        try { event.stopPropagation?.(); event.preventDefault?.(); } catch(_) {}
    }
    const opener = document.getElementById('opener');
    const page   = document.getElementById('wedding-page');
    const seal   = document.querySelector('.wax-seal-btn');

    if (!opener || opener.classList.contains('closing') || opener.style.display === 'none') {
        if (page) page.style.display = 'block';
        return;
    }

    // Trigger golden celebration burst at seal position
    if (typeof spawnParticleBurst === 'function' && seal) {
        try {
            const rect = seal.getBoundingClientRect();
            spawnParticleBurst(rect.left + rect.width / 2, rect.top + rect.height / 2);
        } catch(_) {}
    }

    // Immediately show the page underneath so there is never a blank screen
    if (page) {
        page.style.display = 'block';
    }

    opener.classList.add('closing');
    try {
        const music = document.getElementById('bg-music');
        if (music && music.paused) music.play().catch(() => {});
    } catch(_) {}

    setTimeout(() => {
        opener.style.display = 'none';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        try { initCountdown(); } catch(_) {}
        try { initReveal(); } catch(_) {}
        try { initFancybox(); } catch(_) {}
        try { initSwiper(); } catch(_) {}
    }, 550);
};

@php
    $eventTimestamp = null;
    if (!empty($event->date)) {
        try {
            $parsedDate = \Carbon\Carbon::parse($event->date)->format('Y-m-d');
            $parsedTime = !empty($event->time) ? substr($event->time, 0, 5) : '00:00';
            $eventTimestamp = \Carbon\Carbon::parse("{$parsedDate} {$parsedTime}:00")->timestamp * 1000;
        } catch (\Throwable $e) {
            $eventTimestamp = null;
        }
    }
@endphp
const EVENT_TS = @json($eventTimestamp);

function initCountdown() {
    if (!EVENT_TS || isNaN(EVENT_TS)) return;
    const pad = n => String(n).padStart(2, '0');
    function tick() {
        const diff = EVENT_TS - Date.now();
        if (diff <= 0 || isNaN(diff)) return;
        const d = document.getElementById('cd-days');
        const h = document.getElementById('cd-hours');
        const m = document.getElementById('cd-minutes');
        const s = document.getElementById('cd-seconds');
        if (d) d.textContent = pad(Math.floor(diff / 86400000));
        if (h) h.textContent = pad(Math.floor((diff % 86400000) / 3600000));
        if (m) m.textContent = pad(Math.floor((diff % 3600000) / 60000));
        if (s) s.textContent = pad(Math.floor((diff % 60000) / 1000));
    }
    tick();
    setInterval(tick, 1000);
}

function initReveal() {
    const io = new IntersectionObserver(entries => entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
    }), { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
}

function initFancybox() {
    if (typeof Fancybox !== 'undefined') Fancybox.bind('[data-fancybox]');
}

function initSwiper() {
    if (typeof Swiper !== 'undefined') {
        new Swiper('.wishes-swiper', {
            loop: true,
            autoplay: { delay: 4000 },
            slidesPerView: 1.2,
            spaceBetween: 12,
            breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
        });
    }
}

function toggleMusic() {
    const music = document.getElementById('bg-music');
    const btn   = document.getElementById('music-btn');
    if (!music) return;
    if (music.paused) {
        music.play().catch(() => {});
        btn && (btn.innerHTML = '<span>🎵</span>');
    } else {
        music.pause();
        btn && (btn.innerHTML = '<span class="opacity-60">🔇</span>');
    }
}

const WISHES_URL   = "{{ $guest ? route('event.wishes',   ['slug' => $event->slug, 'gid' => $guest->id]) : null }}";
const RSVP_URL     = "{{ $guest ? route('event.rsvp',     ['slug' => $event->slug, 'gid' => $guest->id]) : null }}";
const DONATION_URL = "{{ $guest ? route('event.donation', ['slug' => $event->slug, 'gid' => $guest->id]) : null }}";
const CSRF_TOKEN   = "{{ csrf_token() }}";

async function rsvpReply(status) {
    document.getElementById('btn-yes')?.classList.toggle('bg-amber-600', status === 'yes');
    document.getElementById('btn-yes')?.classList.toggle('text-white',    status === 'yes');
    document.getElementById('btn-no')?.classList.toggle('bg-stone-200',  status === 'no');
    const msg = document.getElementById('rsvp-msg');
    if (msg) {
        msg.classList.remove('hidden');
        msg.textContent = status === 'yes' ? '🎉 សូមអរគុណសម្រាប់ការឆ្លើយតប!' : '💛 សូមអរគុណ!';
    }
    if (RSVP_URL) {
        try {
            const res = await fetch(RSVP_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({ status })
            });
            const data = await res.json();
            if (data.message && msg) msg.textContent = data.message;
        } catch (_) {}
    }
}

async function sendWishes() {
    const el = document.getElementById('wishes-text');
    const text = el ? el.value.trim() : '';
    if (!text) return;
    if (WISHES_URL) {
        try {
            await fetch(WISHES_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({ wishes: text })
            });
        } catch (_) {}
    }
    document.getElementById('wishes-sent')?.classList.remove('hidden');
    if (el) el.value = '';
}

function openGiftModal() { document.getElementById('gift-modal') && (document.getElementById('gift-modal').style.display = 'flex'); }
function closeGiftModal() { document.getElementById('gift-modal') && (document.getElementById('gift-modal').style.display = 'none'); }

async function submitDonation(e) {
    e.preventDefault();
    if (!DONATION_URL) return;
    const btn = document.getElementById('gift-submit');
    if (btn) btn.disabled = true;
    const payload = {
        payment_method: document.getElementById('gift-payment')?.value || 'cash',
        cash_method:    document.getElementById('gift-cash-method')?.value || 'usd',
        amount_usd:     parseFloat(document.getElementById('gift-usd')?.value) || 0,
        amount_khr:     parseFloat(document.getElementById('gift-khr')?.value) || 0,
        note:           document.getElementById('gift-note')?.value || '',
    };
    try {
        const res = await fetch(DONATION_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('gift-form').style.display = 'none';
            document.getElementById('gift-success-msg').classList.remove('hidden');
            document.getElementById('gift-btn-text') && (document.getElementById('gift-btn-text').textContent = '{{ $t("បានជូនចំណងដៃ", "Gifted") }}');
            setTimeout(closeGiftModal, 2200);
        }
    } catch (_) { if (btn) btn.disabled = false; }
}

function bindOpenerEvents() {
    const seal = document.querySelector('.wax-seal-btn');
    if (seal) {
        seal.onclick = window.openWeddingPage;
    }
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindOpenerEvents);
} else {
    bindOpenerEvents();
}
</script>
</body>
</html>
