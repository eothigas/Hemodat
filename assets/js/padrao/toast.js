/**
 * toast.js - Sistema de notificação não-bloqueante.
 * Uso: showToast('mensagem', 'success' | 'error' | 'info')
 */

(function () {
    // Injeta CSS base caso toast.css não esteja no head
    function ensureContainer() {
        let container = document.getElementById('hd-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'hd-toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    window.showToast = function (message, type = 'info', duration = 3500) {
        const container = ensureContainer();

        const toast = document.createElement('div');
        toast.className = `hd-toast hd-toast-${type}`;

        const icon = { success: '✓', error: '✕', info: 'ℹ' }[type] || 'ℹ';
        toast.innerHTML = `<span class="hd-toast-icon">${icon}</span><span class="hd-toast-msg">${message}</span>`;

        container.appendChild(toast);

        // Força reflow antes de adicionar classe de animação
        void toast.offsetWidth;
        toast.classList.add('hd-toast-show');

        setTimeout(() => {
            toast.classList.remove('hd-toast-show');
            toast.addEventListener('transitionend', () => toast.remove(), { once: true });
        }, duration);
    };
})();
