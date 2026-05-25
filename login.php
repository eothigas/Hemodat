<?php
$titulo     = 'Hemodat - Login';
$css_pagina = '/assets/css/paginas/login.css';
require_once __DIR__ . '/includes/other/header.php';
?>

    <div id="principal">

        <div id="conteudo-esquerda">
            <img src="/imagens/logo/logo.png" id="logo" alt="LOGO">
            <h1>Seja Bem-Vindo!</h1>
            <p>Se não tiver uma conta, cadastre-se agora mesmo!</p>

            <a href="/index.php">
                <button type="button" id="cadastro">CADASTRAR</button>
            </a>
        </div>

        <div id="conteudo-direita">
            <h1>Entre com sua conta</h1>

            <form id="login" action="/php/login.php" method="post">
                <div class="input-container">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" placeholder="Email" id="email">
                </div>
                <div class="input-container">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="senha" placeholder="Senha" id="senha">
                    <a href="/forgot_password.php"><p id="forgot">Esqueci minha senha</p></a>
                </div>
                <button type="submit" id="logar">ENTRAR</button>
            </form>
        </div>
    </div>

    <script src="/assets/js/padrao/toast.js"></script>
    <script src="/assets/js/custom/login.js"></script>

</body>
</html>
