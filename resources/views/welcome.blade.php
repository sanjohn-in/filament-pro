@php
    $themeParam = request()->query('theme');
    $effectiveId = (int) ($themeParam ?: ($theme->id ?? ($event->default_theme_id ?? 1)));
    $themeName   = strtolower($theme->name ?? '');

    $themeView = match(true) {
        $effectiveId === 3 || str_contains($themeName, 'three') => 'theme-three',
        $effectiveId === 2 || str_contains($themeName, 'two')   => 'theme-two',
        default => 'theme-one',
    };

    $buildThemeUrl = function($targetId) use ($event) {
        $queryParams = request()->query();
        unset($queryParams['theme']);
        $url = url('/events/' . $event->slug . '/template/' . $targetId);
        return count($queryParams) ? $url . '?' . http_build_query($queryParams) : $url;
    };
@endphp

@include($themeView)

{{-- ══════════════════════════════════════════════════════════
     RESPONSIVE FLOATING THEME SWITCHER WITH LIVE ANIMATION
══════════════════════════════════════════════════════════ --}}
<style>
    #theme-preview-dock {
        position: fixed;
        bottom: 14px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 999999;
        display: inline-flex;
        align-items: center;
        padding: 5px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1.5px solid rgba(255, 255, 255, 0.95);
        border-radius: 9999px;
        box-shadow: 
            0 14px 36px -4px rgba(0, 0, 0, 0.15),
            0 2px 8px rgba(0, 0, 0, 0.06),
            0 0 0 1px rgba(255, 255, 255, 0.9) inset;
        font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Text', 'Segoe UI', Roboto, sans-serif;
        user-select: none;
        -webkit-user-select: none;
        max-width: calc(100vw - 20px);
        animation: switcherEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) backwards;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
    }

    @keyframes switcherEntrance {
        0% { transform: translate(-50%, 35px); opacity: 0; }
        100% { transform: translate(-50%, 0); opacity: 1; }
    }

    .theme-dock-links {
        display: flex;
        align-items: center;
        gap: 3px;
        background: rgba(0, 0, 0, 0.04);
        padding: 3px;
        border-radius: 9999px;
        border: 1px solid rgba(0, 0, 0, 0.04);
    }

    .theme-dock-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        font-size: 11.5px;
        font-weight: 600;
        letter-spacing: 0.01em;
        padding: 6px 13px;
        border-radius: 9999px;
        text-decoration: none;
        white-space: nowrap;
        color: #475569;
        background: transparent;
        border: 1px solid transparent;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        line-height: 1;
    }

    .theme-dock-btn:hover {
        color: #0f172a;
        background: rgba(0, 0, 0, 0.05);
    }

    .theme-dock-btn.active-theme-1 {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff !important;
        font-weight: 700;
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 3px 10px rgba(217, 119, 6, 0.35), 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .theme-dock-btn.active-theme-2 {
        background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
        color: #ffffff !important;
        font-weight: 700;
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 3px 10px rgba(219, 39, 119, 0.35), 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .theme-dock-btn.active-theme-3 {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: #ffffff !important;
        font-weight: 700;
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 3px 10px rgba(2, 132, 199, 0.35), 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .theme-dock-toggle {
        background: rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.06);
        color: #64748b;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        margin-left: 5px;
        margin-right: 2px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        flex-shrink: 0;
    }
    .theme-dock-toggle:hover {
        background: rgba(0, 0, 0, 0.10);
        color: #0f172a;
        transform: scale(1.06);
    }

    /* Minimized Floating Trigger */
    #theme-dock-minimized {
        position: fixed;
        bottom: 16px;
        right: 16px;
        z-index: 999999;
        background: rgba(255, 255, 255, 0.90);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1.5px solid rgba(255, 255, 255, 0.95);
        color: #0f172a;
        font-size: 11px;
        font-weight: 700;
        padding: 8px 15px;
        border-radius: 9999px;
        cursor: pointer;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.8) inset;
        display: none;
        align-items: center;
        gap: 6px;
        transition: transform 0.2s, background 0.2s;
    }
    #theme-dock-minimized:hover {
        transform: scale(1.04);
        background: #ffffff;
    }

    /* Small Mobile Optimizations (< 480px) */
    @media (max-width: 480px) {
        #theme-preview-dock {
            bottom: 10px;
            padding: 4px;
        }
        .theme-dock-links {
            gap: 2px;
            padding: 2px;
        }
        .theme-dock-btn {
            font-size: 11px;
            padding: 6px 10px;
            gap: 3.5px;
        }
        .theme-dock-btn span.theme-name-full {
            display: none;
        }
        .theme-dock-toggle {
            width: 26px;
            height: 26px;
            margin-left: 3px;
            margin-right: 1px;
        }
    }
</style>

<aside id="theme-preview-dock" aria-label="Theme Preview Switcher">
    <div class="theme-dock-links">
        <a href="{{ $buildThemeUrl(1) }}"
           class="theme-dock-btn {{ $effectiveId === 1 ? 'active-theme-1' : '' }}"
           title="Theme 1: Royal Gold">
            <span>👑</span>
            <span>Royal<span class="theme-name-full"> Gold</span></span>
        </a>

        <a href="{{ $buildThemeUrl(2) }}"
           class="theme-dock-btn {{ $effectiveId === 2 ? 'active-theme-2' : '' }}"
           title="Theme 2: Lotus Floral">
            <span>🌸</span>
            <span>Lotus<span class="theme-name-full"> Ivory</span></span>
        </a>

        <a href="{{ $buildThemeUrl(3) }}"
           class="theme-dock-btn {{ $effectiveId === 3 ? 'active-theme-3' : '' }}"
           title="Theme 3: Modern Luxury">
            <span>✨</span>
            <span>Modern<span class="theme-name-full"> Fusion</span></span>
        </a>
    </div>

    <button type="button" class="theme-dock-toggle" onclick="toggleThemeDock()" title="Minimize switcher" aria-label="Minimize">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
    </button>
</aside>

<button type="button" id="theme-dock-minimized" onclick="toggleThemeDock()">
    <span>🎨</span>
    <span>Themes</span>
</button>

<script>
    function toggleThemeDock() {
        const dock = document.getElementById('theme-preview-dock');
        const minBtn = document.getElementById('theme-dock-minimized');
        if (!dock || !minBtn) return;

        if (dock.style.display === 'none') {
            dock.style.display = 'flex';
            minBtn.style.display = 'none';
        } else {
            dock.style.display = 'none';
            minBtn.style.display = 'flex';
        }
    }
</script>