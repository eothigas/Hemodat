document.getElementById('send').addEventListener('click', async (event) => {
    event.preventDefault();

    const usuario = document.querySelector('input[name="usuario"]').value.trim();
    const email   = document.querySelector('input[name="email"]').value.trim();

    if (!usuario || !email) {
        showToast('Por favor, preencha todos os campos!', 'error');
        return;
    }

    try {
        const response = await fetch('/includes/actions/senha.php?action=recuperar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `usuario=${encodeURIComponent(usuario)}&email=${encodeURIComponent(email)}`,
        });

        const result = await response.json();

        if (result.status === 'success') {
            showToast(result.message, 'success');
            setTimeout(() => { window.location.href = result.redirect; }, 1800);
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        showToast('Erro ao processar a solicitação. Tente novamente mais tarde.', 'error');
        console.error(error);
    }
});
