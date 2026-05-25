document.getElementById('logout').addEventListener('click', function(event) {
    event.preventDefault(); // Impede o comportamento padrão do link ou botão (se houver)
    
    // Fazendo uma requisição para o PHP que destruirá a sessão
    fetch('/php/logout.php') // Caminho para o arquivo PHP de logout
        .then(response => {
            if (response.ok) {
                // Redireciona ou faz algo após o logout
                window.location.href = '/login.php';
            } else {
                console.error('Erro ao tentar fazer logout');
            }
        })
        .catch(error => {
            console.error('Erro na requisição de logout', error);
        });
});
