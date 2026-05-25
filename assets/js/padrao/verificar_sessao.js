document.addEventListener('DOMContentLoaded', () => {
    function verificarSessao() {
        fetch(BASE_URL + '/includes/actions/auth.php?action=session')
            .then(r => {
                if (!r.ok) throw new Error('Erro ao consultar servidor.');
                return r.json();
            })
            .then(data => {
                if (!data.usuario_logado) {
                    window.location.href = BASE_URL + '/login.php';
                }
            })
            .catch(() => {
                window.location.href = BASE_URL + '/login.php';
            });
    }

    verificarSessao();
});
