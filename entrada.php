<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'HEMODAT — Entrada de Bolsas';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active        = 'entrada';
$page_title    = 'Entrada de Bolsas';
$page_subtitle = 'Registre a entrada de sangue no estoque';
require_once __DIR__ . '/includes/other/sidebar.php';
?>

<div class="app-content">
    <div class="page-2col">

        <!-- ── Formulário ───────────────────────────────────── -->
        <div class="content-card" style="flex:1.2;">
            <div class="content-card-title">
                <i class="bi bi-arrow-down-circle-fill" style="color:var(--hemo-red);"></i>
                Dados da bolsa
            </div>

            <form id="entrada"
                  action="<?= BASE_URL ?>/includes/actions/bolsas.php?action=entrada"
                  method="post">

                <!-- Tipo sanguíneo — blood chips -->
                <div class="form-section-label">Tipo sanguíneo</div>
                <div class="blood-chips mb-4">
                    <?php foreach (TIPOS_VALIDOS as $t): ?>
                    <button type="button" class="blood-chip" data-tipo="<?= $t ?>"><?= $t ?></button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="tipo" id="tipo-hidden" required>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Quantidade (litros)</label>
                        <div class="input-icon">
                            <i class="bi bi-eyedropper"></i>
                            <input type="number" name="litros"
                                   class="form-control"
                                   placeholder="Ex: 0.45"
                                   min="0.01" step="0.01"
                                   oninput="limitDigits(this, 10)" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Data de Coleta</label>
                        <div class="input-icon">
                            <i class="bi bi-calendar-check"></i>
                            <input type="text" inputmode="numeric" name="coleta"
                                   class="form-control"
                                   placeholder="DD/MM/AAAA"
                                   maxlength="10"
                                   oninput="formatDate(this)" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Data de Validade</label>
                        <div class="input-icon">
                            <i class="bi bi-calendar-event"></i>
                            <input type="text" inputmode="numeric" name="validade"
                                   class="form-control"
                                   placeholder="DD/MM/AAAA"
                                   maxlength="10"
                                   oninput="formatDate(this)" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="bi bi-check-circle me-1"></i> Registrar Entrada
                    </button>
                </div>

            </form>
        </div>

        <!-- ── Painel resumo estoque ─────────────────────────── -->
        <div class="content-card" style="flex:1;">
            <div class="content-card-title">
                <i class="bi bi-layers-fill" style="color:var(--hemo-blue);"></i>
                Estoque atual
            </div>
            <div id="estoque-resumo">
                <div class="d-flex align-items-center gap-2 text-muted" style="font-size:13px;">
                    <div class="spinner-border spinner-border-sm"></div> Carregando…
                </div>
            </div>
        </div>

    </div>
</div><!-- /app-content -->
</div><!-- /app-main -->
</div><!-- /app-shell -->

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/main.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/entrada.js"></script>
</body>
</html>
