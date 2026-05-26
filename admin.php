<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

if (($_SESSION['usuario_role'] ?? '') !== 'admin') {
    header('Location: ' . BASE_URL . '/home');
    exit;
}

$titulo        = 'HEMODAT — Configurações';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active        = 'admin';
$page_title    = 'Configurações';
$page_subtitle = 'Usuários e parâmetros do sistema';
require_once __DIR__ . '/includes/other/sidebar.php';
?>

<div class="app-content">

    <!-- ── Tabs nav ─────────────────────────────────────────── -->
    <div class="content-card p-0 overflow-hidden">
        <ul class="nav nav-tabs px-4 pt-3" id="adminTabs" role="tablist"
            style="border-bottom:1px solid var(--hemo-border);">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="tab-usuarios-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-usuarios" type="button">
                    <i class="bi bi-people me-1"></i>Usuários
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="tab-estoque-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-estoque" type="button">
                    <i class="bi bi-sliders me-1"></i>Estoque Mínimo
                </button>
            </li>
        </ul>

        <div class="tab-content p-4">

            <!-- ── Aba Usuários ─────────────────────────────── -->
            <div class="tab-pane fade show active" id="tab-usuarios">
                <p class="text-muted small mb-3">Altere as permissões de acesso dos usuários cadastrados.</p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
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
                                    <div class="spinner-border spinner-border-sm me-2"></div>
                                    Carregando…
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── Aba Estoque Mínimo ───────────────────────── -->
            <div class="tab-pane fade" id="tab-estoque">
                <p class="text-muted small mb-3">
                    Define o limite abaixo do qual um alerta é exibido no dashboard.
                </p>
                <form id="form-estoque-min">
                    <div class="row g-3" id="estoque-min-campos">
                        <div class="col-12 text-center text-muted py-3">
                            <div class="spinner-border spinner-border-sm me-2"></div>
                            Carregando…
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

</div><!-- /app-content -->
</div><!-- /app-main -->
</div><!-- /app-shell -->

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/admin.js"></script>
</body>
</html>
