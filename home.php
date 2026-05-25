<?php
$titulo        = 'Hemodat - Home';
$css_pagina    = '/assets/css/paginas/home.css';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active = 'home';
require_once __DIR__ . '/includes/other/nav.php';
?>

    <div id="home">
        <img src="/imagens/background.png" alt="">
        <div id="welcome">
            <h2>Bem-vindo ao HEMODAT!</h2>
        </div>
    </div>

    <script src="/assets/js/padrao/toast.js"></script>
    <script src="/assets/js/padrao/main.js"></script>
    <script src="/assets/js/padrao/logout.js"></script>

</body>
</html>
