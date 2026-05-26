<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'Hemodat - Home';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active = 'home';
$nome   = htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário');
require_once __DIR__ . '/includes/other/nav.php';
?>

<main class="dashboard-main">
    <div class="container">

        <!-- Card principal de boas-vindas -->
        <div class="hemodat-card text-center py-4 mb-3">

            <div class="mb-3">
                <span style="font-size:3.5rem; color:var(--hemo-red);">
                    <i class="bi bi-heart-pulse-fill"></i>
                </span>
            </div>

            <h1 class="fw-bold mb-1" style="color:var(--hemo-red); font-size:1.9rem;">
                Olá, <?= $nome ?>!
            </h1>
            <p class="text-muted mb-4" style="max-width:480px; margin:0 auto;">
                Use o menu para registrar entradas, saídas ou visualizar relatórios.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="<?= BASE_URL ?>/entrada.php" class="btn btn-primary px-4">
                    <i class="bi bi-box-arrow-in-down me-1"></i> Entrada
                </a>
                <a href="<?= BASE_URL ?>/saida.php" class="btn btn-outline-danger px-4">
                    <i class="bi bi-box-arrow-up me-1"></i> Saída
                </a>
                <a href="<?= BASE_URL ?>/relatorio.php" class="btn btn-outline-danger px-4">
                    <i class="bi bi-bar-chart-line me-1"></i> Relatório
                </a>
                <a href="<?= BASE_URL ?>/historico.php" class="btn btn-outline-danger px-4">
                    <i class="bi bi-clock-history me-1"></i> Histórico
                </a>
            </div>

        </div>

        <!-- Alertas dinâmicos (preenchidos via home.js) -->
        <div id="alertas-container" class="d-flex flex-column gap-3"></div>

    </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/main.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/home.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
</body>
</html>
