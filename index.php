<?php
$titulo    = 'Hemodat - Bem Vindo';
$css_pagina = '/assets/css/paginas/index.css';
require_once __DIR__ . '/includes/other/header.php';
?>

    <div id="principal">

        <div id="conteudo-esquerda">
            <img src="/imagens/logo/logo.png" id="logo" alt="LOGO">
            <h1>Seja Bem-Vindo!</h1>
            <p>Acesse sua conta agora mesmo.</p>

            <a href="/login.php">
                <button type="button" id="login">ENTRAR</button>
            </a>
        </div>

        <div id="conteudo-direita">
            <h1>Crie sua conta</h1>
            <h2>Preencha seus dados</h2>

            <form id="register" action="/includes/actions/auth.php?action=cadastro" method="post">
                <div class="input-container">
                    <i class="bi bi-person-plus"></i>
                    <input type="text" name="nome" placeholder="Nome" required minlength="3" maxlength="50">
                </div>
                <div class="input-container">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-container">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="senha" placeholder="Senha" required minlength="9" maxlength="50">
                    <small>Mínimo 9 caracteres (8 alfanuméricos + 1 especial).</small>
                </div>
                <button type="submit" id="new-register">CADASTRAR</button>
            </form>
        </div>
    </div>

    <script src="/assets/js/padrao/toast.js"></script>
    <script src="/assets/js/custom/cadastro.js"></script>

</body>
</html>
