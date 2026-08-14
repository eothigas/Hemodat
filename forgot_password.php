<?php
$titulo     = 'Hemodat - Recuperação de Senha';
$body_class = 'auth-page';
require_once __DIR__ . '/includes/other/header.php';
?>

<div class="login-shell">

    <aside class="login-aside">
        <div class="login-aside-bg"></div>
        <div class="login-aside-content">
            <?= logo_horizontal(28) ?>
            <div class="login-hero">
                <span class="lh-tag"><?= icon('shield-check', ['size' => 11]) ?> Recuperação segura</span>
                <div>
                    <div style="color:#fff; font-size:13px; margin-bottom:4px;">Redefinição de senha</div>
                    <div class="lh-note">
                        Enviamos um código de verificação para o seu e-mail cadastrado.
                        O código expira em 15 minutos por segurança.
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <main class="login-main">
        <div class="login-card">

            <div class="card-header-area" style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                <a href="<?= BASE_URL ?>/login" title="Voltar ao login" style="color:var(--text-3);">
                    <?= icon('chevron-left', ['size' => 20]) ?>
                </a>
                <h1 id="step-title" style="margin:0; font-size:20px;">Recuperação de Senha</h1>
            </div>

            <!-- Indicador de progresso -->
            <div class="row" style="gap:6px; margin-bottom:20px;" aria-label="Etapas">
                <div class="rec-step active" data-step="1">1</div>
                <div class="rec-step-line"></div>
                <div class="rec-step" data-step="2">2</div>
                <div class="rec-step-line"></div>
                <div class="rec-step" data-step="3">3</div>
            </div>

            <!-- ── Step 1: Solicitar código ─────────────────── -->
            <div class="rec-panel" id="panel-1">
                <p class="card-subtitle" style="color:var(--text-3); font-size:13.5px; margin-bottom:16px;">
                    Insira seu usuário e e-mail cadastrado para receber o código de recuperação.
                </p>
                <form id="form-recuperar" class="col" novalidate>
                    <label class="field">
                        <span class="field-lbl">Usuário</span>
                        <span class="input-wrap">
                            <span class="input-ic"><?= icon('user', ['size' => 16]) ?></span>
                            <input type="text" name="usuario" placeholder="Seu nome de usuário" autocomplete="username" required>
                        </span>
                    </label>
                    <label class="field">
                        <span class="field-lbl">E-mail</span>
                        <span class="input-wrap">
                            <span class="input-ic"><?= icon('mail', ['size' => 16]) ?></span>
                            <input type="email" name="email" placeholder="E-mail cadastrado" autocomplete="email" required>
                        </span>
                    </label>
                    <button type="submit" class="btn btn-primary">
                        <?= icon('mail', ['size' => 16]) ?>
                        Enviar código
                    </button>
                </form>
            </div>

            <!-- ── Step 2: Validar código ───────────────────── -->
            <div class="rec-panel d-none" id="panel-2">
                <p class="card-subtitle" id="subtitle-2" style="color:var(--text-3); font-size:13.5px; margin-bottom:16px;">
                    Insira o código enviado por e-mail. Válido por <strong>15 minutos</strong>.
                </p>
                <form id="form-validar" class="col" novalidate>
                    <label class="field">
                        <span class="field-lbl">Código</span>
                        <span class="input-wrap">
                            <span class="input-ic"><?= icon('lock', ['size' => 16]) ?></span>
                            <input id="code-input" type="text" name="code" style="letter-spacing:.25em; font-weight:700; text-align:center;"
                                   placeholder="XXXXXXXX" maxlength="8" autocomplete="one-time-code" required>
                        </span>
                    </label>
                    <button type="submit" class="btn btn-primary">
                        <?= icon('check', ['size' => 16]) ?>
                        Validar código
                    </button>
                    <button type="button" id="btn-reenviar" class="btn btn-ghost btn-sm">Não recebi o e-mail — reenviar</button>
                </form>
            </div>

            <!-- ── Step 3: Nova senha ───────────────────────── -->
            <div class="rec-panel d-none" id="panel-3">
                <p class="card-subtitle" style="color:var(--text-3); font-size:13.5px; margin-bottom:16px;">
                    Crie uma senha com mínimo <strong>9 caracteres</strong>.
                </p>
                <form id="form-alterar" class="col" novalidate>
                    <label class="field">
                        <span class="field-lbl">Nova senha</span>
                        <span class="input-wrap">
                            <span class="input-ic"><?= icon('lock', ['size' => 16]) ?></span>
                            <input type="password" name="senha" id="nova-senha" placeholder="Nova senha" minlength="9" maxlength="50" required>
                            <button type="button" class="input-ic right pwd-toggle" data-target="nova-senha" aria-label="Ver senha"><?= icon('eye', ['size' => 16]) ?></button>
                        </span>
                    </label>

                    <div id="pwd-strength-wrap" class="pwd-strength-wrap" aria-live="polite">
                        <div class="pwd-strength-bar"><div id="pwd-strength-fill" class="pwd-strength-fill"></div></div>
                        <span id="pwd-strength-label" class="pwd-strength-label"></span>
                    </div>

                    <label class="field">
                        <span class="field-lbl">Repita a senha</span>
                        <span class="input-wrap">
                            <span class="input-ic"><?= icon('lock', ['size' => 16]) ?></span>
                            <input type="password" name="confirm-senha" id="conf-senha" placeholder="Repita a senha" minlength="9" maxlength="50" required>
                            <button type="button" class="input-ic right pwd-toggle" data-target="conf-senha" aria-label="Ver senha"><?= icon('eye', ['size' => 16]) ?></button>
                        </span>
                    </label>
                    <button type="submit" class="btn btn-primary">
                        <?= icon('shield-check', ['size' => 16]) ?>
                        Alterar senha
                    </button>
                </form>
            </div>

        </div>
    </main>

</div>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/recuperar_senha.js"></script>
</body>
</html>
