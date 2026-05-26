/**
 * security.js — console hardening + aviso de segurança.
 * Carregado em todas as páginas.
 */
(function () {
    // ── Aviso de segurança ───────────────────────────────────
    if (typeof console !== 'undefined') {
        console.log(
            '%c⚠  HEMODAT  —  Sistema Restrito',
            'color:#DC2626;font-size:22px;font-weight:800;font-family:sans-serif;'
        );
        console.log(
            '%cEste console é destinado a desenvolvedores autorizados.\n' +
            'Se alguém pediu para você colar código aqui, é uma tentativa de ataque.\n' +
            'Feche esta aba imediatamente e contate o administrador.',
            'color:#475569;font-size:13px;line-height:1.7;font-family:sans-serif;'
        );
        console.log(
            '%chttps://hemodatgp.com',
            'color:#94A3B8;font-size:11px;font-family:monospace;'
        );
    }

    // ── Desabilita atalhos de inspecionar em produção ────────
    if (window.location.hostname !== 'localhost' &&
        window.location.hostname !== '127.0.0.1') {

        // F12 / Ctrl+Shift+I / Ctrl+U
        document.addEventListener('keydown', function (e) {
            if (
                e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && ['I', 'J', 'C'].includes(e.key)) ||
                (e.ctrlKey && e.key === 'U')
            ) {
                e.preventDefault();
                return false;
            }
        });

        // Botão direito
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });
    }
})();
