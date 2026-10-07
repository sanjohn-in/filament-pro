<!DOCTYPE html>
<html lang="km">
<head>
    @php
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
    $themeColor = $sanitizeHex($event->theme_color ?? null, '#D4AF37');
    // Page background color from API
    $bgColor    = $sanitizeHex($event->bg_color ?? null, '#101418');

    [$pR, $pG, $pB] = $hexToRgb($themeColor);
    $primaryRgb = "{$pR}, {$pG}, {$pB}";

    [$bgR, $bgG, $bgB] = $hexToRgb($bgColor);
    $bgRgb = "{$bgR}, {$bgG}, {$bgB}";

    $bgIsLight = $isLight($bgColor);

    $primaryLight    = $mixHex($themeColor, '#ffffff', 84); // pastel tint
    $primaryTint     = $mixHex($themeColor, '#ffffff', 94); // soft background tint
    $primaryDark     = $mixHex($themeColor, '#0d1217', 60); // dark heading shade
    $primaryDeep     = $mixHex($themeColor, '#05080a', 78); // hero background deep shade
    $primaryBright   = $mixHex($themeColor, '#ffffff', 35); // radiant highlight

    $textColor       = $bgIsLight ? '#1e252b' : '#f0f4f8';
    $textMuted       = $bgIsLight ? '#5a6672' : '#94a3b8';
    $cardBg          = $bgIsLight ? '#ffffff' : $mixHex($bgColor, '#ffffff', 6);
    $headingColor    = $bgIsLight ? $primaryDark : $primaryBright;

    $eventTimestamp = null;
    if (!empty($event->date)) {
        try {
            $timePart = !empty($event->time) ? substr($event->time, 0, 5) : '00:00';
            $eventTimestamp = \Carbon\Carbon::parse("{$event->date} {$timePart}")->timestamp * 1000;
        } catch (\Exception $e) {
            $eventTimestamp = null;
        }
    }

    $translations = [
        'auspicious_blessing' => $t('សិរីសួស្តី ជ័យមង្គល វិបុលសុខ មហាប្រសើរ', 'Auspicious Blessings & Eternal Joy'),
        'wedding_invitation'  => $t('សិរីមង្គលអាពាហ៍ពិពាហ៍', 'Khmer Wedding Invitation'),
        'respectfully_to'     => $t('សូមគោរពអញ្ជើញ', 'Cordially Inviting'),
        'guest_honor_title'   => $t('ឯកឧត្តម លោកជំទាវ លោក លោកស្រី អ្នកនាងកញ្ញា', 'Honored Guests & Dignitaries'),
        'invite_msg'          => $t('យើងខ្ញុំមានកិត្តិយសសូមគោរពអញ្ជើញ ឯកឧត្តម លោកឧកញ៉ា លោកជំទាវ លោក លោកស្រី អ្នកនាងកញ្ញា អញ្ជើញចូលរួមជាអធិបតី និងជាភ្ញៀវកិត្តិយស ដើម្បីប្រសិទ្ធិពរជ័យសិរីសួស្តី ជ័យមង្គល ក្នុងពិធីអាពាហ៍ពិពាហ៍ របស់យើងខ្ញុំទាំងពីរ។', 'We cordially invite you to celebrate our wedding day. Your presence will bring us great honor and joy.'),
        'open_invite'         => $t('បើកការអញ្ជើញ', 'Open Invitation'),
        'tap_to_open'         => $t('ចុចត្រង់នេះដើម្បីបើកសំបុត្រ', 'Tap here to open invitation'),
        'scroll_down'         => $t('អូសចុះក្រោម', 'Scroll Down'),
        'groom_title'         => $t('កូនប្រុស', 'Groom'),
        'bride_title'         => $t('កូនស្រី', 'Bride'),
        'and'                 => $t('និង', '&'),
        'gallery_title'       => $t('កម្រងរូបភាពអនុស្សាវរីយ៍', 'Wedding Gallery'),
        'event_info'          => $t('កម្មវិធីមង្គលការ', 'Ceremony Schedule'),
        'location_label'      => $t('ទីតាំងប្រារព្ធពិធី', 'Wedding Venue'),
        'open_maps'           => $t('បើកមើល Google Maps', 'Open in Google Maps'),
        'gift_qr'             => $t('ចំណងដៃតាម QR Code', 'Wedding Gift QR Code'),
        'rsvp'                => $t('បញ្ជាក់ការចូលរួម (RSVP)', 'Confirm Attendance (RSVP)'),
        'rsvp_intro'          => $t('វត្តមានដ៏ខ្ពង់ខ្ពស់របស់លោកអ្នក ជាកិត្តិយស និងជាក្តីរីករាយដ៏ធំបំផុតសម្រាប់យើងខ្ញុំ។', 'Your gracious presence is the greatest joy and blessing for our celebration.'),
        'attending'           => $t('ចូលរួម (Attending)', 'Attending'),
        'not_attending'       => $t('មិនអាចចូលរួម (Decline)', 'Decline'),
        'send_wishes'         => $t('ផ្ញើពាក្យប្រសិទ្ធិពរជ័យ', 'Send Wishes & Blessings'),
        'wishes_placeholder'  => $t('សូមសរសេរពាក្យជូនពរសម្រាប់គូស្វាមីភរិយាថ្មី…', 'Write your warm wishes for the couple...'),
        'wishes_btn'          => $t('ផ្ញើពាក្យជូនពរ', 'Submit Wishes'),
        'wishes_success'      => $t('សូមអរគុណសម្រាប់ពាក្យប្រសិទ្ធិពរជ័យដ៏កក់ក្តៅរបស់អ្នក!', 'Thank you so much for your heartwarming wishes!'),
        'guest_comments'      => $t('ពាក្យជូនពរពីភ្ញៀវកិត្តិយស', 'Wishes from Honored Guests'),
        'days'                => $t('ថ្ងៃ', 'Days'),
        'hours'               => $t('ម៉ោង', 'Hours'),
        'minutes'             => $t('នាទី', 'Mins'),
        'seconds'             => $t('វិនាទី', 'Secs'),
        'guest'               => $t('ភ្ញៀវកិត្តិយស', 'Guest')
    ];
    @endphp

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.css"/>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.umd.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Moul&family=Moulpali&family=Kantumruy+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Cinzel:wght@500;600;700;800&display=swap" rel="stylesheet">

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
            --text-light: {{ $textColor }};
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
            color: var(--text-light);
            overflow-x: hidden;
            line-height: 1.7;
        }

        .f-moul { 
            font-family: 'Moul', 'Khmer OS Muol', {{ $lang == 'km' ? 'cursive, serif' : "'Cinzel', 'Playfair Display', Georgia, serif" }}; 
            font-weight: normal; 
        }
        .f-cinzel { font-family: 'Cinzel', serif; letter-spacing: 0.14em; }
        .f-heading {
            font-family: {{ $lang == 'km' ? "'Moul', 'Khmer OS Muol', cursive, serif" : "'Cinzel', 'Playfair Display', Georgia, serif" }};
            letter-spacing: {{ $lang == 'km' ? 'normal' : '0.04em' }};
        }
        .f-modern-title {
            font-family: {{ $lang == 'km' ? "'Moul', 'Khmer OS Muol', cursive, serif" : "'Cinzel', 'Playfair Display', Georgia, serif" }};
            letter-spacing: {{ $lang == 'km' ? '0.01em' : '0.06em' }};
        }
        .f-modern-name {
            font-family: {{ $lang == 'km' ? "'Moul', 'Khmer OS Muol', cursive, serif" : "'Playfair Display', 'Cinzel', Georgia, serif" }};
            font-weight: 700;
            letter-spacing: {{ $lang == 'km' ? 'normal' : '0.03em' }};
        }
        .hero-letter-glow {
            color: #ffffff !important;
            text-shadow: 
                0 2px 4px rgba(0, 0, 0, 0.88),
                0 4px 14px rgba(0, 0, 0, 0.75),
                0 8px 30px rgba(0, 0, 0, 0.55),
                0 0 2px rgba(0, 0, 0, 0.95);
        }
        .hero-accent-glow {
            color: var(--primary-bright) !important;
            text-shadow: 
                0 2px 4px rgba(0, 0, 0, 0.88),
                0 4px 12px rgba(0, 0, 0, 0.65),
                0 0 2px rgba(0, 0, 0, 0.95);
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

        .gold-modern-gradient {
            background: linear-gradient(135deg, var(--primary-bright) 0%, var(--primary) 50%, var(--primary-dark) 85%, var(--primary-bright) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modern-card {
            background: var(--card-bg);
            border: 1.5px solid var(--border-primary);
            border-radius: 20px;
            box-shadow: 0 14px 40px -10px rgba(0, 0, 0, 0.35);
        }

        /* Opener */
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

        .modern-opener-card {
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
            box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.28), 0 0 35px rgba({{ $primaryRgb }}, 0.18);
            animation: cardFloatIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .modern-opener-card::after {
            content: '';
            position: absolute;
            inset: 8px;
            border: 1px dashed var(--border-light);
            border-radius: 20px;
            pointer-events: none;
        }
        @keyframes cardFloatIn {
            0% { opacity: 0; transform: translateY(24px) scale(0.96); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Prominent Wedding Opener Button (✦ បើកការអញ្ជើញ ✦) */
        .modern-open-invite-btn {
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
            animation: modernBtnBreath 2.8s infinite ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }

        .modern-open-invite-btn:hover {
            transform: translateY(-2px) scale(1.025);
            box-shadow: 
                0 14px 32px -4px rgba({{ $primaryRgb }}, 0.70),
                0 0 0 3px var(--card-bg),
                0 0 0 5.5px var(--primary-bright);
        }

        .modern-open-invite-btn:active {
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
            animation: shimmerSweep3 3.2s infinite ease-in-out;
            pointer-events: none;
        }

        @keyframes shimmerSweep3 {
            0%, 35% { left: -120%; }
            70%, 100% { left: 180%; }
        }

        @keyframes modernBtnBreath {
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
            color: var(--primary-bright);
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
            background-size: cover;
            background-position: center 20%;
            background-repeat: no-repeat;
            overflow: hidden;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(circle at center 42%, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0.16) 55%, rgba(0, 0, 0, 0.50) 100%),
                linear-gradient(180deg, rgba(0, 0, 0, 0.40) 0%, transparent 22%, transparent 68%, rgba(0, 0, 0, 0.65) 100%);
            z-index: 1;
        }

        /* Fresh Luminous Frosted Modern Plaque for Hero */
        .hero-invitation-modern {
            position: relative;
            z-index: 10;
            max-width: 480px;
            width: 100%;
            margin: auto;
            background: {{ $bgIsLight ? 'rgba(255, 255, 255, 0.86)' : 'rgba(20, 26, 35, 0.85)' }};
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1.5px solid rgba(255, 255, 255, 0.95);
            border-radius: 32px;
            padding: clamp(22px, 3.5vh, 34px) clamp(16px, 4vw, 26px);
            box-shadow: 
                0 25px 60px -10px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.95) inset,
                0 0 35px rgba({{ $primaryRgb }}, 0.18);
        }
        .hero-invitation-modern::before {
            content: '';
            position: absolute;
            inset: 8px;
            border: 1px dashed rgba({{ $primaryRgb }}, 0.35);
            border-radius: 24px;
            pointer-events: none;
        }

        .countdown-modern {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1.5px solid rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            padding: 10px 14px;
            min-width: 66px;
            box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.25), 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        #leaves-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 10001; /* Live gold stardust floats over opener & all content */
        }

        #wedding-page {
            padding-bottom: 85px; /* Safe space so floating dock never covers content */
        }

        .timeline-modern-item {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 14px 0;
        }
        .timeline-modern-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 18px;
            top: 48px;
            bottom: -6px;
            width: 2px;
            background: linear-gradient(180deg, var(--primary), rgba({{ $primaryRgb }}, 0.1));
        }
        .timeline-modern-dot {
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--card-bg);
            border: 2px solid var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-bright);
            font-size: 13px;
            font-weight: bold;
            z-index: 1;
            box-shadow: 0 2px 8px rgba({{ $primaryRgb }}, 0.25);
        }

        .btn-modern-gold {
            background: linear-gradient(135deg, var(--primary-bright) 0%, var(--primary) 50%, var(--primary-dark) 100%);
            color: {{ $bgIsLight ? '#101418' : '#0a0d10' }};
            font-weight: 700;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba({{ $primaryRgb }}, 0.35);
        }
        .btn-modern-gold:hover { transform: translateY(-2px); filter: brightness(1.08); }

        .reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        .reveal.in { opacity: 1; transform: translateY(0); }

        .floating-action-btn {
            position: fixed;
            z-index: 9990;
            border-radius: 99px;
            background: var(--card-bg);
            color: var(--primary-bright);
            border: 1.5px solid var(--primary);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
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
            .modern-opener-card {
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
     THEME 3: MODERN LUXURY KHMER OPENER
══════════════════════════════════════════════ --}}
<div id="opener" role="dialog" aria-modal="true" aria-label="Modern Luxury Invitation Opener">
    @if($coverImage)
    <div class="opener-bg-photo" style="background-image: url('{{ $coverImage }}');"></div>
    <div class="opener-bg-tint"></div>
    @endif
    <div class="modern-opener-card">
        
        <!-- Authentic Khmer Wedding Emblem: Interlocking Rings, Diamond Solitaire & Modern Laurel -->
        <svg class="w-28 h-14 mx-auto mb-1.5" viewBox="0 0 130 64" fill="none">
            <defs>
                <linearGradient id="crestGold3" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="var(--primary-bright)"/>
                    <stop offset="50%" stop-color="var(--primary)"/>
                    <stop offset="100%" stop-color="var(--primary-dark)"/>
                </linearGradient>
                <linearGradient id="diamondGlow3" x1="50%" y1="0%" x2="50%" y2="100%">
                    <stop offset="0%" stop-color="#ffffff"/>
                    <stop offset="60%" stop-color="var(--primary-bright)"/>
                    <stop offset="100%" stop-color="var(--primary)"/>
                </linearGradient>
                <filter id="sparkleGlow3" x="-20%" y="-20%" width="140%" height="140%">
                    <feGaussianBlur stdDeviation="1.2" result="blur" />
                    <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                </filter>
            </defs>

            <!-- Khmer Ornamental Wings / Lotus Laurel (Bottom & Flanks) -->
            <path d="M65 52 C50 56, 32 54, 18 44 C26 42, 36 43, 45 47" stroke="url(#crestGold3)" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M65 52 C80 56, 98 54, 112 44 C104 42, 94 43, 85 47" stroke="url(#crestGold3)" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M22 42 C14 36, 12 28, 8 20 C14 26, 24 30, 32 34" stroke="url(#crestGold3)" stroke-width="1.3" stroke-linecap="round"/>
            <path d="M108 42 C116 36, 118 28, 122 20 C116 26, 106 30, 98 34" stroke="url(#crestGold3)" stroke-width="1.3" stroke-linecap="round"/>
            <circle cx="8" cy="20" r="2" fill="var(--primary-bright)"/>
            <circle cx="122" cy="20" r="2" fill="var(--primary-bright)"/>
            <circle cx="65" cy="56" r="2.2" fill="var(--primary-bright)"/>

            <!-- Left Wedding Ring (Groom's Ring) -->
            <ellipse cx="52" cy="34" rx="15" ry="17" stroke="url(#crestGold3)" stroke-width="3.5" fill="none" />
            <ellipse cx="52" cy="34" rx="15" ry="17" stroke="rgba(255,255,255,0.7)" stroke-width="0.8" stroke-dasharray="8 30" fill="none" />

            <!-- Right Wedding Ring (Bride's Ring with Diamond Crown) -->
            <ellipse cx="78" cy="34" rx="15" ry="17" stroke="url(#crestGold3)" stroke-width="3.5" fill="none" />
            <ellipse cx="78" cy="34" rx="15" ry="17" stroke="rgba(255,255,255,0.7)" stroke-width="0.8" stroke-dasharray="8 30" fill="none" />

            <!-- Ring Interlocking Front Arc -->
            <path d="M63 26 C67 31, 67 38, 63 43" stroke="url(#crestGold3)" stroke-width="3.6" fill="none" stroke-linecap="round"/>

            <!-- Solitaire Diamond Crown on Bride's Ring -->
            <path d="M72 17 L78 20 L84 17" stroke="url(#crestGold3)" stroke-width="1.6" fill="none"/>
            <path d="M78 18 L78 21" stroke="url(#crestGold3)" stroke-width="1.6"/>
            <polygon points="78,4 85,11 83,17 73,17 71,11" fill="url(#diamondGlow3)" stroke="url(#crestGold3)" stroke-width="1.2"/>
            <line x1="71" y1="11" x2="85" y2="11" stroke="rgba(255,255,255,0.9)" stroke-width="0.8"/>
            <line x1="78" y1="4" x2="75" y2="11" stroke="rgba(255,255,255,0.9)" stroke-width="0.7"/>
            <line x1="78" y1="4" x2="81" y2="11" stroke="rgba(255,255,255,0.9)" stroke-width="0.7"/>
            <line x1="75" y1="11" x2="78" y2="17" stroke="rgba(255,255,255,0.7)" stroke-width="0.7"/>
            <line x1="81" y1="11" x2="78" y2="17" stroke="rgba(255,255,255,0.7)" stroke-width="0.7"/>

            <!-- Radiant 4-Point Sparkle on Diamond Tip -->
            <path d="M78 0 L79.5 3.5 L83 4 L79.5 4.5 L78 8 L76.5 4.5 L73 4 L76.5 3.5 Z" fill="#ffffff" filter="url(#sparkleGlow3)"/>
            <circle cx="78" cy="4" r="1.2" fill="#ffffff"/>

            <!-- Auspicious Crown Apex Motif over Center -->
            <path d="M62 13 C64 9, 66 9, 68 13" stroke="url(#crestGold3)" stroke-width="1.2" stroke-linecap="round"/>
            <circle cx="65" cy="8" r="1.5" fill="var(--primary-bright)"/>
        </svg>

        <h1 class="f-moul text-base md:text-lg gold-modern-gradient mb-2">
            {{ $translations['wedding_invitation'] }}
        </h1>

        <div class="w-16 h-0.5 mx-auto bg-[var(--primary)]/60 my-3"></div>

        <p class="text-xs theme-muted-text mb-1">{{ $translations['respectfully_to'] }}</p>
        <h2 id="opener-guest-name" class="f-moul text-lg theme-bright-text mb-3 leading-snug">
            {{ $guest->name ?? $translations['guest_honor_title'] }}
        </h2>

        <p class="text-xs theme-subtext leading-relaxed px-2 mb-4">
            {!! nl2br(e($translations['invite_msg'])) !!}
        </p>

        <div class="bg-[var(--card-bg)] border border-[var(--border-primary)] rounded-2xl py-3 px-2 mb-5 shadow-sm">
            <div class="f-moul text-base theme-bright-text">
                {{ $lang == 'km' ? $event->groom_name : $event->groom_name_en }}
            </div>
            <div class="theme-primary-text text-xs my-0.5">&amp;</div>
            <div class="f-moul text-base theme-bright-text">
                {{ $lang == 'km' ? $event->bride_name : $event->bride_name_en }}
            </div>
        </div>

        @if($event->date ?? false)
        <p class="text-xs theme-muted-text mb-5">
            🗓 {{ \Carbon\Carbon::parse($event->date)->translatedFormat('l, d F Y') }}
        </p>
        @endif

        <!-- Prominent Wedding Opener Trigger (✦ បើកការអញ្ជើញ ✦) -->
        <div class="text-center pt-1" onclick="openWeddingPage(event)">
            <button type="button" class="modern-open-invite-btn" onclick="openWeddingPage(event)" aria-label="{{ $translations['open_invite'] }}">
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

        <div class="relative z-10 w-full max-w-xl mx-auto px-4 py-6 text-center">
            <!-- Royal Celebration Badge (100% Crisp & High Contrast) -->
            <div class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-black/45 backdrop-blur-md border border-white/30 mb-3 shadow-lg">
                <span class="text-xs text-amber-300">✨</span>
                <p class="f-cinzel text-[11px] md:text-xs tracking-[0.25em] text-white font-bold uppercase drop-shadow-sm">
                    {{ $translations['auspicious_blessing'] }}
                </p>
                <span class="text-xs text-amber-300">✨</span>
            </div>

            <!-- Main Title (Khmer Wedding Invitation) with High-Definition Letter Clarity -->
            <h1 class="f-modern-title text-2xl md:text-3xl text-white font-bold mb-3 tracking-wide hero-letter-glow leading-snug">
                {{ $translations['wedding_invitation'] }}
            </h1>

            <div class="w-24 h-0.5 mx-auto mb-4 bg-gradient-to-r from-transparent via-white/80 to-transparent shadow-sm"></div>

            <!-- Couple Names with World-Class Playfair Display / Cinzel Lettering -->
            <div class="my-4 space-y-1.5">
                <div class="f-modern-name text-3xl md:text-5xl text-white font-bold tracking-wide leading-tight hero-letter-glow">
                    {{ $lang == 'km' ? $event->groom_name : $event->groom_name_en }}
                </div>

                <div class="flex items-center justify-center gap-3 my-2">
                    <span class="h-[1px] w-12 bg-gradient-to-r from-transparent to-white/60 shadow-sm"></span>
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-black/40 backdrop-blur-md border border-white/35 text-white text-xs font-serif italic shadow-md">&amp;</span>
                    <span class="h-[1px] w-12 bg-gradient-to-l from-transparent to-white/60 shadow-sm"></span>
                </div>

                <div class="f-modern-name text-3xl md:text-5xl text-white font-bold tracking-wide leading-tight hero-letter-glow">
                    {{ $lang == 'km' ? $event->bride_name : $event->bride_name_en }}
                </div>
            </div>

            @if($event->date ?? false)
            <!-- Date Pill (Ultra-Clear High Contrast) -->
            <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-black/45 backdrop-blur-md border border-white/30 text-white text-xs md:text-sm font-semibold tracking-wide shadow-xl mt-3">
                <span class="text-amber-300">🗓</span>
                <span class="drop-shadow-sm">{{ \Carbon\Carbon::parse($event->date)->translatedFormat('l, d F Y') }}</span>
            </div>

            <!-- 4 Modern Countdown Tiles (Clean Pearl Cards with Sharp Numbers) -->
            <div id="countdown" class="flex justify-center gap-2.5 md:gap-3.5 mt-5">
                @foreach(['days', 'hours', 'minutes', 'seconds'] as $key)
                <div class="countdown-modern">
                    <div id="cd-{{ $key }}" class="f-cinzel text-xl md:text-2xl font-black theme-primary-text leading-none mb-1">00</div>
                    <div class="text-[9.5px] uppercase font-bold text-slate-700 tracking-wider leading-tight">{{ $translations[$key] }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Scroll Down Cue -->
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-10 cursor-pointer flex flex-col items-center gap-1 opacity-90 hover:opacity-100 transition-opacity" onclick="document.getElementById('invitation-section').scrollIntoView({ behavior: 'smooth' })">
            <span class="animate-bounce text-sm text-white drop-shadow leading-none">⌄</span>
            <span class="f-cinzel text-[10px] font-bold uppercase tracking-[0.22em] text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.85)]">{{ $translations['scroll_down'] }}</span>
        </div>
    </section>

    {{-- INVITATION MESSAGE --}}
    <section id="invitation-section" class="py-12 px-4">
        <div class="max-w-2xl mx-auto modern-card p-6 md:p-10 text-center reveal">
            <div class="theme-primary-text text-2xl mb-2">⚜️</div>
            <p class="f-moul text-sm theme-bright-text mb-3">{{ $translations['invite_msg'] }}</p>
            <div class="w-16 h-0.5 mx-auto bg-[var(--primary)]/40 my-4"></div>
            @if($event->love_quote ?? false)
            <p class="text-xs theme-subtext italic font-medium mt-4">« {{ $event->love_quote }} »</p>
            @endif
        </div>
    </section>

    {{-- SCHEDULE --}}
    @if(!empty($event->schedules) && count($event->schedules))
    <section class="py-6 px-4">
        <div class="max-w-2xl mx-auto modern-card p-6 md:p-8 reveal">
            <div class="text-center mb-6">
                <span class="theme-primary-text text-xl">🗓</span>
                <h2 class="f-moul text-base theme-bright-text mt-1">{{ $translations['event_info'] }}</h2>
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
                <div class="timeline-modern-item">
                    <div class="timeline-modern-dot">{{ $i + 1 }}</div>
                    <div class="flex-1 pt-1">
                        <div class="text-xs font-bold theme-primary-text">{{ $timeDisplay }}</div>
                        <div class="f-moul text-xs theme-subtext mt-0.5">
                            {{ $schedule['label_' . $lang] ?? ($schedule['label_kh'] ?? ($schedule['label_km'] ?? '')) }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($event->address)
            <div class="mt-8 pt-6 border-t border-[var(--border-light)] text-center">
                <span class="text-xs font-bold theme-primary-text block mb-1">📍 {{ $translations['location_label'] }}</span>
                <p class="text-xs theme-subtext">{{ $event->address }}</p>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- GOOGLE MAPS --}}
    @if($event->google_map ?? false)
    <section class="py-6 px-4">
        <div class="max-w-2xl mx-auto modern-card p-5 reveal">
            <h3 class="f-moul text-sm theme-bright-text text-center mb-4">{{ $translations['location_label'] }}</h3>
            @if(str_contains($event->google_map, '<iframe'))
            <div class="rounded-xl overflow-hidden border border-[var(--border-primary)] aspect-video w-full">
                {!! $event->google_map !!}
            </div>
            @else
            <div class="text-center py-4">
                <a href="{{ $event->google_map }}" target="_blank" rel="noopener" class="btn-modern-gold inline-flex items-center gap-2 px-6 py-3 rounded-full text-xs">
                    🗺️ {{ $translations['open_maps'] }}
                </a>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- GALLERY --}}
    @if(($event->portfolios ?? false) && count($event->portfolios) > 0)
    <section class="py-8 px-4">
        <div class="max-w-3xl mx-auto">
            <h2 class="f-moul text-base theme-heading text-center mb-6 reveal">✨ {{ $translations['gallery_title'] }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 reveal">
                @foreach($event->portfolios as $i => $photo)
                <a href="{{ asset('storage/' . $photo) }}" data-fancybox="gallery" class="rounded-2xl overflow-hidden border border-[var(--border-primary)] shadow-sm block aspect-square bg-[var(--card-bg)]">
                    <img src="{{ asset('storage/' . $photo) }}" alt="Photo {{ $i + 1 }}" class="w-full h-full object-cover" loading="lazy">
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- RSVP & WISHES --}}
    <section class="py-8 px-4">
        <div class="max-w-lg mx-auto modern-card p-6 md:p-8 text-center reveal">
            <h2 class="f-moul text-sm theme-heading mb-2">{{ $translations['rsvp'] }}</h2>
            <p class="text-xs theme-muted-text mb-5">{{ $translations['rsvp_intro'] }}</p>

            <div class="flex gap-3 justify-center mb-4">
                <button id="btn-yes" onclick="rsvpReply('yes')" class="flex-1 py-3 rounded-xl border border-[var(--primary)] text-[var(--primary-bright)] font-bold text-xs hover:bg-[var(--primary)] hover:text-black transition {{ ($guest && $guest->is_attending === 'yes') ? 'bg-[var(--primary)] text-black' : '' }}">
                    ✓ {{ $translations['attending'] }}
                </button>
                <button id="btn-no" onclick="rsvpReply('no')" class="flex-1 py-3 rounded-xl border border-[var(--border-primary)] theme-muted-text text-xs hover:bg-[var(--primary)]/10 transition {{ ($guest && $guest->is_attending === 'no') ? 'bg-[var(--primary)]/20 theme-bright-text' : '' }}">
                    ✗ {{ $translations['not_attending'] }}
                </button>
            </div>
            <div id="rsvp-msg" class="text-xs p-3 rounded-lg bg-[var(--card-bg)] border border-[var(--border-primary)] theme-bright-text hidden font-medium"></div>

            <div class="mt-8 pt-6 border-t border-[var(--border-light)]">
                <h3 class="f-moul text-xs theme-bright-text mb-3">{{ $translations['send_wishes'] }}</h3>
                <textarea id="wishes-text" rows="3" class="w-full p-3 text-xs border border-[var(--border-primary)] rounded-xl outline-none bg-[var(--bg-color)] theme-subtext" placeholder="{{ $translations['wishes_placeholder'] }}"></textarea>
                <button onclick="sendWishes()" class="btn-modern-gold w-full py-2.5 rounded-xl text-xs mt-2.5">
                    {{ $translations['wishes_btn'] }}
                </button>
                <p id="wishes-sent" class="text-xs text-emerald-400 mt-2 hidden font-semibold">
                    💛 {{ $translations['wishes_success'] }}
                </p>
            </div>
        </div>
    </section>

    {{-- WISHES SLIDER --}}
    @if(isset($wishes) && count($wishes) > 0)
    <section class="py-8 px-4 overflow-hidden">
        <div class="max-w-3xl mx-auto">
            <h2 class="f-moul text-sm theme-heading text-center mb-6 reveal">{{ $translations['guest_comments'] }}</h2>
            <div class="swiper wishes-swiper reveal">
                <div class="swiper-wrapper">
                    @foreach($wishes as $wish)
                    <div class="swiper-slide p-2">
                        <div class="bg-[var(--card-bg)] rounded-2xl p-5 border border-[var(--border-primary)] shadow-sm text-center">
                            <h4 class="f-moul text-xs theme-bright-text mb-1">{{ $wish->name ?? $translations['guest'] }}</h4>
                            <p class="text-xs theme-subtext italic my-2">« {{ $wish->note }} »</p>
                            <small class="text-[10px] theme-muted-text block mt-2">{{ \Carbon\Carbon::parse($wish->created_at)->translatedFormat('d F Y') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- FOOTER --}}
    <footer class="py-8 px-4 text-center border-t border-[var(--border-light)]">
        <p class="f-moul text-sm theme-heading">
            {{ $lang == 'km' ? $event->groom_name : $event->groom_name_en }} &amp; {{ $lang == 'km' ? $event->bride_name : $event->bride_name_en }}
        </p>
    </footer>

    {{-- GIFT MODAL --}}
    @if($guest)
    <div id="gift-modal" role="dialog" aria-modal="true" style="display:none; position:fixed; inset:0; z-index:99998; align-items:center; justify-content:center; padding:16px;">
        <div onclick="closeGiftModal()" style="position:fixed; inset:0; background:rgba(0,0,0,0.75); backdrop-filter:blur(6px);"></div>
        <div class="relative max-w-sm w-full bg-[var(--card-bg)] rounded-2xl border border-[var(--border-primary)] p-6 shadow-2xl z-10 max-h-[90vh] overflow-y-auto theme-subtext">
            <button onclick="closeGiftModal()" class="absolute top-3 right-3 w-7 h-7 rounded-full bg-[var(--primary)]/10 theme-bright-text flex items-center justify-center text-xs font-bold">✕</button>
            <div class="text-center mb-4">
                <h3 class="f-moul text-sm theme-heading">✨ {{ $translations['gift_qr'] }}</h3>
            </div>
            @if($event->qr_code ?? false)
            <div class="text-center mb-4">
                <img src="{{ asset('storage/' . $event->qr_code) }}" alt="QR" class="w-40 h-40 object-contain mx-auto border border-[var(--border-primary)] p-2 rounded-xl bg-white">
                <a href="{{ asset('storage/' . $event->qr_code) }}" download="wedding-qr.png" class="inline-block mt-2 text-[11px] font-bold theme-bright-text border border-[var(--border-primary)] bg-[var(--primary)]/10 px-3 py-1 rounded-full">
                    ⬇ {{ $t('ទាញយក QR Code', 'Download QR Code') }}
                </a>
            </div>
            @endif
            <form id="gift-form" onsubmit="submitDonation(event)" class="space-y-3 text-xs">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-semibold theme-muted-text mb-1">{{ $t('របៀបបង់', 'Payment') }}</label>
                        <select id="gift-payment" class="w-full p-2 border border-[var(--border-primary)] bg-[var(--bg-color)] theme-subtext rounded-lg text-xs">
                            <option value="cash">{{ __('messages.payment_cash') ?? 'Cash' }}</option>
                            <option value="qr_code">{{ __('messages.payment_qr_code') ?? 'QR Code' }}</option>
                            <option value="other">{{ __('messages.payment_other') ?? 'Other' }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold theme-muted-text mb-1">{{ $t('រូបិយប័ណ្ណ', 'Currency') }}</label>
                        <select id="gift-cash-method" class="w-full p-2 border border-[var(--border-primary)] bg-[var(--bg-color)] theme-subtext rounded-lg text-xs">
                            <option value="usd">USD ($)</option>
                            <option value="khr">KHR (៛)</option>
                            <option value="both">{{ __('messages.both') ?? 'Both' }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold theme-muted-text mb-1">{{ __('messages.amount_usd') ?? 'Amount (USD)' }}</label>
                        <input type="number" id="gift-usd" class="w-full p-2 border border-[var(--border-primary)] bg-[var(--bg-color)] theme-subtext rounded-lg text-xs" value="0" min="0">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold theme-muted-text mb-1">{{ __('messages.amount_khr') ?? 'Amount (KHR)' }}</label>
                        <input type="number" id="gift-khr" class="w-full p-2 border border-[var(--border-primary)] bg-[var(--bg-color)] theme-subtext rounded-lg text-xs" value="0" min="0">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold theme-muted-text mb-1">{{ __('messages.note') ?? 'Note' }}</label>
                    <textarea id="gift-note" rows="2" class="w-full p-2 border border-[var(--border-primary)] bg-[var(--bg-color)] theme-subtext rounded-lg text-xs" placeholder="{{ $t('ពាក្យជូនពរ...', 'Note...') }}"></textarea>
                </div>
                <button type="submit" id="gift-submit" class="btn-modern-gold w-full py-2.5 rounded-xl text-xs font-bold">
                    <span id="gift-submit-label">{{ $t('ផ្ញើចំណងដៃ', 'Submit Gift') }}</span>
                </button>
            </form>
            <div id="gift-success-msg" class="hidden text-center py-4 text-xs font-bold theme-bright-text">
                💛 {{ $t('សូមអរគុណសម្រាប់ចំណងដៃរបស់អ្នក!', 'Thank you for your gift!') }}
            </div>
        </div>
    </div>
    @endif

</div>

<script>
// ─── LIVE LUXURY GOLD STARDUST & SPARKLE ENGINE ───
let spawnModernBurst = null;

(function initGoldDustAtmosphere() {
    const canvas = document.getElementById('leaves-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    const goldPalette = [
        '{{ $primaryBright }}',
        '{{ $themeColor }}',
        '{{ $primaryLight }}',
        'rgba({{ $primaryRgb }}, 0.85)',
        'rgba({{ $primaryRgb }}, 0.50)',
        '#FFFFFF',
        '{{ $primaryTint }}'
    ];

    // Drifting gold stardust & diamond sparkles
    const dust = Array.from({ length: 32 }, () => ({
        x: Math.random() * window.innerWidth,
        y: Math.random() * window.innerHeight,
        size: 1.5 + Math.random() * 3.5,
        speedY: 0.35 + Math.random() * 0.85,
        speedX: (Math.random() - 0.5) * 0.45,
        swaySpeed: 0.02 + Math.random() * 0.03,
        swayOffset: Math.random() * Math.PI * 2,
        rotation: Math.random() * 360,
        rotSpeed: (Math.random() - 0.5) * 2,
        color: goldPalette[Math.floor(Math.random() * goldPalette.length)],
        alpha: 0.3 + Math.random() * 0.6,
        isSparkle: Math.random() > 0.5
    }));

    let burstDust = [];
    spawnModernBurst = function(x, y) {
        for (let i = 0; i < 40; i++) {
            const angle = Math.random() * Math.PI * 2;
            const velocity = 2 + Math.random() * 6;
            burstDust.push({
                x: x,
                y: y,
                vx: Math.cos(angle) * velocity,
                vy: Math.sin(angle) * velocity,
                size: 2 + Math.random() * 4,
                alpha: 1,
                decay: 0.015 + Math.random() * 0.02,
                color: goldPalette[Math.floor(Math.random() * goldPalette.length)]
            });
        }
    };

    let step = 0;
    function loop() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        step++;

        dust.forEach(p => {
            p.y += p.speedY;
            p.x += Math.sin(step * p.swaySpeed + p.swayOffset) * 0.5 + p.speedX;
            p.rotation += p.rotSpeed;

            if (p.y > canvas.height + 20) {
                p.y = -20;
                p.x = Math.random() * canvas.width;
            }

            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate((p.rotation * Math.PI) / 180);
            ctx.globalAlpha = p.alpha;
            ctx.fillStyle = p.color;

            if (p.isSparkle) {
                const s = p.size;
                ctx.beginPath();
                ctx.moveTo(0, -s);
                ctx.quadraticCurveTo(0, 0, s, 0);
                ctx.quadraticCurveTo(0, 0, 0, s);
                ctx.quadraticCurveTo(0, 0, -s, 0);
                ctx.quadraticCurveTo(0, 0, 0, -s);
                ctx.fill();
            } else {
                ctx.beginPath();
                ctx.arc(0, 0, p.size, 0, Math.PI * 2);
                ctx.fill();
            }
            ctx.restore();
        });

        // Draw burst particles
        for (let i = burstDust.length - 1; i >= 0; i--) {
            const b = burstDust[i];
            b.x += b.vx;
            b.y += b.vy;
            b.vy += 0.12;
            b.alpha -= b.decay;

            if (b.alpha <= 0) {
                burstDust.splice(i, 1);
                continue;
            }

            ctx.save();
            ctx.globalAlpha = b.alpha;
            ctx.fillStyle = b.color;
            ctx.beginPath();
            ctx.arc(b.x, b.y, b.size, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }

        requestAnimationFrame(loop);
    }
    loop();
})();

window.openWeddingPage = function(event) {
    if (event && event.stopPropagation) event.stopPropagation();
    if (event && event.preventDefault) event.preventDefault();
    const opener = document.getElementById('opener');
    const page   = document.getElementById('wedding-page');
    const seal   = document.querySelector('.modern-seal-btn');

    if (opener && opener.classList.contains('closing')) return;

    if (typeof spawnModernBurst === 'function' && seal) {
        try {
            const rect = seal.getBoundingClientRect();
            spawnModernBurst(rect.left + rect.width / 2, rect.top + rect.height / 2);
        } catch(e) {}
    }

    if (page) {
        page.style.display = 'block';
    }

    if (opener) opener.classList.add('closing');
    try { toggleMusic(); } catch(e) {}

    setTimeout(() => {
        if (opener) opener.style.display = 'none';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        try { initCountdown(); } catch(e) {}
        try { initReveal(); } catch(e) {}
        try { initFancybox(); } catch(e) {}
        try { initSwiper(); } catch(e) {}
    }, 550);
};

function attachOpenerListeners() {
    const seal = document.querySelector('.modern-seal-btn');
    if (seal) {
        seal.addEventListener('click', window.openWeddingPage);
        seal.addEventListener('touchend', function(e) {
            e.preventDefault();
            window.openWeddingPage(e);
        }, { passive: false });
    }
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', attachOpenerListeners);
} else {
    attachOpenerListeners();
}

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
    const btnYes = document.getElementById('btn-yes');
    const btnNo  = document.getElementById('btn-no');
    if (btnYes) {
        btnYes.style.background = status === 'yes' ? 'var(--primary)' : 'transparent';
        btnYes.style.color      = status === 'yes' ? '#000000' : 'var(--primary-bright)';
    }
    if (btnNo) {
        btnNo.style.background = status === 'no' ? 'rgba({{ $primaryRgb }}, 0.2)' : 'transparent';
    }
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
</script>
</body>
</html>
