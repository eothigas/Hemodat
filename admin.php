<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

// Proteção: apenas admin
if (($_SESSION['usuario_role'] ?? '') !== 'admin') {
    header('Location: ' . BASE_URL . '/home.php');
    exit;
}

$titulo        = 'Hemodat - Admin';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active = 'admin';
require_once __DIR__ . '/includes/other/nav.php';
?>

<main class="dashboard-main">
    <div class="container">

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-0" id="adminTabs" role="tablist"
            style="border-bottom:none;">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="tab-usuarios-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-usuarios"
                        type="button">
                    <i class="bi bi-people me-1"></i>Usuários
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="tab-estoque-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-estoque"
                        type="button">
                    <i class="bi bi-sliders me-1"></i>Estoque Mínimo
                </button>
            </li>
        </ul>

        <div class="tab-content">

            <!-- ── Aba Usuários ─────────────────────────────────────────── -->
            <div class="tab-pane fade show active hemodat-card"
                 style="border-radius:0 16px 16px 16px;"
                 id="tab-usuarios">

                <div class="page-header mb-3">
                    <div>
                        <h1>Gerenciar Usuários</h1>
                        <p>Altere permissões de acesso dos usuários cadastrados</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:rgba(209,0,0,0.06);">
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Permissão</th>
                                <th style="width:140px;">Ação</th>
                            </tr>
                        </thead>
                        <tbody id="usuarios-body">
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <div class="spinner-border spinner-border-sm text-danger me-2"></div>
                                    Carregando...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── Aba Estoque Mínimo ───────────────────────────────────── -->
            <div class="tab-pane fade hemodat-card"
                 style="border-radius:0 16px 16px 16px;"
                 id="tab-estoque">

                <div class="page-header mb-3">
                    <div>
                        <h1>Estoque Mínimo por Tipo</h1>
                        <p>Define o limite abaixo do qual um alerta é exibido no dashboard</p>
                    </div>
                </div>

                <form id="form-estoque-min">
                    <div class="row g-3" id="estoque-min-campos">
                        <div class="col-12 text-center text-muted py-3">
                            <div class="spinner-border spinner-border-sm text-danger me-2"></div>
                            Carregando...
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-floppy me-1"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>

        </div><!-- /tab-content -->
    </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/main.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/admin.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
</body>
</html>
