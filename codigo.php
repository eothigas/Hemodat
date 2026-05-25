<?php
$titulo     = 'Hemodat - Validar Código';
$css_pagina = '/assets/css/paginas/codigo.css';
require_once __DIR__ . '/includes/other/header.php';
?>

    <div id="principal">
        <div id="conteudo-cima">
            <a href="/forgot_password.php"><i class="bi bi-arrow-left"></i></a>
            <div id="text">
                <h1>RECUPERAÇÃO DE SENHA</h1>
                <p>Insira o código enviado por e-mail (válido por 15 minutos)</p>
            </div>
        </div>
        <div id="conteudo-baixo">
            <form id="rec-pass" action="/php/codigo.php" method="post">
                <div class="input-container">
                    <i class="bi bi-code"></i>
                    <input id="code" type="text" name="code" placeholder="Insira o código aqui..." maxlength="8" autocomplete="one-time-code">
                </div>
                <button type="submit" id="send">Validar Código</button>
            </form>
        </div>
    </div>

    <script src="/assets/js/padrao/toast.js"></script>
    <script src="/assets/js/custom/codigo.js"></script>

</body>
</html>
