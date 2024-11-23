document.addEventListener('DOMContentLoaded', () => {
    function verificarSessao() {
        fetch('/php/session.php') // Endpoint PHP que retorna o estado da sessão
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao consultar o servidor.');
                }
                return response.json(); // Converte a resposta em JSON
            })
            .then(data => {
                // Verifica se a resposta indica que o usuário está logado
                if (!data.usuario_logado) {
                    // Redireciona para a página de login se não estiver logado
                    window.location.href = './login.html';
                } else {
                    console.log('Usuário logado:', data.email);
                }
            })
            .catch(error => {
                console.error('Erro ao verificar a sessão:', error);
                // Opcional: redirecionar em caso de erro ao verificar a sessão
                window.location.href = './login.html';
            });
    }

    verificarSessao();
});
