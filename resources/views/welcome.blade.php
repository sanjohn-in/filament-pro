<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->name ?? 'Wedding Invitation' }}</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Fancybox --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.css"/>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.umd.js"></script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Tangerine:wght@400;700&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">

    @php
        $themeColor = $event->theme_color ?? '#4548c9';
        $bgColor    = $event->bg_color ?? ('color-mix(in srgb, ' . $themeColor . ' 6%, #fdf6e8)');
    @endphp

    <style>
        /* ─── Dynamic Theme Colors ─── */
        :root {
            --primary        : {{ $themeColor }};
            --secondary      : {{ $bgColor }};
            --dark           : {{ $bgColor }};
            --text           : {{ $themeColor }};
            --primary-dim    : color-mix(in srgb, var(--primary) 35%, transparent);
            --primary-glow   : color-mix(in srgb, var(--primary) 60%, white);
            --primary-light  : color-mix(in srgb, var(--primary) 10%, #fff);
            --secondary-dark : color-mix(in srgb, var(--secondary) 80%, #bba);
        }

        /* ─── Base ─── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Jost', sans-serif;
            background: var(--secondary);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ─── Font Helpers ─── */
        .f-display { font-family: 'Tangerine', cursive; }
        .f-serif   { font-family: 'Cormorant Garamond', serif; }
        .f-heading { font-family: 'Playfair Display', serif; }

        /* ─── Animations ─── */
        @keyframes fadeUp    { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn   { from { opacity: 0; transform: scale(.9);        } to { opacity: 1; transform: scale(1);    } }
        @keyframes float     { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes shimmer   { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }
        @keyframes pulseGlow { 0%,100% { opacity: .4; } 50% { opacity: 1; } }
        @keyframes doorClose { to { opacity: 0; transform: scale(1.08); pointer-events: none; } }
        @keyframes drawLine  { from { width: 0; } to { width: 100%; } }

        .anim-fadeup  { animation: fadeUp  .9s ease both; }
        .anim-scalein { animation: scaleIn .85s cubic-bezier(.22,1,.36,1) both; }
        .anim-float   { animation: float 4s ease-in-out infinite; }

        .d1 { animation-delay: .2s; }
        .d2 { animation-delay: .4s; }
        .d3 { animation-delay: .6s; }
        .d4 { animation-delay: .8s; }
        .d5 { animation-delay: 1s;  }
        .d6 { animation-delay: 1.2s;}

        /* ─── Divider utilities ─── */
        .divider-line {
            height: 2px;
            background: linear-gradient(to right, transparent, var(--primary), transparent);
        }

        .ornament-row {
            display: flex;
            align-items: center;
            gap: 14px;
            color: var(--primary);
        }
        .ornament-row::before,
        .ornament-row::after {
            content: '';
            flex: 1;
            height: 2px;
        }
        .ornament-row::before { background: linear-gradient(to right, transparent, var(--primary)); }
        .ornament-row::after  { background: linear-gradient(to left,  transparent, var(--primary)); }

        /* ════════════════════════════════════════
           OPENER
        ════════════════════════════════════════ */
        #opener {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
        }

        .opener-bg {
            position: absolute;
            inset: 0;
        }

        .bokeh {
            position: absolute;
            border-radius: 50%;
            background: var(--primary);
            filter: blur(60px);
            opacity: .07;
            pointer-events: none;
        }

        .opener-card {
            position: relative;
            max-width: 420px;
            width: 100%;
            background: white;
            border: 1px solid color-mix(in srgb, var(--primary) 50%, transparent);
            padding: 36px;
            text-align: center;
            box-shadow: 0 40px 100px rgba(0,0,0,.7), 0 0 0 1px rgba(255,255,255,.05) inset;
        }

        .opener-card::before {
            content: '';
            position: absolute;
            inset: 12px;
            border: 1px solid var(--primary-dim);
            pointer-events: none;
        }

        .corner {
            position: absolute;
            width: 26px;
            height: 26px;
            border-color: var(--primary);
            border-style: solid;
            opacity: .6;
        }
        .corner.tl { top: 6px; left: 6px;    border-width: 2px 0 0 2px; }
        .corner.tr { top: 6px; right: 6px;   border-width: 2px 2px 0 0; }
        .corner.bl { bottom: 6px; left: 6px; border-width: 0 0 2px 2px; }
        .corner.br { bottom: 6px; right: 6px;border-width: 0 2px 2px 0; }

        .btn-open {
            position: relative;
            display: inline-block;
            padding: 14px 42px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-glow) 50%, var(--primary) 100%);
            background-size: 200%;
            color: #fff;
            font-family: 'Jost', sans-serif;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .28em;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            transition: background-position .5s, transform .3s, box-shadow .3s;
            box-shadow: 0 8px 24px var(--primary-dim);
        }
        .btn-open:hover {
            background-position: right center;
            transform: translateY(-3px);
            box-shadow: 0 16px 40px var(--primary-dim);
        }

        /* ── FIX: Button wrapper ── */
        .btn-open-wrap {
            position: relative;
            display: inline-block;
        }

        /* ── FIX: Expanding pulse rings ── */
        .btn-open-ring {
            position: absolute;
            inset: -4px;
            border-radius: 0.75rem;
            border: 1.5px solid var(--primary);
            opacity: 0;
            pointer-events: none;
            animation: btnRingPulse 2.4s ease-out infinite;
        }
        .btn-open-ring:nth-child(2) { animation-delay: 1.2s; }

        @keyframes btnRingPulse {
            0%   { opacity: .7; transform: scale(1);    }
            100% { opacity: 0;  transform: scale(1.35); }
        }

        /* ── FIX: Shimmer sweep across button face ── */
        .btn-open-shimmer {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                105deg,
                transparent 40%,
                rgba(255,255,255,.35) 50%,
                transparent 60%
            );
            background-size: 200%;
            background-position: 200% center;
            animation: shimmerSweep 3s ease-in-out infinite;
            animation-delay: 2s;
            pointer-events: none;
            border-radius: inherit;
        }

        @keyframes shimmerSweep {
            0%, 60%  { background-position: 200% center;  }
            75%       { background-position: -20% center;  }
            100%      { background-position: 200% center;  }
        }

        /* ── FIX: Missing openBtnFloat keyframe ── */
        @keyframes openBtnFloat {
            0%, 100% { transform: translateY(0);    }
            50%       { transform: translateY(-6px); }
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: var(--primary);
            opacity: .2;
            pointer-events: none;
        }

        #opener.closing { animation: doorClose .8s ease forwards; }

        /* ════════════════════════════════════════
           MAIN PAGE
        ════════════════════════════════════════ */
        #wedding-page { display: none; }

        .hero {
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 100px 24px 140px;
            background-color: var(--dark);
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            overflow: hidden;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                rgba(0,0,0,.65) 0%,
                rgba(0,0,0,.35) 40%,
                rgba(0,0,0,.35) 60%,
                rgba(0,0,0,.75) 100%
            );
            z-index: 1;
            pointer-events: none;
        }

        .hero-glow {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(ellipse at 20% 30%, color-mix(in srgb, var(--primary) 12%, transparent) 0%, transparent 55%),
                radial-gradient(ellipse at 80% 70%, color-mix(in srgb, var(--primary) 8%, transparent) 0%, transparent 50%);
            pointer-events: none;
            z-index: 2;
        }

        .hero-frame {
            position: absolute;
            inset: 28px;
            border: 1px solid var(--primary);
            pointer-events: none;
            z-index: 3;
        }
        .hero-frame::before,
        .hero-frame::after {
            content: '✦';
            position: absolute;
            color: var(--primary);
            font-size: 12px;
            opacity: .5;
        }
        .hero-frame::before { top: -8px; left: 50%; transform: translateX(-50%); }
        .hero-frame::after  { bottom: -8px; left: 50%; transform: translateX(-50%); }

        .cd-box {
            background: rgba(255,255,255,.09);
            border: 1px solid var(--primary-dim);
            padding: 2px 16px 8px;
            min-width: 50px;
            backdrop-filter: blur(6px);
        }

        .scroll-hint {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            z-index: 5;
        }
        .scroll-bar {
            width: 1px;
            height: 56px;
            background: linear-gradient(to bottom, transparent, var(--primary));
            animation: pulseGlow 2s ease-in-out infinite;
        }

        .section-light { background: var(--secondary); }
        .section-dark  { background: var(--dark); }

        .photo-ring {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 2px solid var(--primary);
            padding: 4px;
            background: linear-gradient(135deg, var(--secondary), var(--secondary-dark));
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            cursor: pointer;
            transition: transform .3s, box-shadow .3s;
            box-shadow: 0 0 0 6px var(--primary-dim);
        }
        .photo-ring:hover { transform: scale(1.04); box-shadow: 0 0 0 10px var(--primary-dim); }
        .photo-ring img   { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

        .info-card {
            background: #fff;
            border: 1px solid color-mix(in srgb, var(--primary) 20%, transparent);
            border-top: 3px solid var(--primary);
            padding: 32px;
        }

        .btn-rsvp-yes, .btn-rsvp-no {
            flex: 1;
            padding: 14px;
            font-family: 'Jost', sans-serif;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: .18em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all .3s;
        }
        .btn-rsvp-yes {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        .btn-rsvp-yes:hover, .btn-rsvp-yes.active {
            background: var(--primary);
            color: #fff;
        }
        .btn-rsvp-no {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #9ca3af;
        }
        .btn-rsvp-no:hover, .btn-rsvp-no.active {
            background: #f9fafb;
            border-color: #9ca3af;
            color: #6b7280;
        }

        .wishes-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #e5e7eb;
            font-family: 'Cormorant Garamond', serif;
            font-size: 15px;
            color: #4b5563;
            resize: none;
            outline: none;
            transition: border-color .3s;
            background: #fff;
        }
        .wishes-input:focus { border-color: var(--primary); }

        .btn-primary {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: #fff;
            font-family: 'Jost', sans-serif;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .26em;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            transition: opacity .3s, transform .2s;
            margin-top: 10px;
        }
        .btn-primary:hover { opacity: .88; transform: translateY(-2px); }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }
        .gallery-grid .g-tall { grid-row: span 2; }
        .gallery-grid a {
            display: block;
            overflow: hidden;
            aspect-ratio: 1;
            background: var(--dark);
        }
        .gallery-grid .g-tall { aspect-ratio: auto; }
        .gallery-grid img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .5s ease;
            display: block;
        }
        .gallery-grid a:hover img { transform: scale(1.06); }

        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .7s ease, transform .7s ease;
        }
        .reveal.in { opacity: 1; transform: translateY(0); }

        ::-webkit-scrollbar       { width: 4px; }
        ::-webkit-scrollbar-track { background: var(--secondary); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 99px; }

        @media (max-width: 480px) {
            .opener-card { padding: 36px 22px 36px; }
            .hero-frame  { inset: 14px; }
        }

        /* ════════════════════════════════════════
           HAND POINTER
        ════════════════════════════════════════ */
        .hand-cta {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 22px;
        }

        .hand-tap-zone {
            position: relative;
            display: inline-flex;
            align-items: flex-start;
            justify-content: center;
        }

        .hand-halo {
            position: absolute;
            inset: -14px;
            border-radius: 50%;
            background: radial-gradient(
                circle,
                color-mix(in srgb, var(--primary) 45%, transparent) 0%,
                transparent 68%
            );
            animation: haloBreath 2.5s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes haloBreath {
            0%, 100% { opacity: .2;  transform: scale(.75);  }
            40%       { opacity: .85; transform: scale(1.55); }
        }

        .hand-ripple {
            position: absolute;
            top: 2px;
            left: 50%;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--primary);
            transform: translateX(-50%) scale(0);
            opacity: 0;
            pointer-events: none;
        }
        .hand-ripple.r1 { animation: rippleBurst 2.5s ease-out infinite; }
        .hand-ripple.r2 { animation: rippleBurst 2.5s ease-out infinite; animation-delay: .16s; }

        @keyframes rippleBurst {
            0%,  26%  { transform: translateX(-50%) scale(0);   opacity: 0;  }
            30%        { transform: translateX(-50%) scale(.5);  opacity: .85;}
            70%        { transform: translateX(-50%) scale(3.6); opacity: 0;  }
            100%       { transform: translateX(-50%) scale(3.6); opacity: 0;  }
        }

        .hand-ripple-ring {
            position: absolute;
            top: 2px;
            left: 50%;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1.5px solid var(--primary);
            transform: translateX(-50%) scale(0);
            opacity: 0;
            pointer-events: none;
            animation: rippleRing 2.5s ease-out infinite;
            animation-delay: .32s;
        }

        @keyframes rippleRing {
            0%,  26%  { transform: translateX(-50%) scale(0);   opacity: 0;  }
            30%        { transform: translateX(-50%) scale(.5);  opacity: .6; }
            80%        { transform: translateX(-50%) scale(5);   opacity: 0;  }
            100%       { opacity: 0; }
        }

        .hand-emoji {
            display: block;
            font-size: 2.8rem;
            line-height: 1;
            user-select: none;
            position: relative;
            z-index: 2;
            animation: handTap 2.5s cubic-bezier(.4, 0, .2, 1) infinite;
            filter: drop-shadow(0 6px 18px color-mix(in srgb, var(--primary) 60%, transparent));
        }

        @keyframes handTap {
            0%   { transform: translateY(8px)   rotate(-10deg) scale(1);    opacity: .7;  }
            22%  { transform: translateY(-16px) rotate(-2deg)  scale(1.18); opacity: 1;   }
            34%  { transform: translateY(-4px)  rotate(0deg)   scale(.93);  opacity: 1;   }
            48%  { transform: translateY(-16px) rotate(-2deg)  scale(1.14); opacity: 1;   }
            60%  { transform: translateY(-4px)  rotate(0deg)   scale(.93);  opacity: 1;   }
            75%  { transform: translateY(-10px) rotate(-6deg)  scale(1.06); opacity: .9;  }
            100% { transform: translateY(8px)   rotate(-10deg) scale(1);    opacity: .7;  }
        }

        .hand-label {
            margin-top: 8px;
            font-family: 'Cormorant Garamond', serif;
            font-size: 10px;
            letter-spacing: .24em;
            text-transform: uppercase;
            color: color-mix(in srgb, var(--primary) 65%, transparent);
            animation: labelPulse 2.5s ease-in-out infinite;
        }

        @keyframes labelPulse {
            0%, 100% { opacity: .25; transform: translateY(3px); }
            40%       { opacity: 1;   transform: translateY(0);   }
        }

        .btn-open-enhanced {
            position: relative;
            overflow: hidden;
            border-radius: 0.75rem;
            animation: openBtnFloat 3s ease-in-out infinite;
            animation-delay: 1.6s;
        }

        /* ── Corner sparkles ── */
        .btn-open-sparkle {
            position: absolute;
            color: var(--primary);
            font-size: 9px;
            line-height: 1;
            pointer-events: none;
            animation: openSparkle 2.2s ease-in-out infinite;
        }
        .btn-open-sparkle.s1 { top: -15px;    left:  20%;  animation-delay: 0s;    }
        .btn-open-sparkle.s2 { top: -15px;    right: 20%;  animation-delay: .65s;  }
        .btn-open-sparkle.s3 { bottom: -15px; left:  28%;  animation-delay: 1.1s;  }
        .btn-open-sparkle.s4 { bottom: -15px; right: 28%;  animation-delay: 1.75s; }

        @keyframes openSparkle {
            0%, 100% { opacity: 0;  transform: scale(0)   rotate(0deg);   }
            40%       { opacity: 1;  transform: scale(1.2) rotate(180deg); }
            70%       { opacity: .6; }
        }

        .telegram-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            color: inherit;
            transition: 0.3s;
        }
        .telegram-icon { transition: 0.3s; }
        .telegram-link:hover            { color: #229ED9; }
        .telegram-link:hover .telegram-icon { transform: translateY(-1px) scale(1.1); }

        /* ════════════════════════════════════════
           FALLING LEAVES CANVAS
        ════════════════════════════════════════ */
        #leaves-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 9998; /* below opener (9999), above page */
        }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════════════
     FALLING LEAVES CANVAS  (always visible)
