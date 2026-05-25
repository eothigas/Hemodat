// Busca token CSRF antes de permitir submit
let csrfToken = '';
fetch('/includes/functions/csrf.php')
    .then(r => r.json())
    .then(d => { csrfToken = d.token; })
    .catch(() => {});

document.getElementById('logar').addEventListener('click', async (event) => {
    event.preventDefault();

    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value.trim();

    if (!email || !senha) {
        showToast('Por favor, preencha todos os campos!', 'error');
        return;
    }

    try {
        const response = await fetch('/includes/actions/auth.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(email)}&senha=${encodeURIComponent(senha)}&csrf_token=${encodeURIComponent(csrfToken)}`,
        });

        const result = await response.json();

        if (result.status === 'success') {
            window.location.href = result.redirect;
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        showToast('Erro ao processar o login. Tente novamente mais tarde.', 'error');
        console.error(error);
    }
});
