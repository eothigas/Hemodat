document.getElementById('send').addEventListener('click', async (event) => {
    event.preventDefault(); // Previne o comportamento padrão do formulário

    const usuario = document.querySelector('input[name="usuario"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();

    // Verifica se os campos estão preenchidos
    if (!usuario || !email) {
        alert('Por favor, preencha todos os campos!');
        return;
    }

    try {
        // Envia os dados via POST para o PHP
        const response = await fetch('/php/recuperar_senha.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `usuario=${encodeURIComponent(usuario)}&email=${encodeURIComponent(email)}`
        });

        // Recebe a resposta em formato JSON
        const result = await response.json();

        if (result.status === 'success') {
            // Exibe a mensagem de sucesso e redireciona
            alert(result.message);
            window.location.href = result.redirect;
        } else {
            // Exibe a mensagem de erro
            alert(result.message);
        }
    } catch (error) {
        // Trata erros de rede ou de código
        alert('Erro ao processar a solicitação. Tente novamente mais tarde.');
        console.error(error);
    }
});
