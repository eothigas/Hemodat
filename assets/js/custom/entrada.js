// formatDate e limitDigits definidos em main.js (carregado depois deste arquivo)

// Busca token CSRF
let csrfToken = '';
fetch(BASE_URL + '/includes/functions/csrf.php')
    .then(r => r.json())
    .then(d => { csrfToken = d.token; })
    .catch(() => {});

document.getElementById('entrada').addEventListener('submit', async (e) => {
    e.preventDefault();

    const form = new FormData(e.target);
    form.append('csrf_token', csrfToken);

    const response = await fetch(BASE_URL + '/includes/actions/bolsas.php?action=entrada', {
        method: 'POST',
        body: form,
    });

    const result = await response.json();

    if (result.status === 'success') {
        showToast(result.message, 'success');
        setTimeout(() => location.reload(), 1800);
    } else {
        showToast(result.message, 'error');
    }
});
