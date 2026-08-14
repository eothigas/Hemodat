<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'HEMODAT - Relatórios';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active        = 'relatorio';
$page_title    = 'Relatórios';
$page_subtitle = 'Análise do estoque e movimentações';
require_once __DIR__ . '/includes/other/sidebar.php';
?>

<div class="page">
    <div class="page-head">
        <div>
            <h1>Relatórios</h1>
            <p>Análise do estoque atual por tipo sanguíneo e período de coleta.</p>
        </div>
        <div class="page-actions">
            <button type="button" id="export" class="btn btn-secondary">
                <?= icon('download', ['size' => 16]) ?>
                Exportar PDF
            </button>
        </div>
    </div>

    <div class="card">
        <div class="row" style="gap:10px; flex-wrap:wrap; align-items:flex-end;">
            <label class="field" style="min-width:160px;">
                <span class="field-lbl">Tipo sanguíneo</span>
                <span class="input-wrap">
                    <span class="input-ic"><?= icon('droplet', ['size' => 16]) ?></span>
                    <select id="filtro-tipo">
                        <option value="">Todos</option>
                        <?php foreach (TIPOS_VALIDOS as $t): ?>
                            <option value="<?= $t ?>"><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </span>
            </label>
            <label class="field" style="min-width:170px;">
                <span class="field-lbl">Coleta a partir de</span>
                <span class="input-wrap">
                    <span class="input-ic"><?= icon('calendar', ['size' => 16]) ?></span>
                    <input type="date" id="filtro-ini">
                </span>
            </label>
            <label class="field" style="min-width:170px;">
                <span class="field-lbl">Coleta até</span>
                <span class="input-wrap">
                    <span class="input-ic"><?= icon('calendar', ['size' => 16]) ?></span>
                    <input type="date" id="filtro-fim">
                </span>
            </label>
            <button id="limpar-filtros" class="btn btn-ghost">
                <?= icon('x', ['size' => 16]) ?>
                Limpar
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><div><h3>Estoque por tipo sanguíneo</h3><div class="ch-sub">Litros disponíveis, filtrado pelo período de coleta</div></div></div>
        <div class="card-pad" style="min-height:340px;" id="grafico-relatorio"></div>
        <p id="sem-dados" class="d-none small muted" style="text-align:center; padding:24px;">Nenhum dado para os filtros selecionados.</p>
    </div>

</div><!-- /page -->
</div><!-- /main -->
</div><!-- /app -->

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/charts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/relatorio.js"></script>
</body>
</html>
