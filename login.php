<?php
$titulo     = 'Hemodat - Login';
$body_class = 'auth-page';
require_once __DIR__ . '/includes/other/header.php';
?>

<div class="auth-wrapper">
    <div class="split-card">

        <!-- Painel esquerdo — navy + branding -->
        <div class="split-left">
            <div class="auth-brand">
                <img src="<?= BASE_URL ?>/imagens/logo/logo-white.png"
                     height="36" alt="HEMODAT"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <!-- Fallback -->
                <span style="display:none; flex-direction:column; align-items:center; gap:10px;">
                    <svg width="40" height="44" viewBox="0 0 26 28" fill="none" aria-hidden="true">
                        <rect x="0"    y="20" width="3.5" height="8"  rx="1.2" fill="white" opacity="0.38"/>
                        <rect x="4.5"  y="13" width="3.5" height="15" rx="1.2" fill="white" opacity="0.55"/>
                        <rect x="9"    y="6"  width="3.5" height="22" rx="1.2" fill="white" opacity="0.78"/>
                        <rect x="13.5" y="2"  width="3.5" height="26" rx="1.2" fill="white"/>
                        <rect x="18"   y="7"  width="3.5" height="21" rx="1.2" fill="white" opacity="0.76"/>
                        <rect x="22.5" y="14" width="3.5" height="14" rx="1.2" fill="white" opacity="0.52"/>
                    </svg>
                    <span class="auth-brand-name">HEMODAT</span>
                </span>
            </div>

            <p class="auth-tagline">Decisões em<br>segundos.</p>
            <p class="auth-sub">Não tem conta? Cadastre-se gratuitamente.</p>

            <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-light px-5">
                CADASTRAR
            </a>
        </div>

        <!-- Painel direito — formulário -->
        <div class="split-right">
            <h1>Bem-vindo de volta</h1>
            <h2>Entre com seu e-mail e senha</h2>

            <form id="login"
                  action="<?= BASE_URL ?>/includes/actions/auth.php?action=login"
                  method="post"
                  class="w-100" style="max-width:360px;">

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
                               placeholder="Senha" required>
                    </div>
                </div>

                <div class="mb-4">
                    <a href="<?= BASE_URL ?>/forgot_password.php" class="forgot-link">
                        Esqueci minha senha
                    </a>
                </div>

                <button type="submit" id="logar" class="btn btn-primary w-100">
                    Entrar
                </button>
            </form>
        </div>

    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/login.js"></script>
</body>
</html>
