<?php
$titulo     = 'Hemodat - Alterar Senha';
$css_pagina = '/assets/css/paginas/alt_senha.css';
require_once __DIR__ . '/includes/other/header.php';
?>

    <div id="principal">
        <div id="conteudo-cima">
            <a href="<?= BASE_URL ?>/forgot_password.php"><i class="bi bi-arrow-left"></i></a>
            <div id="text">
                <h1>ALTERAR SENHA</h1>
                <p>Crie uma senha com mínimo 9 caracteres (8 alfanuméricos + 1 especial).</p>
            </div>
        </div>
        <div id="conteudo-baixo">
            <form id="rec-pass" action="<?= BASE_URL ?>/includes/actions/senha.php?action=alterar" method="post">
                <div class="input-container">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="senha" placeholder="Senha Nova" minlength="9" maxlength="50" required>
                </div>
                <div class="input-container">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="confirm-senha" placeholder="Repita a Senha" minlength="9" maxlength="50" required>
                </div>
                <button type="submit" id="send">Alterar</button>
            </form>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/custom/alterar_senha.js"></script>

</body>
</html>
