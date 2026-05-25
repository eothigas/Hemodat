<?php
$titulo        = 'Hemodat — Saída';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active = 'saida';
require_once __DIR__ . '/includes/other/nav.php';
?>

<main class="dashboard-main">
    <div class="container">
        <div class="hemodat-card" style="max-width:560px; margin:0 auto;">

            <!-- Cabeçalho -->
            <div class="page-header">
                <a href="<?= BASE_URL ?>/home.php" title="Voltar">
                    <i class="bi bi-arrow-left-circle"></i>
                </a>
                <div>
                    <h1>Saída de Bolsas</h1>
                    <p>Registre a saída de sangue do estoque</p>
                </div>
            </div>

            <!-- Formulário -->
            <form id="saida"
                  action="<?= BASE_URL ?>/includes/actions/bolsas.php?action=saida"
                  method="post">

                <div class="d-flex flex-column gap-3">

                    <div class="input-icon">
                        <i class="bi bi-droplet-fill"></i>
                        <select id="tipo" name="tipo" class="form-select" required>
                            <option value="">Escolha o Tipo Sanguíneo</option>
                        </select>
                    </div>

                    <div class="input-icon">
                        <i class="bi bi-eyedropper"></i>
                        <input type="number" name="litros"
                               class="form-control"
                               placeholder="Quantidade (litros)"
                               min="0.01" step="0.01"
                               oninput="limitDigits(this, 10)" required>
                    </div>

                    <div class="input-icon">
                        <i class="bi bi-calendar"></i>
                        <input type="text" inputmode="numeric" name="saida"
                               class="form-control"
                               placeholder="Data de Saída (DD/MM/AAAA)"
                               maxlength="10"
                               oninput="formatDate(this)" required>
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="bi bi-check-circle me-1"></i> Registrar
                    </button>
                </div>
            </form>

        </div>
    </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/main.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/saida.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
</body>
</html>
