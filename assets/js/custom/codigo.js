document.getElementById('send').addEventListener('click', async (event) => {
    event.preventDefault();

    const codigo = document.querySelector('input[name="code"]').value.trim().toUpperCase();

    if (!codigo) {
        showToast('Por favor, insira o código.', 'error');
        return;
    }

    try {
        const response = await fetch('/php/codigo.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `code=${encodeURIComponent(codigo)}`,
        });

        if (!response.ok) throw new Error('Erro na comunicação com o servidor.');

        const result = await response.json();

        if (result.status === 'success') {
            showToast(result.message, 'success');
            setTimeout(() => { window.location.href = result.redirect; }, 1500);
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        showToast('Erro ao processar a solicitação. Tente novamente mais tarde.', 'error');
        console.error(error);
    }
});
