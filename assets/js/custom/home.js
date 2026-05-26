// home.js — dashboard: stat cards + alertas + gráfico estoque

(async function () {

    // ── 1. Fetch em paralelo ─────────────────────────────────
    const [resTotal, resVenc, resEstq] = await Promise.all([
        fetch(BASE_URL + '/includes/actions/bolsas.php?action=buscar_total').catch(() => null),
        fetch(BASE_URL + '/includes/actions/bolsas.php?action=vencimento').catch(() => null),
        fetch(BASE_URL + '/includes/actions/bolsas.php?action=estoque_alerta').catch(() => null),
    ]);

    const totalRaw = resTotal ? await resTotal.json().catch(() => ({})) : {};
    const vencendo = resVenc  ? await resVenc.json().catch(() => [])   : [];
    const baixo    = resEstq  ? await resEstq.json().catch(() => [])   : [];

    // Normalize buscar_total response → [{tipo_sanguineo, quantidade}]
    const tipos = totalRaw.tipos_sanguineos ?? [];
    const qtds  = totalRaw.quantidades      ?? [];
    const total = tipos.map((t, i) => ({ tipo_sanguineo: t, quantidade: qtds[i] ?? 0 }));

    // ── 2. Stat cards ────────────────────────────────────────
    const grid = document.getElementById('stat-grid');
    if (grid) {
        // Totais
        const totalLitros = Array.isArray(total)
            ? total.reduce((acc, d) => acc + (parseFloat(d.quantidade) || 0), 0)
            : 0;
        const totalTipos = Array.isArray(total)
            ? total.filter(d => (parseFloat(d.quantidade) || 0) > 0).length
            : 0;
        const alertasN = (Array.isArray(vencendo) ? vencendo.length : 0)
                       + (Array.isArray(baixo)    ? baixo.length    : 0);

        grid.innerHTML = `
            <div class="stat-card">
                <div class="stat-card-label">
                    <span class="stat-icon stat-icon-red"><i class="bi bi-droplet-fill"></i></span>
                    Estoque total
                </div>
                <div class="stat-card-value">${totalLitros.toFixed(1)}<span class="stat-card-unit">L</span></div>
                <div class="stat-card-sub">Em todos os tipos sanguíneos</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">
                    <span class="stat-icon stat-icon-green"><i class="bi bi-check-circle-fill"></i></span>
                    Tipos disponíveis
                </div>
                <div class="stat-card-value">${totalTipos}<span class="stat-card-unit">/ 8</span></div>
                <div class="stat-card-sub">Tipos com estoque > 0</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">
                    <span class="stat-icon stat-icon-amber"><i class="bi bi-exclamation-triangle-fill"></i></span>
                    Alertas ativos
                </div>
                <div class="stat-card-value">${alertasN}</div>
                <div class="stat-card-sub">${alertasN === 0 ? 'Tudo dentro do normal' : 'Requerem atenção'}</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">
                    <span class="stat-icon stat-icon-blue"><i class="bi bi-calendar-event-fill"></i></span>
                    Vencendo em breve
                </div>
                <div class="stat-card-value">${Array.isArray(vencendo) ? vencendo.length : 0}</div>
                <div class="stat-card-sub">Nos próximos 7 dias</div>
            </div>
        `;
    }

    // ── 3. Alertas ───────────────────────────────────────────
    const container = document.getElementById('alertas-container');
    if (container) {
        container.innerHTML = '';

        if (Array.isArray(vencendo) && vencendo.length > 0) {
            const linhas = vencendo.map(b => {
                const d    = new Date(b.data_validade + 'T00:00:00');
                const hoje = new Date(); hoje.setHours(0,0,0,0);
                const dias = Math.round((d - hoje) / 86400000);
                return `<div class="d-flex justify-content-between align-items-center py-2"
                              style="border-bottom:1px solid rgba(226,232,240,.5); font-size:13px;">
                    <span><strong>${b.tipo_sanguineo}</strong> — ${parseFloat(b.quantidade).toFixed(2)} L</span>
                    <span class="badge bg-warning text-dark">Vence em ${dias}d</span>
                </div>`;
            }).join('');

            container.insertAdjacentHTML('beforeend', `
                <div style="margin-bottom:.75rem;">
                    <div style="font-size:12px;font-weight:700;color:#92400E;
                                display:flex;align-items:center;gap:6px;margin-bottom:.4rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        ${vencendo.length} bolsa${vencendo.length > 1 ? 's' : ''} vencendo em breve
                    </div>
                    ${linhas}
                </div>
            `);
        }

        if (Array.isArray(baixo) && baixo.length > 0) {
            const linhas = baixo.map(b => `
                <div class="d-flex justify-content-between align-items-center py-2"
                     style="border-bottom:1px solid rgba(226,232,240,.5); font-size:13px;">
                    <span><strong>${b.tipo}</strong></span>
                    <span class="badge bg-danger">${parseFloat(b.atual).toFixed(2)} / ${parseFloat(b.minimo).toFixed(2)} L</span>
                </div>
            `).join('');

            container.insertAdjacentHTML('beforeend', `
                <div>
                    <div style="font-size:12px;font-weight:700;color:var(--hemo-red);
                                display:flex;align-items:center;gap:6px;margin-bottom:.4rem;">
                        <i class="bi bi-droplet-half"></i>
                        Estoque crítico — ${baixo.length} tipo${baixo.length > 1 ? 's' : ''}
                    </div>
                    ${linhas}
                </div>
            `);
        }

        if (container.innerHTML.trim() === '') {
            container.innerHTML = `
                <div style="font-size:13px;color:var(--hemo-text-3);
                            display:flex;align-items:center;gap:8px;padding:.5rem 0;">
                    <i class="bi bi-check-circle-fill" style="color:var(--hemo-success);"></i>
                    Estoque dentro dos parâmetros normais.
                </div>
            `;
        }
    }

    // ── 4. Gráfico estoque ───────────────────────────────────
    const canvas = document.getElementById('graficoEstoque');
    if (canvas && Array.isArray(total) && total.length > 0) {
        // Aguarda Chart.js carregar (defer)
        const waitChart = () => new Promise(resolve => {
            if (typeof Chart !== 'undefined') { resolve(); return; }
            const iv = setInterval(() => {
                if (typeof Chart !== 'undefined') { clearInterval(iv); resolve(); }
            }, 50);
        });
        await waitChart();

        const labels = total.map(d => d.tipo_sanguineo);
        const values = total.map(d => parseFloat(d.quantidade) || 0);

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Estoque (L)',
                    data: values,
                    backgroundColor: values.map(v =>
                        v <= 0 ? 'rgba(226,232,240,.6)' :
                        v < 1  ? 'rgba(245,158,11,.8)'  :
                                 'rgba(220,38,38,.8)'
                    ),
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y.toFixed(2)} L`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 12, weight: '600' } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(226,232,240,.6)' },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            callback: v => v + ' L'
                        }
                    }
                }
            }
        });
    }

})();
