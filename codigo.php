<?php
$titulo     = 'Hemodat - Validar Código';
$body_class = 'auth-page';
require_once __DIR__ . '/includes/other/header.php';
?>

<div class="narrow-card-wrapper">
    <div class="narrow-card">

        <div class="card-header-area">
            <a href="<?= BASE_URL ?>/forgot_password.php" title="Voltar">
                <i class="bi bi-arrow-left-circle-fill"></i>
            </a>
            <div>
                <h1>Validar Código</h1>
            </div>
        </div>

        <p class="card-subtitle">
            Insira o código enviado por e-mail. Válido por <strong>15 minutos</strong>.
        </p>

        <form id="rec-pass"
              action="<?= BASE_URL ?>/includes/actions/senha.php?action=validar"
              method="post"
              class="d-flex flex-column gap-3">

            <div class="input-icon">
                <i class="bi bi-key"></i>
                <input id="code" type="text" name="code"
                       class="form-control text-center fw-bold"
                       style="letter-spacing: 0.25em; font-size:1.15rem;"
                       placeholder="XXXXXXXX"
                       maxlength="8"
                       autocomplete="one-time-code" required>
            </div>

            <button type="submit" id="send" class="btn btn-primary mt-1">
                <i class="bi bi-check-circle me-1"></i> Validar Código
            </button>

        </form>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/codigo.js"></script>
</body>
</html>
