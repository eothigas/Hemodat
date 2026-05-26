<?php
$titulo     = 'Hemodat - Alterar Senha';
$body_class = 'auth-page';
require_once __DIR__ . '/includes/other/header.php';
?>

<div class="narrow-card-wrapper">
    <div class="narrow-card">

        <div class="card-header-area">
            <a href="<?= BASE_URL ?>/forgot_password" title="Voltar">
                <i class="bi bi-arrow-left-circle-fill"></i>
            </a>
            <div>
                <h1>Alterar Senha</h1>
            </div>
        </div>

        <p class="card-subtitle">
            Crie uma senha com mínimo <strong>9 caracteres</strong> (8 alfanuméricos + 1 especial).
        </p>

        <form id="rec-pass"
              action="<?= BASE_URL ?>/includes/actions/senha.php?action=alterar"
              method="post"
              class="d-flex flex-column gap-3">

            <div class="input-icon">
                <i class="bi bi-lock"></i>
                <input type="password" name="senha"
                       class="form-control"
                       placeholder="Nova Senha"
                       minlength="9" maxlength="50" required>
            </div>

            <div class="input-icon">
                <i class="bi bi-lock-fill"></i>
                <input type="password" name="confirm-senha"
                       class="form-control"
                       placeholder="Repita a Senha"
                       minlength="9" maxlength="50" required>
            </div>

            <button type="submit" id="send" class="btn btn-primary mt-1">
                <i class="bi bi-shield-check me-1"></i> Alterar Senha
            </button>

        </form>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/alterar_senha.js"></script>
</body>
</html>
