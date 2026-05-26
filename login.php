<?php
$titulo     = 'HEMODAT - Login';
$body_class = 'auth-page';
require_once __DIR__ . '/includes/other/header.php';
?>

<div class="auth-wrapper">
    <div class="split-card">

        <!-- ── Painel esquerdo - navy + brand ─────────────── -->
        <div class="split-left">

            <div class="auth-brand">
                <img src="<?= BASE_URL ?>/imagens/logo/logo-white.png"
                     height="38" alt="HEMODAT"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                <span style="display:none;">
                    <span class="auth-brand-name">HEMODAT</span>
                </span>
            </div>

            <p class="auth-tagline">Gestão de estoque<br>de sangue.</p>
            <p class="auth-sub">Controle preciso. Decisões em segundos.</p>

            <ul class="auth-features">
                <li>
                    <i class="bi bi-activity"></i>
                    Monitoramento em tempo real
                </li>
                <li>
                    <i class="bi bi-exclamation-triangle"></i>
                    Alertas de vencimento automáticos
                </li>
                <li>
                    <i class="bi bi-bar-chart-line"></i>
                    Relatórios e histórico completo
                </li>
                <li>
                    <i class="bi bi-shield-check"></i>
                    Controle de acesso por perfil
                </li>
            </ul>

        </div>

        <!-- ── Painel direito - formulário ────────────────── -->
        <div class="split-right">

            <div class="auth-form-header">
                <h1>Bem-vindo de volta</h1>
                <h2>Acesse o painel HEMODAT</h2>
            </div>

            <form id="login"
                  action="<?= BASE_URL ?>/includes/actions/auth.php?action=login"
                  method="post"
                  class="auth-form">

                <div class="auth-field">
                    <label for="email">E-mail</label>
                    <div class="input-icon">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" name="email"
                               class="form-control"
                               placeholder="seu@email.com"
                               autocomplete="email"
                               required>
                    </div>
                </div>

                <div class="auth-field">
                    <label for="senha">Senha</label>
                    <div class="input-icon input-icon--password">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="senha" name="senha"
                               class="form-control"
                               placeholder="••••••••••"
                               autocomplete="current-password"
                               required>
                        <button type="button" class="pwd-toggle" tabindex="-1" aria-label="Mostrar senha">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <a href="<?= BASE_URL ?>/forgot_password" class="forgot-link">
                        Esqueci minha senha
                    </a>
                </div>

                <button type="submit" id="logar" class="btn btn-primary w-100 mt-2">
                    <i class="bi bi-box-arrow-in-right me-1"></i>
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
