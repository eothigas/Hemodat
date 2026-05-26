<?php
$titulo     = 'Hemodat - Recuperação de Senha';
$body_class = 'auth-page';
require_once __DIR__ . '/includes/other/header.php';
?>

<div class="narrow-card-wrapper">
    <div class="narrow-card" style="max-width:420px;">

        <!-- Cabeçalho fixo -->
        <div class="card-header-area" style="margin-bottom:1.25rem;">
            <a href="<?= BASE_URL ?>/login" title="Voltar ao login">
                <i class="bi bi-arrow-left-circle-fill"></i>
            </a>
            <div>
                <h1 id="step-title">Recuperação de Senha</h1>
            </div>
        </div>

        <!-- Indicador de progresso -->
        <div class="rec-steps" aria-label="Etapas">
            <div class="rec-step active" data-step="1">
                <div class="rec-step-dot"><i class="bi bi-envelope-fill"></i></div>
                <span>E-mail</span>
            </div>
            <div class="rec-step-line"></div>
            <div class="rec-step" data-step="2">
                <div class="rec-step-dot"><i class="bi bi-key-fill"></i></div>
                <span>Código</span>
            </div>
            <div class="rec-step-line"></div>
            <div class="rec-step" data-step="3">
                <div class="rec-step-dot"><i class="bi bi-shield-lock-fill"></i></div>
                <span>Nova senha</span>
            </div>
        </div>

        <!-- ── Step 1: Solicitar código ─────────────────────────────────── -->
        <div class="rec-panel" id="panel-1">
            <p class="card-subtitle">
                Insira seu usuário e e-mail cadastrado para receber o código de recuperação.
            </p>
            <form id="form-recuperar" class="d-flex flex-column gap-3" novalidate>
                <div class="input-icon">
                    <i class="bi bi-person"></i>
                    <input type="text" name="usuario" class="form-control"
                           placeholder="Usuário" autocomplete="username" required>
                </div>
                <div class="input-icon">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" class="form-control"
                           placeholder="E-mail cadastrado" autocomplete="email" required>
                </div>
                <button type="submit" class="btn btn-primary mt-1">
                    <i class="bi bi-send me-1"></i> Enviar código
                </button>
            </form>
        </div>

        <!-- ── Step 2: Validar código ───────────────────────────────────── -->
        <div class="rec-panel d-none" id="panel-2">
            <p class="card-subtitle" id="subtitle-2">
                Insira o código enviado por e-mail. Válido por <strong>15 minutos</strong>.
            </p>
            <form id="form-validar" class="d-flex flex-column gap-3" novalidate>
                <div class="input-icon">
                    <i class="bi bi-key"></i>
                    <input id="code-input" type="text" name="code"
                           class="form-control text-center fw-bold"
                           style="letter-spacing:.25em;font-size:1.15rem;"
                           placeholder="XXXXXXXX"
                           maxlength="8"
                           autocomplete="one-time-code" required>
                </div>
                <button type="submit" class="btn btn-primary mt-1">
                    <i class="bi bi-check-circle me-1"></i> Validar código
                </button>
                <button type="button" id="btn-reenviar" class="btn btn-link btn-sm p-0 text-muted">
                    Não recebi o e-mail — reenviar
                </button>
            </form>
        </div>

        <!-- ── Step 3: Nova senha ───────────────────────────────────────── -->
        <div class="rec-panel d-none" id="panel-3">
            <p class="card-subtitle">
                Crie uma senha com mínimo <strong>9 caracteres</strong>.
            </p>
            <form id="form-alterar" class="d-flex flex-column gap-3" novalidate>
                <div class="input-icon input-icon--password">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="senha" id="nova-senha"
                           class="form-control"
                           placeholder="Nova senha"
                           minlength="9" maxlength="50" required>
                    <button type="button" class="pwd-toggle" data-target="nova-senha" aria-label="Ver senha">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <!-- Indicador de força -->
                <div id="pwd-strength-wrap" class="pwd-strength-wrap" aria-live="polite">
                    <div class="pwd-strength-bar">
                        <div id="pwd-strength-fill" class="pwd-strength-fill"></div>
                    </div>
                    <span id="pwd-strength-label" class="pwd-strength-label"></span>
                </div>

                <div class="input-icon input-icon--password">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="confirm-senha" id="conf-senha"
                           class="form-control"
                           placeholder="Repita a senha"
                           minlength="9" maxlength="50" required>
                    <button type="button" class="pwd-toggle" data-target="conf-senha" aria-label="Ver senha">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <button type="submit" class="btn btn-primary mt-1">
                    <i class="bi bi-shield-check me-1"></i> Alterar senha
                </button>
            </form>
        </div>

    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/recuperar_senha.js"></script>
</body>
</html>
