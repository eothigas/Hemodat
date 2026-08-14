<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'HEMODAT - Entrada de Bolsas';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active        = 'entrada';
$page_title    = 'Entrada de Bolsas';
$page_subtitle = 'Registre a entrada de sangue no estoque';
require_once __DIR__ . '/includes/other/sidebar.php';
?>

<div class="page">
    <div class="page-head">
        <div>
            <h1>Registrar entrada de bolsa</h1>
            <p>Cadastre uma nova entrada de sangue no estoque do hemocentro.</p>
        </div>
    </div>

    <div class="grid grid-3-2">
        <div class="card card-pad-lg">
            <div class="card-head" style="margin-bottom:0;"><div><h3>Dados da entrada</h3><div class="ch-sub">Todos os campos são obrigatórios</div></div></div>

            <form id="entrada" class="col" style="gap:20px; margin-top:16px;">
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

                <div class="grid grid-3">
                    <label class="field">
                        <span class="field-lbl">Volume (litros)</span>
                        <span class="input-wrap">
                            <span class="input-ic"><?= icon('droplet', ['size' => 16]) ?></span>
                            <input type="number" name="litros" step="0.01" min="0.01" placeholder="0.45" required>
                        </span>
                    </label>
                    <label class="field">
                        <span class="field-lbl">Data da coleta</span>
                        <span class="input-wrap">
                            <span class="input-ic"><?= icon('calendar', ['size' => 16]) ?></span>
                            <input type="text" name="coleta" placeholder="DD/MM/AAAA" required>
                        </span>
                    </label>
                    <label class="field">
                        <span class="field-lbl">Validade</span>
                        <span class="input-wrap">
                            <span class="input-ic"><?= icon('clock', ['size' => 16]) ?></span>
                            <input type="text" name="validade" placeholder="DD/MM/AAAA" required>
                        </span>
                    </label>
                </div>

                <div class="row" style="justify-content:flex-end; gap:10px; margin-top:4px;">
                    <button type="submit" class="btn btn-primary">
                        <?= icon('check', ['size' => 16]) ?>
                        Registrar entrada
                    </button>
                </div>
            </form>
        </div>

        <div class="col" style="gap:16px;">
            <div class="card">
                <div class="card-head"><div><h3>Estoque atual</h3></div></div>
                <div class="card-pad" id="estoque-resumo">
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
<script src="<?= BASE_URL ?>/assets/js/custom/entrada.js"></script>
</body>
</html>
