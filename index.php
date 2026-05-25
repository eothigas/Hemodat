<?php
$titulo     = 'Hemodat - Cadastro';
$body_class = 'auth-page';
require_once __DIR__ . '/includes/other/header.php';
?>

<div class="auth-wrapper">
    <div class="split-card">

        <!-- Painel esquerdo -->
        <div class="split-left">
            <img src="<?= BASE_URL ?>/imagens/logo/logo.png" width="140" alt="Hemodat">
            <h1>Bem-Vindo!</h1>
            <p>Já tem uma conta? Acesse agora mesmo.</p>
            <a href="<?= BASE_URL ?>/login.php" class="btn btn-outline-light mt-3 px-4">
                ENTRAR
            </a>
        </div>

        <!-- Painel direito -->
        <div class="split-right">
            <h1>Crie sua conta</h1>
            <h2>Preencha seus dados abaixo</h2>

            <form id="register"
                  action="<?= BASE_URL ?>/includes/actions/auth.php?action=cadastro"
                  method="post"
                  class="w-100" style="max-width:380px;">

                <div class="mb-3">
                    <div class="input-icon">
                        <i class="bi bi-person-plus"></i>
                        <input type="text" name="nome"
                               class="form-control"
                               placeholder="Nome" required
                               minlength="3" maxlength="50">
                    </div>
                </div>

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
                               placeholder="Senha" required
                               minlength="9" maxlength="50">
                    </div>
                </div>

                <small class="text-muted d-block mb-4" style="font-size:.78rem;">
                    Mínimo 9 caracteres (8 alfanuméricos + 1 especial).
                </small>

                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary px-5">
                        CADASTRAR
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/cadastro.js"></script>
</body>
</html>
