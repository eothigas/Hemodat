document.getElementById('register').addEventListener('submit', async function (event) {
    event.preventDefault(); // Evita o envio padrão do formulário

    const formData = new FormData(this); // Captura os dados do formulário

    try {
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
            alert(result.message); // Exibe mensagem de sucesso
            window.location.href = '/login.html'; // Redireciona para login
        } else {
            alert(result.message); // Exibe mensagem de erro
        }
    } catch (error) {
        console.error('Erro na requisição:', error);
        alert('Ocorreu um erro ao processar o formulário.');
    }
});