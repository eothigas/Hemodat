<?php
/**
 * logos.php - Marca HEMODAT (mark "Pulse Drop" + wordmark), portada do design system.
 */

function logo_mark(int $size = 24, string $color = 'var(--brand)', string $accent = '#fff'): string {
    return sprintf(
        '<svg viewBox="0 0 32 32" width="%1$d" height="%1$d" fill="none" aria-hidden="true">
            <path d="M16 3.5 C20.5 9, 25 14.6, 25 19.5 a9 9 0 1 1 -18 0 C7 14.6, 11.5 9, 16 3.5 z" fill="%2$s"/>
            <path d="M7.5 19 L11 19 L12.5 14.5 L14.5 23 L17 17 L19 21 L21 19 L25 19" stroke="%3$s" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        </svg>',
        $size, htmlspecialchars($color), htmlspecialchars($accent)
    );
}

function wordmark(int $size = 18, string $color = 'var(--text)', string $accent = 'var(--brand)'): string {
    return sprintf(
        '<span class="hemo-wordmark" style="font-size:%1$dpx;color:%2$s;">HEM<span class="hemo-wordmark-o">O<i style="background:%3$s"></i></span>DAT</span>',
        $size, htmlspecialchars($color), htmlspecialchars($accent)
    );
}

function logo_horizontal(int $size = 24, bool $label = true): string {
    $html = '<span class="hemo-logo">' . logo_mark($size);
    if ($label) $html .= wordmark((int) round($size * 0.75));
    return $html . '</span>';
}
