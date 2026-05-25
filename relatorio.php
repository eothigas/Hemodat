<?php
$titulo        = 'Hemodat - Relatório';
$css_pagina    = '/assets/css/paginas/relatorio.css';
$requer_sessao = true;
$head_extras   = ['<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>'];
require_once __DIR__ . '/includes/other/header.php';

$active = 'relatorio';
require_once __DIR__ . '/includes/other/nav.php';
?>

    <div id="dados">
        <div id="title-grafico">
            <h2>Níveis de Sangue Disponíveis</h2>
            <canvas id="graficoBar"></canvas>
        </div>
        <button type="button" id="export">EXPORTAR RELATÓRIO EM PDF</button>
    </div>

    <script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/custom/relatorio.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/padrao/main.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

</body>
</html>
