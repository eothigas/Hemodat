<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'HEMODAT - Saída de Bolsas';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active        = 'saida';
$page_title    = 'Saída de Bolsas';
$page_subtitle = 'Registre a saída de sangue do estoque';
require_once __DIR__ . '/includes/other/sidebar.php';
?>

<div class="page">
    <div class="page-head">
        <div>
            <h1>Registrar saída</h1>
            <p>Registre a saída de sangue do estoque (FEFO — libera o lote mais próximo do vencimento).</p>
        </div>
    </div>

    <div class="grid grid-3-2">
        <div class="card card-pad-lg">
            <div class="card-head" style="margin-bottom:0;"><div><h3>Dados da saída</h3></div></div>

            <form id="saida" class="col" style="gap:20px; margin-top:16px;">
                <div>
                    <span class="field-lbl" style="display:block; margin-bottom:10px;">Tipo sanguíneo</span>
                    <div class="row" style="gap:6px; flex-wrap:wrap;">
                        <?php foreach (TIPOS_VALIDOS as $t): ?>
                            <button type="button" class="chip blood-chip" data-tipo="<?= $t ?>" style="min-width:60px; justify-content:center;">
                                <span style="font-family:var(--font-mono); font-weight:700;"><?= $t ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="tipo-hidden" name="tipo" required>
                </div>

                <hr class="divider">

                <div class="grid grid-2">
                    <label class="field">
                        <span class="field-lbl">Volume a liberar (litros)</span>
                        <span class="input-wrap">
                            <span class="input-ic"><?= icon('droplet', ['size' => 16]) ?></span>
                            <input type="number" name="litros" step="0.01" min="0.01" placeholder="0.45" required>
                        </span>
                    </label>
                    <label class="field">
                        <span class="field-lbl">Data da saída</span>
                        <span class="input-wrap">
                            <span class="input-ic"><?= icon('calendar', ['size' => 16]) ?></span>
                            <input type="text" name="saida" placeholder="DD/MM/AAAA" required>
                        </span>
                    </label>
                </div>

                <div class="alert alert-amber" style="font-size:12.5px;">
                    <?= icon('alert', ['size' => 15]) ?>
                    <span>A saída retira automaticamente do lote com validade mais próxima (FEFO) para o tipo selecionado.</span>
                </div>

                <div class="row" style="justify-content:flex-end; gap:10px;">
                    <button type="submit" class="btn btn-primary">
                        <?= icon('arrow-up', ['size' => 16]) ?>
                        Registrar saída
                    </button>
                </div>
            </form>
        </div>

        <div class="col" style="gap:16px;">
            <div class="card">
                <div class="card-head"><div><h3>Últimas saídas</h3></div></div>
                <div class="card-pad" id="ultimas-saidas">
                    <p class="small muted">Carregando…</p>
                </div>
            </div>
        </div>
    </div>
</div>

</div><!-- /main -->
</div><!-- /app -->

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/saida.js"></script>
</body>
</html>
