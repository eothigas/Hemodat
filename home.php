<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'HEMODAT — Dashboard';
$body_class    = 'dashboard-page';
$requer_sessao = true;
$head_extras   = ['<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" defer></script>'];
require_once __DIR__ . '/includes/other/header.php';

$active        = 'home';
$page_title    = 'Dashboard';
$page_subtitle = 'Visão geral do estoque';
require_once __DIR__ . '/includes/other/sidebar.php';
?>

<div class="app-content">

    <!-- ── Stat cards ───────────────────────────────────────── -->
    <div class="stat-grid" id="stat-grid">
        <!-- preenchido por home.js -->
        <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="stat-card skeleton-card">
            <div class="skeleton" style="width:60%;height:13px;margin-bottom:8px;border-radius:6px;"></div>
            <div class="skeleton" style="width:40%;height:28px;border-radius:6px;"></div>
        </div>
        <?php endfor; ?>
    </div>

    <!-- ── Linha 2: Alertas + Estoque por tipo ──────────────── -->
    <div class="dash-row">

        <!-- Alertas vencimento / estoque crítico -->
        <div class="content-card" style="flex:1; min-width:0;">
            <div class="content-card-title">
                <i class="bi bi-exclamation-triangle-fill" style="color:var(--hemo-warning);"></i>
                Alertas
            </div>
            <div id="alertas-container">
                <div class="d-flex align-items-center gap-2 text-muted" style="font-size:13px; padding:.5rem 0;">
                    <div class="spinner-border spinner-border-sm"></div> Carregando…
                </div>
            </div>
        </div>

        <!-- Gráfico estoque por tipo -->
        <div class="content-card" style="flex:1.8; min-width:0;">
            <div class="content-card-title">
                <i class="bi bi-bar-chart-fill" style="color:var(--hemo-blue);"></i>
                Estoque por tipo sanguíneo
            </div>
            <div style="position:relative; height:220px;">
                <canvas id="graficoEstoque"></canvas>
            </div>
        </div>

    </div>

    <!-- ── Ações rápidas ────────────────────────────────────── -->
    <div class="content-card">
        <div class="content-card-title">
            <i class="bi bi-lightning-fill" style="color:var(--hemo-red);"></i>
            Ações rápidas
        </div>
        <div class="quick-actions">
            <a href="<?= BASE_URL ?>/entrada.php" class="quick-action-btn">
                <i class="bi bi-arrow-down-circle-fill"></i>
                <span>Registrar Entrada</span>
            </a>
            <a href="<?= BASE_URL ?>/saida.php" class="quick-action-btn">
                <i class="bi bi-arrow-up-circle-fill"></i>
                <span>Registrar Saída</span>
            </a>
            <a href="<?= BASE_URL ?>/relatorio.php" class="quick-action-btn">
                <i class="bi bi-bar-chart-line-fill"></i>
                <span>Ver Relatórios</span>
            </a>
            <a href="<?= BASE_URL ?>/historico.php" class="quick-action-btn">
                <i class="bi bi-clock-history"></i>
                <span>Histórico</span>
            </a>
        </div>
    </div>

</div><!-- /app-content -->
</div><!-- /app-main -->
</div><!-- /app-shell -->

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/home.js"></script>
</body>
</html>
