<?php
$titulo     = 'Hemodat - Cadastro';
$body_class = 'auth-page';
require_once __DIR__ . '/includes/other/header.php';
?>

<div class="auth-wrapper">
    <div class="split-card">

        <!-- Painel esquerdo — navy + branding -->
        <div class="split-left">
            <div class="auth-brand">
                <!-- Pixel Drop logo -->
                <svg width="48" height="52" viewBox="0 0 26 28" fill="none" aria-hidden="true">
                    <rect x="0"    y="20" width="3.5" height="8"  rx="1.2" fill="white" opacity="0.38"/>
                    <rect x="4.5"  y="13" width="3.5" height="15" rx="1.2" fill="white" opacity="0.55"/>
                    <rect x="9"    y="6"  width="3.5" height="22" rx="1.2" fill="white" opacity="0.78"/>
                    <rect x="13.5" y="2"  width="3.5" height="26" rx="1.2" fill="white"/>
                    <rect x="18"   y="7"  width="3.5" height="21" rx="1.2" fill="white" opacity="0.76"/>
                    <rect x="22.5" y="14" width="3.5" height="14" rx="1.2" fill="white" opacity="0.52"/>
                </svg>
                <span class="auth-brand-name">HEMODAT</span>
            </div>

            <p class="auth-tagline">Sangue salva vidas.<br>Operação organizada.</p>
            <p class="auth-sub">Já tem uma conta? Acesse agora.</p>

            <a href="<?= BASE_URL ?>/login.php" class="btn btn-outline-light px-5">
                ENTRAR
            </a>
        </div>

        <!-- Painel direito — formulário -->
        <div class="split-right">
            <h1>Crie sua conta</h1>
            <h2>Preencha seus dados abaixo</h2>

            <form id="register"
                  action="<?= BASE_URL ?>/includes/actions/auth.php?action=cadastro"
                  method="post"
                  class="w-100" style="max-width:360px;">

                <div class="mb-3">
                    <div class="input-icon">
                        <i class="bi bi-person"></i>
                        <input type="text" name="nome"
                               class="form-control"
                               placeholder="Nome completo" required
                               minlength="3" maxlength="50">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-icon">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email"
                               class="form-control"
                               placeholder="E-mail" required>
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

                <small class="d-block mb-4" style="font-size:.75rem; color:#94A3B8;">
                    Mínimo 9 caracteres (8 alfanuméricos + 1 especial).
                </small>

                <button type="submit" class="btn btn-primary w-100">
                    Criar conta
                </button>
            </form>
        </div>

    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/cadastro.js"></script>
</body>
</html>
