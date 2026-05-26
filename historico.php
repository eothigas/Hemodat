<?php
require_once __DIR__ . '/includes/functions/config.php';
require_auth();

$titulo        = 'Hemodat - Histórico';
$body_class    = 'dashboard-page';
$requer_sessao = true;
require_once __DIR__ . '/includes/other/header.php';

$active = 'historico';
require_once __DIR__ . '/includes/other/nav.php';
?>

<main class="dashboard-main">
    <div class="container">
        <div class="hemodat-card">

            <!-- Cabeçalho -->
            <div class="page-header mb-3">
                <a href="<?= BASE_URL ?>/home.php" title="Voltar">
                    <i class="bi bi-arrow-left-circle"></i>
                </a>
                <div>
                    <h1>Histórico de Movimentações</h1>
                    <p>Entradas e saídas de bolsas de sangue</p>
                </div>
            </div>

            <!-- Filtros -->
            <div class="row g-2 mb-4 align-items-end">
                <div class="col-sm-4">
                    <label class="form-label text-muted small mb-1">Tipo Sanguíneo</label>
                    <div class="input-icon">
                        <i class="bi bi-droplet-fill"></i>
                        <select id="f-tipo" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <?php foreach (TIPOS_VALIDOS as $t): ?>
                                <option value="<?= $t ?>"><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label text-muted small mb-1">Operação</label>
                    <div class="input-icon">
                        <i class="bi bi-arrow-left-right"></i>
                        <select id="f-operacao" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            <option value="entrada">Entrada</option>
                            <option value="saida">Saída</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <button id="limpar-filtros" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-x-circle me-1"></i>Limpar Filtros
                    </button>
                </div>
            </div>

            <!-- Tabela -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabela-historico">
                    <thead style="background:rgba(209,0,0,0.06);">
                        <tr>
                            <th style="width:110px;">Operação</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <th>Data</th>
                            <th>Responsável</th>
                        </tr>
                    </thead>
                    <tbody id="historico-body">
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                                Carregando...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
                <small id="pag-info" class="text-muted"></small>
                <div class="d-flex gap-2">
                    <button id="pag-prev" class="btn btn-outline-danger btn-sm" disabled>
                        <i class="bi bi-chevron-left"></i> Anterior
                    </button>
                    <button id="pag-next" class="btn btn-outline-danger btn-sm" disabled>
                        Próximo <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/padrao/toast.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/main.js"></script>
<script src="<?= BASE_URL ?>/assets/js/custom/historico.js"></script>
<script src="<?= BASE_URL ?>/assets/js/padrao/logout.js"></script>
</body>
</html>
