/**
 * darkmode.js - tema claro/escuro com persistência em localStorage.
 * Aplica `data-theme="dark"` no <html>.
 * Botão trigger: #btn-tema (topbar).
 */

(function () {
    const KEY    = 'hemodat_theme';
    const html   = document.documentElement;
    const DARK   = 'dark';
    const LIGHT  = 'light';

    /* ── Aplica tema ───────────────────────────────────────── */
    function applyTheme(theme) {
        if (theme === DARK) {
            html.setAttribute('data-theme', DARK);
        } else {
            html.removeAttribute('data-theme');
        }
        localStorage.setItem(KEY, theme);
        syncIcon(theme);
    }

    /* ── Ícone do botão ─────────────────────────────────────── */
    const SVG_SUN  = '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>';
    const SVG_MOON = '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';

    function syncIcon(theme) {
        const btn = document.getElementById('btn-tema');
        if (!btn) return;
        if (theme === DARK) {
            btn.innerHTML = SVG_SUN;
            btn.title = 'Modo claro';
        } else {
            btn.innerHTML = SVG_MOON;
            btn.title = 'Modo escuro';
        }
    }

    /* ── Lê preferência salva ou sistema ───────────────────── */
    function preferredTheme() {
        const saved = localStorage.getItem(KEY);
        if (saved === DARK || saved === LIGHT) return saved;
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? DARK : LIGHT;
    }

    /* ── Init (executado imediatamente - sem flash) ─────────── */
    applyTheme(preferredTheme());

    /* ── Bind toggle após DOM ───────────────────────────────── */
    document.addEventListener('DOMContentLoaded', function () {
        syncIcon(preferredTheme());  // garante ícone correto após render

        const btn = document.getElementById('btn-tema');
        if (!btn) return;

        btn.addEventListener('click', function () {
            const current = html.getAttribute('data-theme') === DARK ? DARK : LIGHT;
            applyTheme(current === DARK ? LIGHT : DARK);
        });
    });

    /* ── Sincroniza entre abas ──────────────────────────────── */
    window.addEventListener('storage', function (e) {
        if (e.key === KEY) applyTheme(e.newValue === DARK ? DARK : LIGHT);
    });

})();
