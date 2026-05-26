<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'HEMODAT — Relatórios';
$body_class    = 'dashboard-page';
$requer_sessao = true;
$head_extras   = ['<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" defer></script>'];
require_once __DIR__ . '/includes/other/header.php';

$active        = 'relatorio';
$page_title    = 'Relatórios';
$page_subtitle = 'Análise do estoque e movimentações';
require_once __DIR__ . '/includes/other/sidebar.php';
?>

<div class="app-content">

    <!-- ── Filtros ──────────────────────────────────────────── -->
    <div class="content-card mb-0">
        <div class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label text-muted small mb-1">Tipo Sanguíneo</label>
                <div class="input-icon">
                    <i class="bi bi-droplet-fill"></i>
                    <select id="filtro-tipo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php foreach (TIPOS_VALIDOS as $t): ?>
                            <option value="<?= $t ?>"><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <label class="form-label text-muted small mb-1">Coleta a partir de</label>
                <input type="date" id="filtro-ini" class="form-control form-control-sm">
            </div>
            <div class="col-sm-3">
                <label class="form-label text-muted small mb-1">Coleta até</label>
                <input type="date" id="filtro-fim" class="form-control form-control-sm">
            </div>
            <div class="col-sm-2">
                <button id="limpar-filtros" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-x-circle me-1"></i>Limpar
                </button>
            </div>
            <div class="col-sm-1 d-flex justify-content-end">
                <button type="button" id="export" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-file-earmark-pdf"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ── Gráfico estoque ──────────────────────────────────── -->
    <div class="content-card">
        <div class="content-card-title">
            <i class="bi bi-bar-chart-fill" style="color:var(--hemo-blue);"></i>
            Estoque por tipo sanguíneo
        </div>
        <div style="position:relative; height:340px;">
            <canvas id="graficoBar"></canvas>
        </div>
        <p id="sem-dados" class="text-center text-muted py-4 d-none">
            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
            Nenhum dado para os filtros selecionados.
        </p>
    </div>

</div><!-- /app-content -->
</div><!-- /app-main -->
</div><!-- /app-shell -->

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/relatorio.js"></script>
</body>
</html>
