document.getElementById('send').addEventListener('click', async (event) => {
    event.preventDefault();

    const senha       = document.querySelector('input[name="senha"]').value.trim();
    const confirmSenha = document.querySelector('input[name="confirm-senha"]').value.trim();

    if (!senha || !confirmSenha) {
        showToast('Por favor, preencha todos os campos.', 'error');
        return;
    }

    if (senha !== confirmSenha) {
        showToast('As senhas não coincidem.', 'error');
        return;
    }

    if (senha.length < 9) {
        showToast('A senha deve ter pelo menos 9 caracteres (8 alfanuméricos + 1 especial).', 'error');
        return;
    }

    try {
        const response = await fetch('/php/alterar_senha.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `senha=${encodeURIComponent(senha)}&confirm-senha=${encodeURIComponent(confirmSenha)}`,
        });

        if (!response.ok) throw new Error('Erro na comunicação com o servidor.');

        const result = await response.json();

        if (result.status === 'success') {
            showToast(result.message, 'success');
            setTimeout(() => { window.location.href = result.redirect; }, 2000);
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        showToast('Erro ao processar a solicitação. Tente novamente mais tarde.', 'error');
        console.error(error);
    }
});
