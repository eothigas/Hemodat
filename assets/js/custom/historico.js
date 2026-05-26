// historico.js - Tabela paginada de movimentações de bolsas

let paginaAtual = 1;

function buildUrl(page) {
    const tipo    = document.getElementById('f-tipo').value;
    const operacao = document.getElementById('f-operacao').value;
    const params  = new URLSearchParams({ action: 'historico', page });
    if (tipo)    params.set('tipo',     tipo);
    if (operacao) params.set('operacao', operacao);
    return BASE_URL + '/includes/actions/bolsas.php?' + params.toString();
}

function badgeOperacao(op) {
    if (op === 'Entrada') return '<span class="badge" style="background:rgba(25,135,84,0.15);color:#146c43;">↓ Entrada</span>';
    if (op === 'Saída')   return '<span class="badge" style="background:rgba(209,0,0,0.12);color:rgb(175,0,0);">↑ Saída</span>';
    return `<span class="badge bg-secondary">${op}</span>`;
}

function fmtData(val) {
    if (!val) return '-';
    const d = new Date(val + (val.includes('T') ? '' : 'T00:00:00'));
    return d.toLocaleDateString('pt-BR');
}

async function carregar(page) {
    const tbody = document.getElementById('historico-body');
    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">
        <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>Carregando...</td></tr>`;

    try {
        const res  = await fetch(buildUrl(page));
        const data = await res.json();

        if (!data.rows || data.rows.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhuma movimentação encontrada.</td></tr>`;
            document.getElementById('pag-info').textContent = '';
            document.getElementById('pag-prev').disabled = true;
            document.getElementById('pag-next').disabled = true;
            return;
        }

        tbody.innerHTML = data.rows.map(r => `
            <tr>
                <td>${badgeOperacao(r.operacao)}</td>
                <td><strong>${r.tipo_sanguineo}</strong></td>
                <td>${parseFloat(r.quantidade).toFixed(2)} L</td>
                <td>${fmtData(r.data_evento)}</td>
                <td class="text-muted small">${r.responsavel ?? '-'}</td>
            </tr>
        `).join('');

        paginaAtual = data.page;
        const inicio = (data.page - 1) * 15 + 1;
        const fim    = Math.min(data.page * 15, data.total);
        document.getElementById('pag-info').textContent =
            `Exibindo ${inicio}–${fim} de ${data.total} registros`;

        document.getElementById('pag-prev').disabled = data.page <= 1;
        document.getElementById('pag-next').disabled = data.page >= data.pages;

    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">
            <i class="bi bi-exclamation-triangle me-1"></i>Erro ao carregar histórico.</td></tr>`;
        console.error(e);
    }
}

// ── Eventos ───────────────────────────────────────────────────────────────────

document.getElementById('f-tipo').addEventListener('change',    () => carregar(1));
document.getElementById('f-operacao').addEventListener('change', () => carregar(1));

document.getElementById('limpar-filtros').addEventListener('click', () => {
    document.getElementById('f-tipo').value    = '';
    document.getElementById('f-operacao').value = '';
    carregar(1);
});

document.getElementById('pag-prev').addEventListener('click', () => {
    if (paginaAtual > 1) carregar(paginaAtual - 1);
});
document.getElementById('pag-next').addEventListener('click', () => {
    carregar(paginaAtual + 1);
});

// ── Carga inicial ─────────────────────────────────────────────────────────────

carregar(1);
