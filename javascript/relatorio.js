const ctx = document.getElementById('graficoPizza').getContext('2d');
const dados = {
    labels: ['A+','A-', 'B+', 'B-', 'O+', 'O-'],
    datasets: [{
        label: 'Bolsas de Sangue',
        data: [2, 9, 3, 5, 2, 9],
        backgroundColor: [
            'rgba(16, 117, 38)',
            'rgba(255, 230, 0)',
            'rgba(12, 158, 240)',
            'rgba(255, 181, 181)',
            'rgba(255, 122, 0)',
            'rgba(128, 0, 255)'
        ],
        borderColor: [
            'rgba(10, 70, 23, 1)',   
            'rgba(204, 184, 0, 1)', 
            'rgba(8, 100, 153, 1)',  
            'rgba(204, 130, 130, 1)', 
            'rgba(179, 86, 0, 1)', 
            'rgba(77, 0, 153, 1)' 
        ],
        borderWidth: 2
    }]
};

const graficoPizza = new Chart(ctx, {
    type: 'pie',
    data: dados,
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false, // Desativa a legenda padrão
            }
        }
    }
});

// Adicionar legendas abaixo do gráfico
const legenda = document.getElementById('legenda');
dados.labels.forEach((label, index) => {
    const color = dados.datasets[0].backgroundColor[index];
    legenda.innerHTML += `<div style="display: flex; align-items: center;">
                            <span style="display: inline-block; width: 10px; height: 10px; background-color:${color}; margin-right: 5px;"></span>
                            ${label}
                          </div>`;
});