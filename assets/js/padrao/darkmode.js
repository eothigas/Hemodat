/**
 * darkmode.js — tema claro/escuro com persistência em localStorage.
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
    function syncIcon(theme) {
        const btn = document.getElementById('btn-tema');
        if (!btn) return;
        const icon = btn.querySelector('i');
        if (!icon) return;
        if (theme === DARK) {
            icon.className = 'bi bi-sun';
            btn.title = 'Modo claro';
        } else {
            icon.className = 'bi bi-moon';
            btn.title = 'Modo escuro';
        }
    }

    /* ── Lê preferência salva ou sistema ───────────────────── */
    function preferredTheme() {
        const saved = localStorage.getItem(KEY);
        if (saved === DARK || saved === LIGHT) return saved;
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? DARK : LIGHT;
    }

    /* ── Init (executado imediatamente — sem flash) ─────────── */
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
