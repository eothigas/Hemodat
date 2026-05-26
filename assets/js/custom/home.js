// home.js - Busca e exibe alertas de vencimento e estoque baixo no dashboard

(async function () {
    const container = document.getElementById('alertas-container');
    if (!container) return;

    try {
        const [resVenc, resEstq] = await Promise.all([
            fetch(BASE_URL + '/includes/actions/bolsas.php?action=vencimento'),
            fetch(BASE_URL + '/includes/actions/bolsas.php?action=estoque_alerta'),
        ]);

        const vencendo = await resVenc.json();
        const baixo    = await resEstq.json();

        // ── Bolsas vencendo em breve ─────────────────────────────────────────
        if (Array.isArray(vencendo) && vencendo.length > 0) {
            const linhas = vencendo.map(b => {
                const d = new Date(b.data_validade + 'T00:00:00');
                const hoje = new Date();
                hoje.setHours(0,0,0,0);
                const dias = Math.round((d - hoje) / 86400000);
                const dtFmt = d.toLocaleDateString('pt-BR');
                return `<li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                    <span><strong>${b.tipo_sanguineo}</strong> — ${parseFloat(b.quantidade).toFixed(2)} L</span>
                    <span class="badge bg-warning text-dark">Vence em ${dias}d (${dtFmt})</span>
                </li>`;
            }).join('');

            container.insertAdjacentHTML('beforeend', `
                <div class="hemodat-card p-0 overflow-hidden">
                    <div class="d-flex align-items-center gap-2 px-3 py-2"
                         style="background:rgba(255,193,7,0.15); border-bottom:1px solid rgba(255,193,7,0.3);">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                        <strong class="text-warning-emphasis">Bolsas vencendo em até 7 dias</strong>
                    </div>
                    <ul class="list-group list-group-flush">${linhas}</ul>
                </div>
            `);
        }

        // ── Estoque abaixo do mínimo ─────────────────────────────────────────
        if (Array.isArray(baixo) && baixo.length > 0) {
            const linhas = baixo.map(b => `
                <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                    <span><strong>${b.tipo}</strong></span>
                    <span class="badge bg-danger">${b.atual.toFixed(2)} L / mín. ${b.minimo.toFixed(2)} L</span>
                </li>
            `).join('');

            container.insertAdjacentHTML('beforeend', `
                <div class="hemodat-card p-0 overflow-hidden">
                    <div class="d-flex align-items-center gap-2 px-3 py-2"
                         style="background:rgba(209,0,0,0.08); border-bottom:1px solid rgba(209,0,0,0.15);">
                        <i class="bi bi-droplet-half fs-5" style="color:var(--hemo-red);"></i>
                        <strong style="color:var(--hemo-red);">Estoque crítico</strong>
                    </div>
                    <ul class="list-group list-group-flush">${linhas}</ul>
                </div>
            `);
        }

    } catch (e) {
        // Silencioso — alertas são informativos, não bloqueiam o dashboard
        console.warn('Erro ao carregar alertas:', e);
    }
})();
