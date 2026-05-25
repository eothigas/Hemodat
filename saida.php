<?php
$titulo        = 'Hemodat - Saída';
$css_pagina    = '/assets/css/paginas/saida.css';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active = 'saida';
require_once __DIR__ . '/includes/other/nav.php';
?>

    <div id="principal">
        <div id="conteudo-cima">
            <a href="<?= BASE_URL ?>/home.php"><i class="bi bi-arrow-left"></i></a>
            <div id="text">
                <h1>SAÍDA</h1>
                <p>Insira os dados</p>
            </div>
        </div>
        <div id="conteudo-baixo">
            <form id="saida" action="<?= BASE_URL ?>/includes/actions/bolsas.php?action=saida" method="post">
                <div id="saida-form">
                    <div class="input-container">
                        <i class="bi bi-droplet"></i>
                        <select id="tipo" name="tipo" required>
                            <option value="">Escolha o Tipo Sanguíneo</option>
                        </select>
                    </div>
                    <div class="input-container">
                        <i class="bi bi-eyedropper"></i>
                        <input type="number" name="litros" placeholder="Quantidade (em litros)" min="0.01" step="0.01" oninput="limitDigits(this, 10)" required>
                    </div>
                    <div class="input-container">
                        <i class="bi bi-calendar"></i>
                        <input type="text" inputmode="numeric" name="saida" placeholder="Data de Saída (DD/MM/AAAA)" maxlength="10" oninput="formatDate(this)" required>
                    </div>
                </div>
                <button type="submit" id="registrar">Registrar</button>
            </form>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/custom/saida.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/padrao/main.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>

</body>
</html>
