// Busca token CSRF e armazena como Promise (evita race condition)
const csrfReady = fetch(BASE_URL + '/includes/functions/csrf.php')
    .then(r => r.json())
    .then(d => d.token)
    .catch(() => '');

document.getElementById('register').addEventListener('submit', async function (event) {
    event.preventDefault();

    const csrfToken = await csrfReady;

    if (!csrfToken) {
        showToast('Erro de segurança. Recarregue a página.', 'error');
        return;
    }

    const formData = new FormData(this);
    formData.append('csrf_token', csrfToken);

    try {
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
        });

        const result = await response.json();

        if (result.status === 'success') {
            showToast(result.message, 'success');
            setTimeout(() => { window.location.href = BASE_URL + '/login.php'; }, 1800);
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        console.error('Erro na requisição:', error);
        showToast('Ocorreu um erro ao processar o formulário.', 'error');
    }
});
