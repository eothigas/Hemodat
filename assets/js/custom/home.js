// home.js - dashboard: stat cards + gráficos + alertas + movimentações (dados reais)

(async function () {

    const DONUT_COLORS = ['var(--brand)', 'var(--blue-500)', 'var(--green-500)', 'var(--amber-500)',
                           'var(--brand-300)', 'var(--blue-100)', 'var(--green-600)', 'var(--amber-700)'];

    const [resTotal, resVenc, resEstq, resSerie, resMovHoje, resHist] = await Promise.all([
        fetch(BASE_URL + '/includes/actions/bolsas.php?action=buscar_total').catch(() => null),
        fetch(BASE_URL + '/includes/actions/bolsas.php?action=vencimento').catch(() => null),
        fetch(BASE_URL + '/includes/actions/bolsas.php?action=estoque_alerta').catch(() => null),
        fetch(BASE_URL + '/includes/actions/bolsas.php?action=serie_diaria&days=14').catch(() => null),
        fetch(BASE_URL + '/includes/actions/bolsas.php?action=movimentacoes_hoje').catch(() => null),
        fetch(BASE_URL + '/includes/actions/bolsas.php?action=historico').catch(() => null),
    ]);

    const totalRaw = resTotal   ? await resTotal.json().catch(() => ({}))  : {};
    const vencendo = resVenc    ? await resVenc.json().catch(() => [])     : [];
    const baixo    = resEstq    ? await resEstq.json().catch(() => [])     : [];
    const serie    = resSerie   ? await resSerie.json().catch(() => [])    : [];
    const movHoje  = resMovHoje ? await resMovHoje.json().catch(() => ({total:0})) : { total: 0 };
    const hist     = resHist    ? await resHist.json().catch(() => ({rows:[]})) : { rows: [] };

    const tipos = totalRaw.tipos_sanguineos ?? [];
    const qtds  = totalRaw.quantidades      ?? [];
    const total = tipos.map((t, i) => ({ tipo_sanguineo: t, quantidade: parseFloat(qtds[i]) || 0 }));
    const totalLitros = total.reduce((s, d) => s + d.quantidade, 0);

    // ── Stat cards ───────────────────────────────────────────
    const grid = document.getElementById('stat-grid');
    if (grid) {
        const alertasN = (vencendo?.length || 0) + (baixo?.length || 0);
        grid.innerHTML = `
            <div class="card stat">
                <div class="stat-label"><span class="stat-ic brand">${iconSvg('droplet')}</span>ESTOQUE TOTAL</div>
                <div class="stat-value">${totalLitros.toFixed(1)}<span class="stat-unit">L</span></div>
                <div class="stat-meta"><span>Em todos os tipos sanguíneos</span></div>
            </div>
            <div class="card stat">
                <div class="stat-label"><span class="stat-ic green">${iconSvg('check')}</span>TIPOS DISPONÍVEIS</div>
                <div class="stat-value">${total.filter(d => d.quantidade > 0).length}<span class="stat-unit">/ 8</span></div>
                <div class="stat-meta"><span>Tipos com estoque &gt; 0</span></div>
            </div>
            <div class="card stat">
                <div class="stat-label"><span class="stat-ic amber">${iconSvg('clock')}</span>VENCENDO EM BREVE</div>
                <div class="stat-value">${vencendo?.length || 0}</div>
                <div class="stat-meta"><span>Nos próximos 7 dias</span></div>
            </div>
            <div class="card stat">
                <div class="stat-label"><span class="stat-ic blue">${iconSvg('activity')}</span>MOVIMENTAÇÕES HOJE</div>
                <div class="stat-value">${movHoje.total}</div>
                <div class="stat-meta"><span>${alertasN === 0 ? 'Tudo dentro do normal' : alertasN + ' alerta(s) ativo(s)'}</span></div>
            </div>
        `;
    }

    // ── Gráfico entradas x saídas ─────────────────────────────
    const ioChart = document.getElementById('io-chart');
    if (ioChart && Array.isArray(serie) && serie.length) {
        ioChart.innerHTML = HemoCharts.lineChart(serie, ['entradas', 'saidas'], {
            colors: ['var(--brand)', 'var(--blue-600)'], height: 260,
        });
        const totEnt = serie.reduce((s, d) => s + d.entradas, 0);
        const totSai = serie.reduce((s, d) => s + d.saidas, 0);
        document.getElementById('io-legend').innerHTML = HemoCharts.legend([
            { label: 'Entradas', value: totEnt.toFixed(0), color: 'var(--brand)' },
            { label: 'Saídas',   value: totSai.toFixed(0), color: 'var(--blue-600)' },
        ]);
    }

    // ── Gráfico estoque por tipo (barra horizontal) ───────────
    const estqChart = document.getElementById('estoque-chart');
    if (estqChart) {
        document.getElementById('estoque-total-sub').textContent = `${totalLitros.toFixed(1)} L no total`;
        if (total.length) {
            estqChart.innerHTML = HemoCharts.hbar(
                total.map(d => ({ label: d.tipo_sanguineo, value: d.quantidade })),
                { height: 260 }
            );
        } else {
            estqChart.innerHTML = '<p class="small muted">Sem dados de estoque.</p>';
        }
    }

    // ── Donut distribuição por tipo ───────────────────────────
    const donutC = document.getElementById('donut-container');
    if (donutC && total.length) {
        const donutData = total.filter(d => d.quantidade > 0).map((d, i) => ({
            label: d.tipo_sanguineo, value: d.quantidade, color: DONUT_COLORS[i % DONUT_COLORS.length],
        }));
        if (donutData.length) {
            donutC.innerHTML = `
                <div class="row" style="gap:20px; align-items:center;">
                    ${HemoCharts.donut(donutData, {
                        size: 150, thickness: 18,
                        centerHtml: `<span class="tnum" style="font-family:var(--font-display);font-size:20px;font-weight:700;">${totalLitros.toFixed(0)}</span><span class="small muted">litros</span>`,
                    })}
                    <div class="col" style="gap:8px; flex:1;">
                        ${donutData.map(d => `
                            <div class="row between" style="font-size:12.5px;">
                                <span class="row" style="gap:8px;">
                                    <span style="width:8px;height:8px;border-radius:999px;background:${d.color};"></span>
                                    <span>${d.label}</span>
                                </span>
                                <span class="tnum" style="font-weight:600;">${d.value.toFixed(1)} L</span>
                            </div>`).join('')}
                    </div>
                </div>`;
        } else {
            donutC.innerHTML = '<p class="small muted">Sem estoque disponível.</p>';
        }
    }

    // ── Alertas operacionais ──────────────────────────────────
    const container = document.getElementById('alertas-container');
    if (container) {
        let html = '';
        if (Array.isArray(baixo) && baixo.length > 0) {
            html += baixo.map(b => `
                <div class="alert alert-brand">
                    ${iconSvg('alert')}
                    <div>
                        <div style="font-weight:600;">Estoque crítico de ${b.tipo}</div>
                        <div class="small" style="margin-top:2px;opacity:.85;">
                            ${parseFloat(b.atual).toFixed(1)} L — abaixo do mínimo de ${parseFloat(b.minimo).toFixed(1)} L.
                        </div>
                    </div>
                </div>`).join('');
        }
        if (Array.isArray(vencendo) && vencendo.length > 0) {
            html += vencendo.map(b => `
                <div class="alert alert-amber">
                    ${iconSvg('clock')}
                    <div>
                        <div style="font-weight:600;">${b.tipo_sanguineo} vence em breve</div>
                        <div class="small" style="margin-top:2px;opacity:.85;">
                            ${parseFloat(b.quantidade).toFixed(1)} L — validade ${fmtDate(b.data_validade)}.
                        </div>
                    </div>
                </div>`).join('');
        }
        container.innerHTML = html
            ? `<div class="col">${html}</div>`
            : `<div class="row small muted" style="gap:8px;">${iconSvg('check')} Estoque dentro dos parâmetros normais.</div>`;
    }

    // ── Últimas movimentações ─────────────────────────────────
    const movsBody = document.getElementById('movs-body');
    if (movsBody) {
        const rows = (hist.rows || []).slice(0, 8);
        if (!rows.length) {
            movsBody.innerHTML = '<tr><td colspan="5" class="text-center small muted" style="padding:20px;">Nenhuma movimentação registrada.</td></tr>';
        } else {
            movsBody.innerHTML = rows.map(r => `
                <tr>
                    <td>${movBadge(r.operacao)}</td>
                    <td><span class="bt">${r.tipo_sanguineo}</span></td>
                    <td class="num tnum">${parseFloat(r.quantidade).toFixed(2)} L</td>
                    <td class="small muted">${r.responsavel ?? '-'}</td>
                    <td class="num tnum">${fmtDate(r.data_evento)}</td>
                </tr>`).join('');
        }
    }

    // ── Helpers ────────────────────────────────────────────────
    function movBadge(op) {
        if (op === 'Entrada') return '<span class="badge badge-green"><span class="dot"></span>Entrada</span>';
        if (op === 'Saída')   return '<span class="badge badge-blue"><span class="dot"></span>Saída</span>';
        return `<span class="badge badge-gray">${op}</span>`;
    }
    function fmtDate(val) {
        if (!val) return '-';
        const d = new Date(val + (String(val).includes('T') ? '' : 'T00:00:00'));
        return d.toLocaleDateString('pt-BR');
    }
    function iconSvg(name) {
        const paths = {
            droplet:  '<path d="M12 2.5s7 7.5 7 12.5a7 7 0 1 1-14 0c0-5 7-12.5 7-12.5z"/>',
            check:    '<path d="m5 12 5 5L20 7"/>',
            clock:    '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
            activity: '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
            alert:    '<path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><path d="M12 9v4M12 17h.01"/>',
        };
        return `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">${paths[name] || ''}</svg>`;
    }

})();
