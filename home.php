<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'HEMODAT - Dashboard';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active        = 'home';
$page_title    = 'Dashboard';
$page_subtitle = 'Visão executiva do estoque e movimentações';
require_once __DIR__ . '/includes/other/sidebar.php';
?>

<div class="page">
    <div class="page-head">
        <div>
            <h1>Dashboard</h1>
            <p>Visão executiva do estoque e movimentações</p>
        </div>
        <div class="page-actions">
            <a href="<?= BASE_URL ?>/entrada" class="btn btn-primary">
                <?= icon('plus', ['size' => 16]) ?>
                Registrar entrada
            </a>
        </div>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-4" id="stat-grid">
        <div class="card stat"><div class="stat-label">Carregando…</div></div>
        <div class="card stat"><div class="stat-label">Carregando…</div></div>
        <div class="card stat"><div class="stat-label">Carregando…</div></div>
        <div class="card stat"><div class="stat-label">Carregando…</div></div>
    </div>

    <!-- Charts row -->
    <div class="grid grid-3-2">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Entradas × Saídas</h3>
                    <div class="ch-sub">Últimos 14 dias</div>
                </div>
                <div id="io-legend"></div>
            </div>
            <div class="card-pad" style="height:260px;" id="io-chart"></div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Estoque por tipo sanguíneo</h3>
                    <div class="ch-sub" id="estoque-total-sub">—</div>
                </div>
            </div>
            <div class="card-pad" style="height:260px;" id="estoque-chart"></div>
        </div>
    </div>

    <!-- Movimentações + alertas -->
    <div class="grid grid-3-2">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Últimas movimentações</h3>
                    <div class="ch-sub">Entradas e saídas mais recentes</div>
                </div>
                <a href="<?= BASE_URL ?>/historico" class="btn btn-secondary btn-sm">Ver tudo</a>
            </div>
            <div class="tbl-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Tipo mov.</th>
                            <th>Tipo sang.</th>
                            <th class="num">Volume</th>
                            <th>Responsável</th>
                            <th class="num">Data</th>
                        </tr>
                    </thead>
                    <tbody id="movs-body">
                        <tr><td colspan="5" class="text-center small muted" style="padding:20px;">Carregando…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col" style="gap:16px;">
            <div class="card">
                <div class="card-head"><div><h3>Alertas operacionais</h3></div></div>
                <div class="card-pad" id="alertas-container">
                    <div class="small muted">Carregando…</div>
                </div>
            </div>

            <div class="card">
                <div class="card-head"><div><h3>Distribuição por tipo</h3></div></div>
                <div class="card-pad" id="donut-container" style="display:flex; justify-content:center;">
                </div>
            </div>
        </div>
    </div>
</div>

</div><!-- /main -->
</div><!-- /app -->

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/charts.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/home.js"></script>
</body>
</html>
