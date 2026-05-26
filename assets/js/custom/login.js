// ── Password toggle ──────────────────────────────────────────
document.querySelector('.pwd-toggle')?.addEventListener('click', function () {
    const inp  = document.getElementById('senha');
    const icon = this.querySelector('i');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

// Busca token CSRF e armazena como Promise (evita race condition)
const csrfReady = fetch(BASE_URL + '/includes/functions/csrf.php')
    .then(r => r.json())
    .then(d => d.token)
    .catch(() => '');

document.getElementById('login').addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.target;

    const email = form.querySelector('[name="email"]').value.trim();
    const senha = form.querySelector('[name="senha"]').value.trim();

    if (!email || !senha) {
        showToast('Por favor, preencha todos os campos!', 'error');
        return;
    }

    const csrfToken = await csrfReady;

    if (!csrfToken) {
        showToast('Erro de segurança. Recarregue a página.', 'error');
        return;
    }

    try {
        const response = await fetch(BASE_URL + '/includes/actions/auth.php?action=login', {
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
