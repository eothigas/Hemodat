<?php
$titulo     = 'Hemodat - Esqueci minha senha';
$css_pagina = '/assets/css/paginas/f_password.css';
require_once __DIR__ . '/includes/other/header.php';
?>

    <div id="principal">
        <div id="conteudo-cima">
            <a href="<?= BASE_URL ?>/login.php"><i class="bi bi-arrow-left"></i></a>
            <div id="text">
                <h1>RECUPERAÇÃO DE SENHA</h1>
                <p>Insira os dados</p>
            </div>
        </div>
        <div id="conteudo-baixo">
            <form id="rec-pass" action="<?= BASE_URL ?>/includes/actions/senha.php?action=recuperar" method="post">
                <div class="input-container">
                    <i class="bi bi-person"></i>
                    <input type="text" name="usuario" placeholder="Usuário" required>
                </div>
                <div class="input-container">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" placeholder="Email Registrado" required>
                </div>
                <button type="submit" id="send">Enviar Email</button>
            </form>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/custom/recuperar_senha.js"></script>

</body>
</html>
