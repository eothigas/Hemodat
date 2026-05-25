<?php
$titulo     = 'Hemodat — Recuperação de Senha';
$body_class = 'auth-page';
require_once __DIR__ . '/includes/other/header.php';
?>

<div class="narrow-card-wrapper">
    <div class="narrow-card">

        <div class="card-header-area">
            <a href="<?= BASE_URL ?>/login.php" title="Voltar">
                <i class="bi bi-arrow-left-circle-fill"></i>
            </a>
            <div>
                <h1>Recuperação de Senha</h1>
            </div>
        </div>

        <p class="card-subtitle">
            Insira seu usuário e e-mail cadastrado para receber o código de recuperação.
        </p>

        <form id="rec-pass"
              action="<?= BASE_URL ?>/includes/actions/senha.php?action=recuperar"
              method="post"
              class="d-flex flex-column gap-3">

            <div class="input-icon">
                <i class="bi bi-person"></i>
                <input type="text" name="usuario"
                       class="form-control"
                       placeholder="Usuário" required>
            </div>

            <div class="input-icon">
                <i class="bi bi-envelope"></i>
                <input type="email" name="email"
                       class="form-control"
                       placeholder="Email Registrado" required>
            </div>

            <button type="submit" id="send" class="btn btn-primary mt-1">
                <i class="bi bi-send me-1"></i> Enviar Email
            </button>

        </form>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/recuperar_senha.js"></script>
</body>
</html>