═══════════════════════════════════════════════ --}}
<canvas id="leaves-canvas" aria-hidden="true"></canvas>

{{-- ═══════════════════════════════════════════════
     OPENER
═══════════════════════════════════════════════ --}}
<div id="opener" role="dialog" aria-modal="true" aria-label="Invitation">

    <div class="opener-bg"></div>

    {{-- Bokeh blobs --}}
    <div class="bokeh anim-float"     style="width:320px;height:320px;top:-80px;left:-100px;animation-delay:0s"    aria-hidden="true"></div>
    <div class="bokeh anim-float"     style="width:260px;height:260px;bottom:-60px;right:-80px;animation-delay:1s" aria-hidden="true"></div>
    <div class="bokeh anim-float"     style="width:180px;height:180px;top:50%;left:60%;animation-delay:.5s"        aria-hidden="true"></div>

    {{-- Floating particles --}}
    <div aria-hidden="true" class="absolute inset-0 overflow-hidden pointer-events-none">
        @foreach([
            ['18%','12%','0s'],['32%','85%','.6s'],['68%','20%','1.1s'],
            ['80%','72%','1.7s'],['50%','5%','.3s'],['15%','55%','.9s'],
            ['90%','40%','1.4s'],['40%','90%','.2s']
        ] as $p)
        <div class="particle anim-float"
             style="top:{{ $p[0] }};left:{{ $p[1] }};animation-delay:{{ $p[2] }}"></div>
        @endforeach
    </div>

    {{-- Card --}}
    <div class="opener-card anim-scalein">
        <div class="corner tl" aria-hidden="true"></div>
        <div class="corner tr" aria-hidden="true"></div>
        <div class="corner bl" aria-hidden="true"></div>
        <div class="corner br" aria-hidden="true"></div>

        <div class="f-display anim-fadeup leading-none mb-1" style="font-size:2.5rem;color:var(--primary)">
            ✦
        </div>

        <div class="divider-line mb-6"></div>

        <p class="anim-fadeup d1 text-base tracking-widest uppercase mb-5 f-serif" style="color:var(--primary)">
            — សិរីមង្គលអាពាហ៏ពិពាហ៍ —
        </p>

        <p class="anim-fadeup d2 f-serif text-base italic mb-1" style="color:#9a8070">
            ដោយការគោរពនិងជូនចំពោះ
        </p>

        <h2 class="f-display anim-fadeup d2 mb-1 leading-tight py-2" style="font-size:2.2rem;color:var(--primary)">
            {{ $guest->name ?? '—' }}
        </h2>

        <div class="divider-line mb-4"></div>

        <p class="anim-fadeup d3 f-serif leading-relaxed" style="color:#7a6555">
           យើងខ្ញុំសូមអញ្ជើញលោកអ្នក<br> មកចូលរួមពិធីមង្គលការរបស់យើង។
        </p>

        {{-- FIX: w-100 → w-full --}}
        <div class="anim-fadeup d3 flex gap-3 w-full items-center justify-center">
            <h1 class="f-display text-xl leading-none">
                {{ $event->groom_name ?? 'Hun Chan Malyly' }}
            </h1>
            <span class="f-display" style="font-size:2.2rem;color:var(--primary)">&amp;</span>
            <h1 class="f-display leading-none text-xl">
                {{ $event->bride_name ?? 'Thou San' }}
            </h1>
        </div>

        <div class="divider-line my-4"></div>

        @if($event->date ?? false)
            <p class="anim-fadeup d4 f-serif text-sm tracking-wider" style="color:#9a8070">
                {{ \Carbon\Carbon::parse($event->date)->translatedFormat('l, d F Y') }}
            </p>
        @endif

        <div class="mt-8 anim-fadeup d5 text-center">
            <div class="btn-open-wrap">
                {{-- Expanding pulse rings --}}
                <span class="btn-open-ring" aria-hidden="true"></span>
                <span class="btn-open-ring" aria-hidden="true"></span>

                {{-- Corner sparkles --}}
                <span class="btn-open-sparkle s1" aria-hidden="true">✦</span>
                <span class="btn-open-sparkle s2" aria-hidden="true">✦</span>
                <span class="btn-open-sparkle s3" aria-hidden="true">✦</span>
                <span class="btn-open-sparkle s4" aria-hidden="true">✦</span>

                <button class="btn-open btn-open-enhanced" onclick="openWeddingPage()">
                    <span class="btn-open-shimmer" aria-hidden="true"></span>
                    ✦ &nbsp; បើកការអញ្ជើញ &nbsp; ✦
                </button>
            </div>

            {{-- Hand pointer CTA --}}
            <div class="hand-cta anim-fadeup d6" aria-hidden="true">
                <div class="hand-tap-zone">
                    <div class="hand-halo"></div>
                    <div class="hand-ripple r1"></div>
                    <div class="hand-ripple r2"></div>
                    <div class="hand-ripple-ring"></div>
                    <span class="hand-emoji">👆</span>
                </div>
                <span class="hand-label">ចុចដើម្បីបើក</span>
            </div>
        </div>

        <div class="divider-line mt-8 mb-3"></div>
        <p class="anim-fadeup d6 f-serif text-xs tracking-widest" style="color:#c0b0a0">
            {{ $event->name ?? 'Our Wedding' }}
        </p>
    </div>
