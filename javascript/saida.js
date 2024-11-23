// Formatar os campos de data para date (sem ocultar o placeholder)
function formatDate(input) {
    // Remove caracteres não numéricos
    let value = input.value.replace(/\D/g, "");

    // Adiciona barras conforme a quantidade de caracteres
    if (value.length >= 5) {
        let day = value.slice(0, 2);
        let month = value.slice(2, 4);
        let year = value.slice(4, 8);

        // Validação do dia (1 a 31)
        if (parseInt(day) < 1 || parseInt(day) > 31) {
            day = "31"; // Se o dia estiver fora do intervalo, corrige para 31
        }

        // Validação do mês (1 a 12)
        if (parseInt(month) < 1 || parseInt(month) > 12) {
            month = "12"; // Se o mês estiver fora do intervalo, corrige para 12
        }

        input.value = `${day}/${month}/${year}`;
    } else if (value.length >= 3) {
        input.value = `${value.slice(0, 2)}/${value.slice(2, 4)}`;
    } else {
        input.value = value;
    }
}

// Limitar o número de quantidade litros
function limitDigits(input, maxDigits) {
    if (input.value.length > maxDigits) {
        input.value = input.value.slice(0, maxDigits);
    }
}

// Preenche as opções com os tipos sanguíneos cadastrados no banco de dados
document.addEventListener("DOMContentLoaded", async function() {
    try {
        // Fazer a requisição para pegar os tipos sanguíneos
        const response = await fetch('/php/buscar_tipo.php'); // Altere o caminho conforme necessário
        const tiposSanguineosUnicos = await response.json();

        // Referência ao select
        const tipoSelect = document.getElementById('tipo');

        // Criar as opções do select
        tiposSanguineosUnicos.forEach(tipo => {
            const option = document.createElement('option');
            option.value = tipo;
            option.textContent = tipo;
            tipoSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Erro ao carregar os tipos sanguíneos:', error);
    }
});

// Realiza a operação de saída de sangue
document.getElementById('saida').addEventListener('submit', function(event) {
    event.preventDefault(); // Previne o envio do formulário padrão

    // Obter os dados do formulário
    const tipo = document.getElementById('tipo').value;
    const litros = document.querySelector('input[name="litros"]').value;
    const saida = document.querySelector('input[name="saida"]').value;

    // Validar campos (exemplo simples)
    if (!tipo || !litros || !saida) {
        alert('Por favor, preencha todos os campos!');
        return;
    }

    // Criar o objeto de dados para enviar
    const formData = new FormData();
    formData.append('tipo', tipo);
    formData.append('litros', litros);
    formData.append('saida', saida);

    // Enviar o formulário via AJAX (fetch)
    fetch('/php/saida.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        // Verificar o status da resposta
        if (result.status === 'success') {
            alert(result.message);
            // Recarregar a página para atualizar o estado
            window.location.reload();
        } else {
            alert(result.message);
        }
    })
    .catch(error => {
        alert('Erro ao enviar dados: ' + error);
    });
});