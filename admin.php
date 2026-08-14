<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

if (($_SESSION['usuario_role'] ?? '') !== 'admin') {
    header('Location: ' . BASE_URL . '/home');
    exit;
}

$titulo        = 'HEMODAT - Configurações';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active        = 'admin';
$page_title    = 'Configurações';
$page_subtitle = 'Usuários e parâmetros do sistema';
require_once __DIR__ . '/includes/other/sidebar.php';
?>

<div class="page">
    <div class="page-head">
        <div>
            <h1>Configurações</h1>
            <p>Usuários e parâmetros do sistema.</p>
        </div>
    </div>

    <div class="tabs" id="admin-tabs">
        <button class="tab active" data-tab="usuarios">Usuários</button>
        <button class="tab" data-tab="estoque">Estoque mínimo</button>
    </div>

    <!-- ── Aba Usuários ─────────────────────────────────────── -->
    <div class="card" id="tab-usuarios" data-panel>
        <div class="card-head"><div><h3>Usuários</h3><div class="ch-sub">Altere as permissões de acesso dos usuários cadastrados</div></div></div>
        <div class="tbl-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Permissão</th>
                        <th style="width:140px;">Ação</th>
                    </tr>
                </thead>
                <tbody id="usuarios-body">
                    <tr><td colspan="4" class="text-center small muted" style="padding:24px;">Carregando…</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ── Aba Estoque Mínimo ───────────────────────────────── -->
    <div class="card d-none" id="tab-estoque" data-panel>
        <div class="card-head"><div><h3>Estoque mínimo</h3><div class="ch-sub">Define o limite abaixo do qual um alerta é exibido no dashboard</div></div></div>
        <form id="form-estoque-min" class="card-pad">
            <div class="grid grid-4" id="estoque-min-campos">
                <div class="small muted">Carregando…</div>
            </div>
            <div class="row" style="justify-content:flex-end; margin-top:20px;">
                <button type="submit" class="btn btn-primary">
                    <?= icon('check', ['size' => 16]) ?>
                    Salvar
                </button>
            </div>
        </form>
    </div>

</div><!-- /page -->
</div><!-- /main -->
</div><!-- /app -->

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/admin.js"></script>
</body>
</html>
