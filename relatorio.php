<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'Hemodat - Relatório';
$body_class    = 'dashboard-page';
$requer_sessao = true;
$head_extras   = ['<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>'];
require_once __DIR__ . '/includes/other/header.php';

$active = 'relatorio';
require_once __DIR__ . '/includes/other/nav.php';
?>

<main class="dashboard-main">
    <div class="container">
        <div class="hemodat-card">

            <!-- Cabeçalho -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                <div>
                    <h1 class="fw-bold mb-0" style="font-size:1.5rem; color:#222;">
                        <i class="bi bi-bar-chart-line me-2" style="color:var(--hemo-red);"></i>
                        Níveis de Sangue Disponíveis
                    </h1>
                    <p class="text-muted mb-0" style="font-size:.875rem;">
                        Estoque atual por tipo sanguíneo
                    </p>
                </div>
                <button type="button" id="export" class="btn btn-primary px-4">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
                </button>
            </div>

            <!-- Filtros -->
            <div class="row g-2 mb-4 align-items-end">
                <div class="col-sm-4">
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
                    <button id="limpar-filtros" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-x-circle me-1"></i>Limpar
                    </button>
                </div>
            </div>

            <!-- Gráfico -->
            <div style="position:relative; height:400px;">
                <canvas id="graficoBar"></canvas>
            </div>

            <!-- Mensagem sem dados -->
            <p id="sem-dados" class="text-center text-muted py-4 d-none">
                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                Nenhum dado encontrado para os filtros selecionados.
            </p>

        </div>
    </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/main.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/relatorio.js"></script>
</body>
</html>
