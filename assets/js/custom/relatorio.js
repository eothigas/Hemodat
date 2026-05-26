// relatorio.js - Gráfico de barras (Chart.js) com filtros + exportação PDF

const TICK_COLOR  = '#444';
const BAR_COLOR   = 'rgba(209, 0, 0, 0.80)';
const BAR_BORDER  = 'rgba(175, 0, 0, 1)';
const GRID_COLOR  = 'rgba(0,0,0,0.08)';

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
        const qtds  = data.quantidades      ?? [];

        if (tipos.length === 0) {
            canvas.classList.add('d-none');
            semDados.classList.remove('d-none');
            if (chart) { chart.destroy(); chart = null; }
            return;
        }

        canvas.classList.remove('d-none');
        semDados.classList.add('d-none');

        if (chart) {
            chart.data.labels                  = tipos;
            chart.data.datasets[0].data        = qtds;
            chart.update();
        } else {
            const ctx = canvas.getContext('2d');
            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: tipos,
                    datasets: [{
                        label: 'Bolsas disponíveis (litros)',
                        data: qtds,
                        backgroundColor: BAR_COLOR,
                        borderColor:     BAR_BORDER,
                        borderWidth: 1.5,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: { color: TICK_COLOR, font: { size: 14, family: 'Outfit' } }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: TICK_COLOR, font: { size: 14, family: 'Outfit' } },
                            grid:  { color: GRID_COLOR }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: TICK_COLOR, font: { size: 14, family: 'Outfit' } },
                            grid:  { color: GRID_COLOR }
                        }
                    }
                }
            });
        }

    } catch (err) {
        console.error('Erro ao carregar dados do gráfico:', err);
    }
}

// ── Filtros ──────────────────────────────────────────────────────────────────

document.getElementById('filtro-tipo').addEventListener('change', carregarGrafico);
document.getElementById('filtro-ini').addEventListener('change', carregarGrafico);
document.getElementById('filtro-fim').addEventListener('change', carregarGrafico);

document.getElementById('limpar-filtros').addEventListener('click', () => {
    document.getElementById('filtro-tipo').value = '';
    document.getElementById('filtro-ini').value  = '';
    document.getElementById('filtro-fim').value  = '';
    carregarGrafico();
});

// ── Exportação PDF ───────────────────────────────────────────────────────────

document.getElementById('export').addEventListener('click', () => {
    if (!chart) return;
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(16);
    doc.text('Relatório de Bolsas de Sangue - Hemodat', 148, 20, { align: 'center' });

    setTimeout(() => {
        const dataURL = canvas.toDataURL('image/png');
        doc.addImage(dataURL, 'PNG', 10, 35, 277, 150);
        doc.save('Relatorio_Bolsas_Sangue.pdf');
    }, 200);
});

// ── Carga inicial ─────────────────────────────────────────────────────────────

carregarGrafico();
