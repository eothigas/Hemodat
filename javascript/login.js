document.getElementById('logar').addEventListener('click', async (event) => {
    event.preventDefault(); // Previne o comportamento padrão do botão (submit)

    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value.trim();

    // Verifica se os campos estão preenchidos
    if (!email || !senha) {
        alert('Por favor, preencha todos os campos!');
        return;
    }

    try {
        // Envia os dados via POST para o PHP
        const response = await fetch('/php/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(email)}&senha=${encodeURIComponent(senha)}`
        });

        // Recebe a resposta em formato JSON
        const result = await response.json();

        if (result.status === 'success') {
            window.location.href = result.redirect;
        } else {
            // Exibe a mensagem de erro se as credenciais forem inválidas
            alert(result.message);
        }
    } catch (error) {
        // Se houver algum erro na requisição
        alert('Erro ao processar o login. Tente novamente mais tarde.');
        console.error(error);
    }
});
