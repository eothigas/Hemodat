// relatorio.js - Gráfico de estoque por tipo com filtros + exportação PDF

const graficoEl = document.getElementById('grafico-relatorio');
const semDados  = document.getElementById('sem-dados');
let ultimoDado  = null;

function buildUrl() {
    const tipo    = document.getElementById('filtro-tipo').value;
    const dataIni = document.getElementById('filtro-ini').value;
    const dataFim = document.getElementById('filtro-fim').value;
    const params  = new URLSearchParams({ action: 'buscar_total' });
    if (tipo)    params.set('tipo',     tipo);
    if (dataIni) params.set('data_ini', dataIni);
    if (dataFim) params.set('data_fim', dataFim);
    return BASE_URL + '/includes/actions/bolsas.php?' + params.toString();
}

async function carregarGrafico() {
    try {
        const response = await fetch(buildUrl());
        if (!response.ok) throw new Error('Erro de servidor');
        const data = await response.json();

        const tipos = data.tipos_sanguineos ?? [];
        const qtds  = (data.quantidades ?? []).map(Number);

        if (tipos.length === 0) {
            graficoEl.classList.add('d-none');
            semDados.classList.remove('d-none');
            ultimoDado = null;
            return;
        }

        graficoEl.classList.remove('d-none');
        semDados.classList.add('d-none');

        ultimoDado = tipos.map((t, i) => ({ label: t, value: qtds[i] || 0 }));
        graficoEl.innerHTML = HemoCharts.hbar(ultimoDado, { height: 320 });

    } catch (err) {
        console.error('Erro ao carregar dados do relatório:', err);
    }
}

// ── Filtros ──────────────────────────────────────────────────
document.getElementById('filtro-tipo').addEventListener('change', carregarGrafico);
document.getElementById('filtro-ini').addEventListener('change',  carregarGrafico);
document.getElementById('filtro-fim').addEventListener('change',  carregarGrafico);

document.getElementById('limpar-filtros').addEventListener('click', () => {
    document.getElementById('filtro-tipo').value = '';
    document.getElementById('filtro-ini').value  = '';
    document.getElementById('filtro-fim').value  = '';
    carregarGrafico();
});

// ── Exportação PDF ───────────────────────────────────────────
document.getElementById('export').addEventListener('click', () => {
    if (!ultimoDado || !ultimoDado.length) { showToast('Nada para exportar.', 'error'); return; }
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(16);
    doc.text('Relatório de Estoque - HEMODAT', 148, 20, { align: 'center' });
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(11);
    let y = 36;
    ultimoDado.forEach(d => {
        doc.text(`${d.label}: ${d.value.toFixed(2)} L`, 20, y);
        y += 8;
    });
    doc.save('relatorio_hemodat.pdf');
});

// ── Carga inicial ─────────────────────────────────────────────
carregarGrafico();
