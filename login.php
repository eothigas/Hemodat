<?php
$titulo     = 'HEMODAT - Login';
$body_class = 'auth-page';
require_once __DIR__ . '/includes/other/header.php';
?>

<div class="login-shell">

    <!-- ── Painel esquerdo ─────────────────────────────────── -->
    <aside class="login-aside">
        <div class="login-aside-bg"></div>
        <div class="login-aside-content">
            <?= logo_horizontal(28) ?>

            <div class="login-hero">
                <span class="lh-tag"><?= icon('droplet', ['size' => 11]) ?> HEMODAT</span>
                <div>
                    <div style="color:#fff; font-size:13px; margin-bottom:4px;">
                        Gestão de estoque de sangue
                    </div>
                    <div class="lh-note">
                        Controle preciso de entradas, saídas e validade de bolsas — decisões
                        em segundos, com alertas automáticos de vencimento e estoque crítico.
                    </div>
                </div>
            </div>

            <div class="login-aside-quote">
                <p>"Onde cada bolsa de sangue importa, organização e velocidade salvam vidas."</p>
                <cite>— Equipe técnica · HEMODAT</cite>
            </div>

            <div class="login-trust">
                <span class="lt-item"><?= icon('shield-check', ['size' => 14]) ?> Acesso controlado</span>
                <span class="lt-item"><?= icon('shield-check', ['size' => 14]) ?> Dados criptografados</span>
            </div>
        </div>
    </aside>

    <!-- ── Painel direito - formulário ─────────────────────── -->
    <main class="login-main">
        <div class="login-card">
            <div class="row between">
                <span class="badge badge-brand"><span class="dot"></span>Sistema seguro</span>
                <button id="btn-tema" class="tb-icon-btn" title="Modo escuro"><?= icon('moon', ['size' => 17]) ?></button>
            </div>

            <div class="login-head">
                <h1>Bem-vindo de volta</h1>
                <p>Acesse o painel HEMODAT com suas credenciais.</p>
            </div>

            <form id="login" action="<?= BASE_URL ?>/includes/actions/auth.php?action=login" method="post" class="col">
                <label class="field" for="email">
                    <span class="field-lbl">E-mail</span>
                    <span class="input-wrap">
                        <span class="input-ic"><?= icon('mail', ['size' => 16]) ?></span>
                        <input type="email" id="email" name="email" placeholder="seu@email.com"
                               autocomplete="email" required>
                    </span>
                </label>

                <label class="field" for="senha">
                    <span class="field-lbl">Senha</span>
                    <span class="input-wrap">
                        <span class="input-ic"><?= icon('lock', ['size' => 16]) ?></span>
                        <input type="password" id="senha" name="senha" placeholder="Digite sua senha"
                               autocomplete="current-password" required>
                        <button type="button" class="input-ic right pwd-toggle" aria-label="Mostrar senha">
                            <?= icon('eye', ['size' => 16]) ?>
                        </button>
                    </span>
                </label>

                <div class="row between" style="margin-top:-4px;">
                    <span></span>
                    <a href="<?= BASE_URL ?>/forgot_password" style="color:var(--brand); font-size:13px; font-weight:500;">
                        Esqueci minha senha
                    </a>
                </div>

                <button type="submit" id="logar" class="btn btn-primary btn-lg btn-block">
                    Entrar
                    <?= icon('chevron-right', ['size' => 16]) ?>
                </button>
            </form>
        </div>
    </main>

</div>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/login.js"></script>
</body>
</html>
