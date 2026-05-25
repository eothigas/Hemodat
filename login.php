<?php
$titulo     = 'Hemodat — Login';
$body_class = 'auth-page';
require_once __DIR__ . '/includes/other/header.php';
?>

<div class="auth-wrapper">
    <div class="split-card">

        <!-- Painel esquerdo -->
        <div class="split-left">
            <img src="<?= BASE_URL ?>/imagens/logo/logo.png" width="140" alt="Hemodat">
            <h1>Seja Bem-Vindo!</h1>
            <p>Não tem uma conta? Cadastre-se agora mesmo!</p>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-light mt-3 px-4">
                CADASTRAR
            </a>
        </div>

        <!-- Painel direito -->
        <div class="split-right">
            <h1>Entre com sua conta</h1>
            <h2>Preencha seus dados abaixo</h2>

            <form id="login"
                  action="<?= BASE_URL ?>/includes/actions/auth.php?action=login"
                  method="post"
                  class="w-100" style="max-width:380px;">

                <div class="mb-3">
                    <div class="input-icon">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email"
                               class="form-control"
                               placeholder="Email" required>
                    </div>
                </div>

                <div class="mb-1">
                    <div class="input-icon">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="senha"
                               class="form-control"
                               placeholder="Senha" required>
                    </div>
                </div>

                <div class="mb-4">
                    <a href="<?= BASE_URL ?>/forgot_password.php" class="forgot-link">
                        Esqueci minha senha
                    </a>
                </div>

                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary px-5">
                        ENTRAR
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/login.js"></script>
</body>
</html>
