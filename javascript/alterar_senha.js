document.getElementById('send').addEventListener('click', async (event) => {
    event.preventDefault(); // Previne o comportamento padrão do formulário

    const senha = document.querySelector('input[name="senha"]').value.trim();
    const confirmSenha = document.querySelector('input[name="confirm-senha"]').value.trim();

    if (!senha || !confirmSenha) {
        alert('Por favor, preencha todos os campos.');
        return;
    }

    try {
        const response = await fetch('/php/alterar_senha.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `senha=${encodeURIComponent(senha)}&confirm-senha=${encodeURIComponent(confirmSenha)}`,
        });

        if (!response.ok) {
            throw new Error('Erro na comunicação com o servidor.');
        }

        const result = await response.json();

        if (result.status === 'success') {
            alert(result.message);
            window.location.href = result.redirect;
        } else {
            alert(result.message);
            console.error(result.message);
        }
    } catch (error) {
        alert('Erro ao processar a solicitação. Tente novamente mais tarde.');
        console.error(error);
    }
});