</div>


{{-- ═══════════════════════════════════════════════
     MAIN WEDDING PAGE
═══════════════════════════════════════════════ --}}
<div id="wedding-page">
    @if($event->music->value ?? false)
        <audio id="bg-music" loop preload="auto">
            <source src="{{ asset('storage/' . ($event->music->value ?? '')) }}" type="audio/mpeg">
        </audio>
    @endif

    <button id="music-btn" onclick="toggleMusic()" style="
        position: fixed; bottom: 20px; right: 20px; z-index: 9998;
        width: 44px; height: 44px;
        border-radius: 50%;
        background: var(--primary);
        border: 2px solid var(--primary-dim);
        color: #fff; font-size: 18px;
        cursor: pointer;
        box-shadow: 0 4px 16px var(--primary-dim);
        transition: transform .3s;
    ">🎵</button>

    {{-- ░░░ HERO ░░░ --}}
    <section class="hero"
        @if($event->cover_image)
        style="
            background-image: url('{{ asset('storage/' . $event->cover_image) }}');
            background-position: 50% 20%;
            background-size: cover;
            background-repeat: no-repeat;
        "
        @endif
    >
        <div class="hero-overlay" aria-hidden="true"></div>
        <div class="hero-glow"    aria-hidden="true"></div>
        <div class="hero-frame"   aria-hidden="true"></div>

        <div class="relative z-10 w-full max-w-2xl mx-auto px-4 py-8 rounded-2xl"
             style="background: radial-gradient(circle, rgba(0,0,0,0.3) 0%, transparent 80%);">
            <p class="f-serif tracking-widest uppercase font-semibold anim-fadeup mb-5 text-xl"
               style="color:#fff; text-shadow: 0 2px 10px rgba(0,0,0,0.8);">
               សិរីមង្គលអាពាហ៍ពិពាហ៍
            </p>

            <div class="ornament-row mb-5 text-xl anim-fadeup d1" aria-hidden="true">✦</div>

            <h1 class="f-display text-white anim-fadeup d2 leading-none"
                style="font-size:3.5rem; text-shadow: 0 4px 15px rgba(0,0,0,0.8), 0 2px 4px rgba(0,0,0,0.5);">
                {{ $event->groom_name ?? 'Groom Name' }}
            </h1>
            <p class="f-display anim-fadeup d3 my-2"
               style="font-size:3rem; color:var(--primary); text-shadow: 0 0 15px rgba(255,255,255,0.4), 0 2px 4px rgba(0,0,0,0.5);">&amp;</p>
            <h1 class="f-display text-white anim-fadeup d4 leading-none"
                style="font-size:3.5rem; text-shadow: 0 4px 15px rgba(0,0,0,0.8), 0 2px 4px rgba(0,0,0,0.5);">
                {{ $event->bride_name ?? 'Bride Name' }}
            </h1>

            <div class="ornament-row my-8 text-xl anim-fadeup d5" aria-hidden="true">✦</div>

            @if($event->date ?? false)
                <p class="f-heading anim-fadeup d5 text-lg font-normal tracking-wider"
                   style="color:#fff; text-shadow: 0 2px 8px rgba(0,0,0,0.9);">
                    {{ \Carbon\Carbon::parse($event->date)->translatedFormat('l, d F Y') }}
                </p>
            @endif

            {{-- Countdown --}}
            @if($event->date ?? false)
            <div id="countdown" class="flex justify-center gap-3 mt-10 anim-fadeup d6">
                @foreach(['days' => 'ថ្ងៃ', 'hours' => 'ម៉ោង', 'minutes' => 'នាទី', 'seconds' => 'វិនាទី'] as $key => $label)
                <div class="cd-box shadow-xl">
                    <div id="cd-{{ $key }}" class="f-heading text-2xl font-semibold" style="color:var(--primary)">00</div>
                    <div class="text-xs uppercase tracking-wider mt-1" style="color:rgba(255,255,255,.5)">{{ $label }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="scroll-hint" aria-hidden="true">
            <div class="scroll-bar"></div>
            <p class="f-serif text-xs tracking-widest uppercase"
               style="color:color-mix(in srgb, var(--primary) 40%, transparent)">Scroll</p>
        </div>
    </section>


    {{-- ░░░ COUPLE ░░░ --}}
    <section class="section-light pt-10 pb-6 px-4">
        <div class="max-w-3xl mx-auto text-center">

            <p class="f-serif font-bold text-xl tracking-widest uppercase reveal" style="color:var(--primary)">
                យើងខ្ញុំមានកិត្តិយសសូមគោរពអញ្ជើញ
            </p>
            <h2 class="f-display text-gray-600 reveal mt-2">
                ឯកឧត្តម លោកឧកញ៉ា លោកជំទាវ លោក លោកស្រី អ្នកនាងកញ្ញា អញ្ជើញចូលរួមជាអធិបតី និងជាភ្ញៀវកិត្តិយស ដើម្បីប្រសិទ្ធិពរជ័យសិរីសួស្តី ជ័យមង្គល ក្នុងពិធីអាពាហ៍ពិពាហ៍ របស់យើងខ្ញុំទាំងពីរ។
            </h2>
            <div class="ornament-row my-4 text-sm reveal" aria-hidden="true">✦ ✦ ✦</div>

            @if($event->love_quote ?? false)
            <div class="max-w-lg mx-auto mt-16 reveal">
                <div class="divider-line mb-6"></div>
                <p class="f-serif text-xl leading-relaxed italic" style="color:#5a4535">
                    "{{ $event->love_quote }}"
                </p>
                <div class="divider-line mt-6"></div>
            </div>
            @endif
        </div>
    </section>


    {{-- ░░░ GALLERY ░░░ --}}
    @if(($event->portfolios ?? false) && count($event->portfolios) > 0)
    <section class="section-light pb-5 px-4">
        <div class="max-w-2xl mx-auto">
            <p class="f-serif text-lg tracking-widest uppercase text-center mb-3 reveal" style="color:var(--primary)">
                កំរងរូបភាពអាពាហ៍ពិពាហ៍របស់យើង
            </p>
            <div class="gallery-grid reveal">
                @foreach($event->portfolios as $i => $photo)
                <a href="{{ asset('storage/' . $photo) }}"
                   data-fancybox="gallery"
                   data-caption="Photo {{ $i + 1 }}"
                   class="{{ $i === 0 ? 'g-tall' : '' }}">
                    <img src="{{ asset('storage/' . $photo) }}" alt="Gallery photo {{ $i + 1 }}" loading="lazy">
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif


    {{-- ░░░ EVENT DETAILS ░░░ --}}
    <section class="section-dark py-5 px-4">
        <div class="max-w-3xl mx-auto text-center">

            <h2 class="f-display reveal mt-2" style="font-size:2.5rem">
                ព័ត៌មានអំពីកម្មវិធី
            </h2>

            <div class="ornament-row my-4 text-sm reveal" style="color:var(--primary)" aria-hidden="true">
                ✦ ✦ ✦
            </div>

            @if(!empty($event->schedules) && count($event->schedules))
            <div class="info-card reveal">

                <p class="f-serif text-center text-base font-semibold mb-6"
                   style="color:var(--primary);letter-spacing:.04em">
                    {{ \Carbon\Carbon::parse($event->date)->translatedFormat('l, d F Y') }}
                </p>

                <div class="divider-line mb-6"></div>

                <div class="space-y-5">
                    @foreach($event->schedules as $i => $schedule)

                    @php
                        $rawTime = $schedule['time'] ?? null;
                        $hour    = $rawTime ? (int) explode(':', $rawTime)[0] : null;
                        $isPM    = $hour !== null && $hour >= 12;

                        if ($rawTime) {
                            $h12         = $hour === 0 ? 12 : ($hour > 12 ? $hour - 12 : $hour);
                            $min         = explode(':', $rawTime)[1] ?? '00';
                            $period      = $isPM ? 'រសៀល' : 'ព្រឹក';
                            $timeDisplay = 'ម៉ោង ' . $h12 . ':' . $min . ' ' . $period;
                        } else {
                            $timeDisplay = '-';
                        }
                    @endphp

                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded flex items-center justify-center text-sm font-semibold"
                             style="background:color-mix(in srgb, var(--primary) 12%, transparent);color:var(--primary)">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 text-start">
                            <p class="f-serif text-lg font-semibold" style="color:var(--text)">
                                {{ $timeDisplay }}
                            </p>
                            <p class="f-serif text-base mt-0.5" style="color:#9a8070">
                                {{ $schedule['label'] ?? '' }}
                            </p>
                        </div>
                    </div>

                    @if(!$loop->last)
                        <div class="divider-line"></div>
                    @endif

                    @endforeach
                </div>
            </div>
            @endif

            @if($event->google_map ?? false)
            <div class="mt-10 reveal">
                <a href="{{ $event->google_map }}" target="_blank" rel="noopener"
                   class="f-serif inline-flex items-center gap-3 px-10 py-3 text-sm tracking-wider transition-all"
                   style="border:1px solid var(--primary);color:var(--primary)"
                   onmouseover="this.style.background='var(--primary)';this.style.color='#fff'"
                   onmouseout="this.style.background='transparent';this.style.color='var(--primary)'">
                    📍 &nbsp; បើក Google Maps
                </a>
            </div>
            @endif
        </div>
    </section>


    {{-- ░░░ QR CODE ░░░ --}}
    @if($event->qr_code ?? false)
    <section class="section-light py-6 px-4">
        <div class="max-w-sm mx-auto text-center reveal">
            <p class="f-serif text-base tracking-widest uppercase mb-3" style="color:var(--primary)">
                ចងដៃ​ Qr Code
            </p>
            <img src="{{ asset('storage/' . $event->qr_code) }}" alt="QR Code"
                 class="mx-auto"
                 style="max-width:200px;border:1px solid var(--primary-dim);padding:12px;background:#fff">
        </div>
    </section>
    @endif


    {{-- ░░░ RSVP ░░░ --}}
    <section class="section-light pb-6 px-4">
        <div class="max-w-lg mx-auto text-center">

            <p class="f-serif text-base tracking-widest uppercase reveal" style="color:var(--primary)">
                បញ្ជាក់ការចូលរួម
            </p>
            {{-- FIX: fw-bold → font-bold --}}
            <h2 class="f-display font-bold reveal mt-2" style="font-size:2.5rem;color:#2a1200">RSVP</h2>

            <div class="ornament-row my-4 text-lg reveal" aria-hidden="true">✦</div>

            <p class="f-serif reveal text-base mb-1" style="color:#7a6555">
                ជូនចំពោះ <strong style="color:var(--primary)">{{ $guest->name ?? 'ភ្ញៀវកិត្តិយស' }}</strong>,
            </p>
            <p class="f-serif reveal mb-8 text-base italic" style="color:#9a8a7a">
                វត្តមានរបស់អ្នកជាសុភមង្គលដ៏ធំបំផុតសម្រាប់យើង។
            </p>

            <div class="flex gap-3 mb-4 reveal">
                <button id="btn-yes" class="btn-rsvp-yes" onclick="rsvpReply('yes')">
                    ✓ &nbsp; ចូលរួម
                </button>
                <button id="btn-no" class="btn-rsvp-no" onclick="rsvpReply('no')">
                    ✗ &nbsp; មិនចូលរួម
                </button>
            </div>

            <div id="rsvp-msg" class="f-serif text-sm p-4 mb-6 hidden italic"
                 style="background:var(--primary-light);border:1px solid var(--primary-dim);color:#7a5a30">
            </div>

            <div class="reveal mt-8">
                <div class="ornament-row mb-6 text-sm" aria-hidden="true">✦</div>
                <h3 class="f-heading mb-4 font-normal" style="color:#2a1200;font-size:1.4rem">
                    ផ្ញើពាក្យអបអរសាទរ
                </h3>

                <textarea id="wishes-text" class="wishes-input" rows="4"
                          placeholder="សូមសរសេរពាក្យអបអរសាទរ និងពរជ័យសម្រាប់កូនក្រមុំ និងកូនកម្រា…"></textarea>

                <button class="btn-primary" onclick="sendWishes()">
                    ✦ &nbsp; ផ្ញើពាក្យអបអរ &nbsp; ✦
                </button>

                <p id="wishes-sent" class="f-serif text-sm mt-4 hidden" style="color:var(--primary)">
                    💛 សូមអរគុណសម្រាប់ពាក្យអបអរសាទររបស់អ្នក!
                </p>
            </div>
        </div>
    </section>


    {{-- ░░░ FOOTER ░░░ --}}
    <footer class="section-dark py-6 px-4 text-center">
        <div class="max-w-md mx-auto">
            <p class="f-display text-base" style="color:var(--primary)">
                {{ $event->groom_name ?? 'Groom' }} &amp; {{ $event->bride_name ?? 'Bride' }}
            </p>
            @if($event->date ?? false)
            <p class="f-serif mt-2 mb-10 text-sm italic"
               style="color:color-mix(in srgb, var(--primary) 40%, #aaa)">
                {{ \Carbon\Carbon::parse($event->date)->translatedFormat('d F Y') }}
            </p>
            @endif
            <div class="divider-line mb-6"></div>
            {{-- FIX: align-items-center → items-center, removed invalid "primary" class --}}
            <p class="f-serif text-xs flex items-center justify-center gap-1"
               style="color:color-mix(in srgb, var(--primary) 30%, #888)">
                Contact me ♥ ·
                <a href="https://t.me/san_john" target="_blank" class="telegram-link" style="color:var(--primary)">
                    <svg class="telegram-icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                        <path d="M9.993 15.674l-.396 4.227c.567 0 .813-.244 1.107-.537l2.658-2.545 5.51 4.03c1.01.557 1.73.264 1.99-.934l3.606-16.92.001-.001c.308-1.438-.52-2.003-1.504-1.64L1.31 9.22c-1.38.54-1.36 1.31-.234 1.656l5.62 1.755 13.055-8.23c.614-.38 1.172-.17.712.21"/>
                    </svg>
                    San John
                </a>
            </p>
        </div>
    </footer>

</div>{{-- end #wedding-page --}}


<script>
/* ══════════════════════════════════════════════════
   FALLING LEAVES  — pure canvas, no library needed
══════════════════════════════════════════════════ */
(function initFallingLeaves() {
    const canvas = document.getElementById('leaves-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    /* Resize canvas to fill viewport */
    function resize() {
        canvas.width  = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    /* Wedding Petal Palette — Dynamically generated from theme color */
    const PALETTE = [
        '{{ $themeColor }}',
        'color-mix(in srgb, {{ $themeColor }} 80%, white)',
        'color-mix(in srgb, {{ $themeColor }} 60%, white)',
        'color-mix(in srgb, {{ $themeColor }} 40%, white)',
        'color-mix(in srgb, {{ $themeColor }} 20%, white)',
        'color-mix(in srgb, {{ $themeColor }} 10%, white)',
        '#ffffff',
    ];

    const NUM_LEAVES = 28;

    class Leaf {
        constructor(fresh) { this.spawn(fresh); }

        spawn(fresh = true) {
            this.x      = Math.random() * (canvas.width + 160) - 80;
            this.y      = fresh ? -(30 + Math.random() * 120) : Math.random() * canvas.height;
            this.size   = 8 + Math.random() * 2;          // petal size
            this.vy     = 0.5 + Math.random() * 1.2;       // slower fall speed
            this.vx     = (Math.random() - 0.5) * 0.6;     // gentle horizontal drift
            this.angle  = Math.random() * Math.PI * 2;     // current rotation
            this.spin   = (Math.random() - 0.5) * 0.035;   // rotation speed
            this.swing  = Math.random() * Math.PI * 2;     // sine-wave phase
            this.swingR = 0.010 + Math.random() * 0.018;  // sine-wave rate
            this.swingA = 18   + Math.random() * 30;       // sine-wave amplitude px
            this.alpha  = 0.45 + Math.random() * 0.45;
            this.color  = PALETTE[Math.floor(Math.random() * PALETTE.length)];
            /* Each leaf has a slight tilt in the vein direction for variety */
            this.veinAngle = (Math.random() - 0.5) * 0.5;
        }

        update() {
            this.swing += this.swingR;
            this.x     += this.vx + Math.sin(this.swing) * 0.5;
            this.y     += this.vy + Math.abs(Math.sin(this.swing)) * 0.2; // slight speed pulse
            this.angle += this.spin;

            /* Respawn when fully off-screen or out of bounds */
            if (this.y > canvas.height + 80 ||
                this.x < -140 ||
                this.x > canvas.width + 140) {
                this.spawn(true);
                this.x = Math.random() * canvas.width;
            }
        }

        draw() {
            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate(this.angle * 0.5);
            // Simulate a 3D fluttering effect by scaling the height based on rotation
            ctx.scale(1, Math.cos(this.angle * 0.8));
            ctx.globalAlpha = this.alpha;

            const s  = this.size;

            /* ── Petal silhouette (organic rose petal shape) ── */
            ctx.beginPath();
            ctx.moveTo(0, s * 0.4);
            ctx.bezierCurveTo(-s * 1.4, s * 0.1, -s * 1.1, -s * 1.2, 0, -s);
            ctx.bezierCurveTo(s * 1.1, -s * 1.2, s * 1.4, s * 0.1, 0, s * 0.4);
            ctx.fillStyle = this.color;
            ctx.fill();

            /* ── Subtle highlight ── */
            ctx.beginPath();
            ctx.moveTo(-s * 0.4, -s * 0.3);
            ctx.quadraticCurveTo(0, -s * 0.6, s * 0.4, -s * 0.3);
            ctx.strokeStyle = 'rgba(255,255,255,0.4)';
            ctx.lineWidth   = 0.8;
            ctx.stroke();

            ctx.restore();
        }
    }

    /* Stagger initial positions across the full screen height */
    const leaves = Array.from({ length: NUM_LEAVES }, () => new Leaf(false));

    /* Throttle to ~60 fps via rAF */
    function loop() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        leaves.forEach(l => { l.update(); l.draw(); });
        requestAnimationFrame(loop);
    }

    loop();
})();


/* ─── Open the invitation page ─── */
function openWeddingPage() {
    const opener = document.getElementById('opener');
    const page   = document.getElementById('wedding-page');

    opener.classList.add('closing');
    toggleMusic();

    setTimeout(() => {
        opener.style.display = 'none';
        page.style.display   = 'block';
        window.scrollTo({ top: 0 });
        initCountdown();
        initReveal();
        initFancybox();
    }, 800);
}

/* ─── Countdown ─── */
@if($event->date ?? false)
const EVENT_DATE = "{{ $event->date }}";
const EVENT_TIME = "{{ $event->time ?? '00:00' }}";
const EVENT_TS   = new Date(EVENT_DATE + "T" + EVENT_TIME + ":00").getTime();
@else
const EVENT_TS = null;
@endif

function initCountdown() {
    if (!EVENT_TS) return;
    const pad = n => String(n).padStart(2, '0');

    function tick() {
        const diff = EVENT_TS - Date.now();
        const ids  = ['days','hours','minutes','seconds'];

        if (diff <= 0) {
            ids.forEach(k => {
                const el = document.getElementById('cd-' + k);
                if (el) el.textContent = '00';
            });
            return;
        }

        const d = document.getElementById('cd-days');
        const h = document.getElementById('cd-hours');
        const m = document.getElementById('cd-minutes');
        const s = document.getElementById('cd-seconds');

        if (d) d.textContent = pad(Math.floor(diff / 86400000));
        if (h) h.textContent = pad(Math.floor((diff % 86400000) / 3600000));
        if (m) m.textContent = pad(Math.floor((diff % 3600000)  / 60000));
        if (s) s.textContent = pad(Math.floor((diff % 60000)    / 1000));
    }

    tick();
    setInterval(tick, 1000);
}

/* ─── Scroll Reveal ─── */
function initReveal() {
    const io = new IntersectionObserver(
        entries => entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
        }),
        { threshold: 0.1, rootMargin: '0px 0px -36px 0px' }
    );
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
}

/* ─── Fancybox ─── */
function initFancybox() {
    if (typeof Fancybox === 'undefined') return;
    Fancybox.bind('[data-fancybox]', {
        Toolbar  : { display: { left: [], middle: [], right: ['close'] } },
        Images   : { zoom: true },
        Carousel : { infinite: true },
    });
}

/* ─── RSVP ─── */
function rsvpReply(status) {
    document.getElementById('btn-yes').classList.toggle('active', status === 'yes');
    document.getElementById('btn-no').classList.toggle('active',  status === 'no');

    const msg = document.getElementById('rsvp-msg');
    msg.textContent = status === 'yes'
        ? '🎉 អរគុណ! យើងពិតជារីករាយណាស់ដែលអ្នកអាចចូលរួមជាមួយយើងបាន។'
        : '💛 យើងយល់ហើយ។ សូមអរគុណសម្រាប់ការប្រាប់យើងឱ្យដឹង។';
    msg.classList.remove('hidden');
    msg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/* ─── Wishes ─── */
function sendWishes() {
    const text = document.getElementById('wishes-text').value.trim();
    if (!text) return;
    document.getElementById('wishes-sent').classList.remove('hidden');
    document.getElementById('wishes-text').value = '';
}

/* ─── Music ─── */
function toggleMusic() {
    const music = document.getElementById('bg-music');
    const btn   = document.getElementById('music-btn');
    if (!music) return;
    if (music.paused) {
        music.play().catch(() => {});
        if (btn) btn.textContent = '🎵';
    } else {
        music.pause();
        if (btn) btn.textContent = '🔇';
    }
}
</script>

</body>
</html>