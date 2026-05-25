<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'Hemodat - Entrada';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active = 'entrada';
require_once __DIR__ . '/includes/other/nav.php';
?>

<main class="dashboard-main">
    <div class="container">
        <div class="hemodat-card" style="max-width:620px; margin:0 auto;">

            <!-- Cabeçalho -->
            <div class="page-header">
                <a href="<?= BASE_URL ?>/home.php" title="Voltar">
                    <i class="bi bi-arrow-left-circle"></i>
                </a>
                <div>
                    <h1>Entrada de Bolsas</h1>
                    <p>Registre a entrada de sangue no estoque</p>
                </div>
            </div>

            <!-- Formulário -->
            <form id="entrada"
                  action="<?= BASE_URL ?>/includes/actions/bolsas.php?action=entrada"
                  method="post">

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="input-icon">
                            <i class="bi bi-droplet-fill"></i>
                            <select name="tipo" class="form-select" required>
                                <option value="">Tipo Sanguíneo</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="input-icon">
                            <i class="bi bi-eyedropper"></i>
                            <input type="number" name="litros"
                                   class="form-control"
                                   placeholder="Quantidade (litros)"
                                   min="0.01" step="0.01"
                                   oninput="limitDigits(this, 10)" required>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="input-icon">
                            <i class="bi bi-calendar-check"></i>
                            <input type="text" inputmode="numeric" name="coleta"
                                   class="form-control"
                                   placeholder="Data de Coleta (DD/MM/AAAA)"
                                   maxlength="10"
                                   oninput="formatDate(this)" required>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="input-icon">
                            <i class="bi bi-calendar-event"></i>
                            <input type="text" inputmode="numeric" name="validade"
                                   class="form-control"
                                   placeholder="Data de Validade (DD/MM/AAAA)"
                                   maxlength="10"
                                   oninput="formatDate(this)" required>
                        </div>
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
<script src="<?= BASE_URL ?>/assets/js/custom/entrada.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
</body>
</html>
