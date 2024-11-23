document.getElementById('send').addEventListener('click', async (event) => {
    event.preventDefault(); // Previne o comportamento padrão do formulário

    const codigo = document.querySelector('input[name="code"]').value.trim();

    if (!codigo) {
        alert('Por favor, insira o código.');
        return;
    }

    try {
        const response = await fetch('/php/codigo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `code=${encodeURIComponent(codigo)}`,
        });

        if (!response.ok) {
            throw new Error('Erro na comunicação com o servidor.');
        }

        const result = await response.json();

        if (result.status === 'success') {
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
