// Busca token CSRF antes de permitir submit
let csrfToken = '';
fetch('/php/csrf.php')
    .then(r => r.json())
    .then(d => { csrfToken = d.token; })
    .catch(() => {});

document.getElementById('register').addEventListener('submit', async function (event) {
    event.preventDefault();

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
            setTimeout(() => { window.location.href = '/login.html'; }, 1800);
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        console.error('Erro na requisição:', error);
        showToast('Ocorreu um erro ao processar o formulário.', 'error');
    }
});
