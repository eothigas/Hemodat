// ── Password toggle ──────────────────────────────────────────
const SVG_EYE     = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>';
const SVG_EYE_OFF = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A11 11 0 0 1 12 5c7 0 10 7 10 7a13 13 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13 13 0 0 0 2 12s3 7 10 7a9 9 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>';

document.querySelector('.pwd-toggle')?.addEventListener('click', function () {
    const inp = document.getElementById('senha');
    if (inp.type === 'password') {
        inp.type = 'text';
        this.innerHTML = SVG_EYE_OFF;
    } else {
        inp.type = 'password';
        this.innerHTML = SVG_EYE;
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
