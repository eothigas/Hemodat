// relatorio.js - Gráfico de barras com filtros + exportação PDF

let chart = null;
const canvas   = document.getElementById('graficoBar');
const semDados = document.getElementById('sem-dados');

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
            canvas.classList.add('d-none');
            semDados.classList.remove('d-none');
            if (chart) { chart.destroy(); chart = null; }
            return;
        }

        canvas.classList.remove('d-none');
        semDados.classList.add('d-none');

        if (chart) {
            chart.data.labels           = tipos;
            chart.data.datasets[0].data = qtds;
            chart.update();
        } else {
            chart = new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: tipos,
                    datasets: [{
                        label: 'Estoque (litros)',
                        data: qtds,
                        backgroundColor: qtds.map(v =>
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
                            callbacks: { label: ctx => ` ${ctx.parsed.y.toFixed(2)} L` }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 12, weight: '600' } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(226,232,240,.7)' },
                            ticks: {
                                font: { family: 'Inter', size: 11 },
                                callback: v => v + ' L'
                            }
                        }
                    }
                }
            });
        }

    } catch (err) {
        console.error('Erro ao carregar dados do gráfico:', err);
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
    if (!chart) return;
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(16);
    doc.text('Relatório de Estoque - HEMODAT', 148, 20, { align: 'center' });
    setTimeout(() => {
        doc.addImage(canvas.toDataURL('image/png'), 'PNG', 10, 35, 277, 150);
        doc.save('relatorio_hemodat.pdf');
    }, 200);
});

// ── Carga inicial (aguarda Chart.js com defer) ───────────────
(function waitAndLoad() {
    if (typeof Chart !== 'undefined') { carregarGrafico(); return; }
    setTimeout(waitAndLoad, 60);
})();
