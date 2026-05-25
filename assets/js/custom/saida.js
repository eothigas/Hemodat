// formatDate e limitDigits definidos em main.js (carregado depois deste arquivo)

// Busca token CSRF
let csrfToken = '';
fetch(BASE_URL + '/includes/functions/csrf.php')
    .then(r => r.json())
    .then(d => { csrfToken = d.token; })
    .catch(() => {});

// Popula select com tipos sanguíneos disponíveis em estoque
document.addEventListener('DOMContentLoaded', async function () {
    try {
        const response = await fetch(BASE_URL + '/includes/actions/bolsas.php?action=buscar_tipo');
        const tipos = await response.json();
        const tipoSelect = document.getElementById('tipo');

        tipos.forEach(tipo => {
            const option = document.createElement('option');
            option.value = tipo;
            option.textContent = tipo;
            tipoSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Erro ao carregar os tipos sanguíneos:', error);
        showToast('Erro ao carregar tipos sanguíneos.', 'error');
    }
});

// Submissão do formulário de saída
document.getElementById('saida').addEventListener('submit', function (event) {
    event.preventDefault();

    const tipo   = document.getElementById('tipo').value;
    const litros = document.querySelector('input[name="litros"]').value;
    const saida  = document.querySelector('input[name="saida"]').value;

    if (!tipo || !litros || !saida) {
        showToast('Por favor, preencha todos os campos!', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('tipo',        tipo);
    formData.append('litros',      litros);
    formData.append('saida',       saida);
    formData.append('csrf_token',  csrfToken);

    fetch(BASE_URL + '/includes/actions/bolsas.php?action=saida', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(result => {
            if (result.status === 'success') {
                showToast(result.message, 'success');
                setTimeout(() => window.location.reload(), 1800);
            } else {
                showToast(result.message, 'error');
            }
        })
        .catch(error => {
            showToast('Erro ao enviar dados: ' + error, 'error');
        });
});
