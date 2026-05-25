// relatorio.js — Gráfico de barras (Chart.js) + exportação em PDF

fetch(BASE_URL + '/includes/actions/bolsas.php?action=buscar_total')
    .then(response => {
        if (!response.ok) throw new Error('Erro ao buscar os dados do servidor');
        return response.json();
    })
    .then(data => {
        const tiposSanguineos = data.tipos_sanguineos;
        const quantidades     = data.quantidades;

        const canvas = document.getElementById('graficoBar');
        const ctx    = canvas.getContext('2d');

        // Paleta: barras vermelhas, fundo branco (card branco)
        const TICK_COLOR   = '#444';
        const BAR_COLOR    = 'rgba(209, 0, 0, 0.80)';
        const BAR_BORDER   = 'rgba(175, 0, 0, 1)';
        const GRID_COLOR   = 'rgba(0,0,0,0.08)';

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: tiposSanguineos,
                datasets: [{
                    label: 'Bolsas disponíveis (litros)',
                    data: quantidades,
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
                        labels: {
                            color: TICK_COLOR,
                            font: { size: 14, family: 'Outfit' }
                        }
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

        // ── Exportação PDF ──────────────────────────────────────────────
        document.getElementById('export').addEventListener('click', () => {
            const { jsPDF } = window.jspdf;

            // Cores PDF (já são escuras — sem troca necessária)
            const doc = new jsPDF('landscape');

            doc.setFont('helvetica', 'bold');
            doc.setFontSize(16);
            doc.text('Relatório de Bolsas de Sangue — Hemodat', 148, 20, { align: 'center' });

            setTimeout(() => {
                const dataURL = canvas.toDataURL('image/png');
                doc.addImage(dataURL, 'PNG', 10, 35, 277, 150);
                doc.save('Relatorio_Bolsas_Sangue.pdf');
            }, 200);
        });
    })
    .catch(error => {
        console.error('Erro ao carregar os dados para o gráfico:', error);
    });
