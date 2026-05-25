// Declara ctx globalmente
let ctx;

// Busca os dados do servidor
fetch(BASE_URL + '/includes/actions/bolsas.php?action=buscar_total') // Caminho para o arquivo PHP que retorna os dados
  .then(response => {
      if (!response.ok) {
          throw new Error('Erro ao buscar os dados do servidor');
      }
      return response.json();
  })
  .then(data => {
      const tiposSanguineos = data.tipos_sanguineos;
      const quantidades = data.quantidades;

      // Obtém o contexto do canvas
      const canvas = document.getElementById('graficoBar');
      ctx = canvas.getContext('2d');

      const dados = {
          labels: tiposSanguineos, // Tipos sanguíneos como rótulos
          datasets: [{
              label: 'Bolsas de Sangue',
              data: quantidades, // Quantidades como valores
              backgroundColor: 'rgb(156, 156, 156)',
              borderColor: 'rgba(0, 0, 0, 0.6)',
              borderWidth: 1
          }]
      };

      // Cria o gráfico
      const chart = new Chart(ctx, {
          type: 'bar', // Gráfico de barras
          data: dados,
          options: {
              responsive: true,
              plugins: {
                  legend: {
                      display: true, // Ativa a legenda
                      labels: {
                          color: 'rgb(255, 255, 255)', // Fonte branca para exibição
                          font: { size: 18 }
                      }
                  }
              },
              scales: {
                  x: {
                      ticks: {
                          color: 'rgb(255, 255, 255)', // Fonte branca para exibição
                          font: { size: 18 }
                      }
                  },
                  y: {
                      beginAtZero: true,
                      ticks: {
                          color: 'rgb(255, 255, 255)', // Fonte branca para exibição
                          font: { size: 18 }
                      }
                  }
              }
          }
      });

      // Adiciona evento de exportação ao botão
      document.getElementById('export').addEventListener('click', () => {
          const { jsPDF } = window.jspdf;

          // Troca as cores das fontes para preto antes de exportar
          chart.options.plugins.legend.labels.color = 'rgb(0, 0, 0)'; // Fonte preta
          chart.options.scales.x.ticks.color = 'rgb(0, 0, 0)'; // Eixo X preto
          chart.options.scales.y.ticks.color = 'rgb(0, 0, 0)'; // Eixo Y preto

          // Atualiza o gráfico para aplicar as mudanças
          chart.update();

          // Adiciona um pequeno atraso para garantir a renderização antes da captura
          setTimeout(() => {
              // Criação de um novo documento PDF com orientação paisagem
              const doc = new jsPDF('landscape');

              // Adiciona um título ao PDF
              doc.setFontSize(16);
              doc.text('Relatório de Bolsas de Sangue', 45, 20, { align: 'center' });

              // Captura a imagem do gráfico
              const dataURL = canvas.toDataURL("image/png");

              // Adiciona a imagem ao PDF
              doc.addImage(dataURL, 'PNG', 5, 40, 280, 110); // Ajuste conforme necessário

              // Salva o PDF com o nome especificado
              doc.save('Relatório Bolsas de Sangue.pdf');

              // Restaura as cores brancas para a exibição
              chart.options.plugins.legend.labels.color = 'rgb(255, 255, 255)'; // Fonte branca
              chart.options.scales.x.ticks.color = 'rgb(255, 255, 255)'; // Eixo X branco
              chart.options.scales.y.ticks.color = 'rgb(255, 255, 255)'; // Eixo Y branco

              // Atualiza o gráfico para exibição
              chart.update();
          }, 300); // 300ms para garantir que o gráfico seja renderizado
      });
  })
  .catch(error => {
      console.error('Erro ao carregar os dados para o gráfico:', error);
  });
