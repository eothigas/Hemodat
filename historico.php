<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'HEMODAT - Histórico';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active        = 'historico';
$page_title    = 'Histórico';
$page_subtitle = 'Entradas e saídas de bolsas de sangue';
require_once __DIR__ . '/includes/other/sidebar.php';
?>

<div class="page">
    <div class="page-head">
        <div>
            <h1>Histórico de movimentações</h1>
            <p>Consulte entradas e saídas registradas no estoque.</p>
        </div>
    </div>

    <div class="card" pad="none">
        <div class="row" style="padding:16px 20px; gap:12px; flex-wrap:wrap; border-bottom:1px solid var(--border);">
            <label class="field" style="min-width:160px;">
                <span class="field-lbl">Tipo sanguíneo</span>
                <span class="input-wrap">
                    <span class="input-ic"><?= icon('droplet', ['size' => 16]) ?></span>
                    <select id="f-tipo">
                        <option value="">Todos</option>
                        <?php foreach (TIPOS_VALIDOS as $t): ?>
                            <option value="<?= $t ?>"><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </span>
            </label>
            <label class="field" style="min-width:160px;">
                <span class="field-lbl">Operação</span>
                <span class="input-wrap">
                    <span class="input-ic"><?= icon('filter', ['size' => 16]) ?></span>
                    <select id="f-operacao">
                        <option value="">Todas</option>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saída</option>
                    </select>
                </span>
            </label>
            <button id="limpar-filtros" class="btn btn-ghost" style="margin-top:auto;">
                <?= icon('x', ['size' => 16]) ?>
                Limpar
            </button>
        </div>

        <div class="tbl-wrap">
            <table class="tbl" id="tabela-historico">
                <thead>
                    <tr>
                        <th style="width:110px;">Operação</th>
                        <th>Tipo</th>
                        <th class="num">Quantidade</th>
                        <th class="num">Data</th>
                        <th>Responsável</th>
                    </tr>
                </thead>
                <tbody id="historico-body">
                    <tr><td colspan="5" class="text-center small muted" style="padding:24px;">Carregando…</td></tr>
                </tbody>
            </table>
        </div>

        <div class="tbl-foot">
            <span id="pag-info"></span>
            <div class="row" style="gap:8px;">
                <button id="pag-prev" class="pager-btn" disabled><?= icon('chevron-left', ['size' => 14]) ?> Anterior</button>
                <button id="pag-next" class="pager-btn" disabled>Próximo <?= icon('chevron-right', ['size' => 14]) ?></button>
            </div>
        </div>
    </div>
</div><!-- /page -->
</div><!-- /main -->
</div><!-- /app -->

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/historico.js"></script>
</body>
</html>
