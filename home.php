<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'Hemodat - Home';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active = 'home';
require_once __DIR__ . '/includes/other/nav.php';
?>

<main class="dashboard-main">
    <div class="container">
        <div class="hemodat-card text-center py-5">

            <div class="mb-4">
                <span style="font-size:4rem; color:var(--hemo-red);">
                    <i class="bi bi-heart-pulse-fill"></i>
                </span>
            </div>

            <h1 class="fw-bold mb-2" style="color:var(--hemo-red); font-size:2rem;">
                Bem-vindo ao Hemodat!
            </h1>
            <p class="text-muted mb-4" style="max-width:480px; margin:0 auto;">
                Sistema de gerenciamento de banco de sangue. Use o menu acima para
                registrar entradas, saídas ou visualizar relatórios.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap mt-3">
                <a href="<?= BASE_URL ?>/entrada.php" class="btn btn-primary px-4">
                    <i class="bi bi-box-arrow-in-down me-1"></i> Entrada
                </a>
                <a href="<?= BASE_URL ?>/saida.php" class="btn btn-outline-danger px-4">
                    <i class="bi bi-box-arrow-up me-1"></i> Saída
                </a>
                <a href="<?= BASE_URL ?>/relatorio.php" class="btn btn-outline-danger px-4">
                    <i class="bi bi-bar-chart-line me-1"></i> Relatório
                </a>
            </div>

        </div>
    </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/main.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
</body>
</html>
