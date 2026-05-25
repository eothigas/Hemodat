<?php
$titulo        = 'Hemodat - Entrada';
$css_pagina    = '/assets/css/paginas/entrada.css';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active = 'entrada';
require_once __DIR__ . '/includes/other/nav.php';
?>

    <div id="principal">
        <div id="conteudo-cima">
            <a href="/home.php"><i class="bi bi-arrow-left"></i></a>
            <div id="text">
                <h1>ENTRADA</h1>
                <p>Insira os dados</p>
            </div>
        </div>
        <div id="conteudo-baixo">
            <form id="entrada" action="/php/entrada.php" method="post">
                <div class="input-up">
                    <div class="input-container">
                        <i class="bi bi-droplet"></i>
                        <select name="tipo" required>
                            <option value="">Tipo Sanguíneo</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="input-container">
                        <i class="bi bi-eyedropper"></i>
                        <input type="number" name="litros" placeholder="Quantidade (em litros)" min="0.01" step="0.01" oninput="limitDigits(this, 10)" required>
                    </div>
                </div>
                <div class="input-down">
                    <div class="input-container">
                        <i class="bi bi-calendar"></i>
                        <input type="text" inputmode="numeric" name="coleta" placeholder="Data de Coleta (DD/MM/AAAA)" maxlength="10" oninput="formatDate(this)" required>
                    </div>
                    <div class="input-container">
                        <i class="bi bi-calendar-event"></i>
                        <input type="text" inputmode="numeric" name="validade" placeholder="Data de Validade (DD/MM/AAAA)" maxlength="10" oninput="formatDate(this)" required>
                    </div>
                </div>
                <button type="submit" id="registrar">Registrar</button>
            </form>
        </div>
    </div>

    <script src="/assets/js/padrao/toast.js"></script>
    <script src="/assets/js/custom/entrada.js"></script>
    <script src="/assets/js/padrao/main.js"></script>
    <script src="/assets/js/padrao/logout.js"></script>

</body>
</html>
