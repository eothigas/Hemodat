<?php
/**
 * nav.php - Bootstrap navbar compartilhada (páginas autenticadas).
 * Defina $active antes de incluir: 'home' | 'entrada' | 'saida' | 'relatorio' | 'historico' | 'admin'
 */
$active    = $active    ?? '';
$B         = BASE_URL;
$nome      = htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário');
$role      = $_SESSION['usuario_role'] ?? 'operador';
?>
<nav class="navbar navbar-expand-md navbar-hemodat py-0" style="min-height:60px;">
    <div class="container">

        <!-- Brand / Logo -->
        <a class="navbar-brand py-2" href="<?= $B ?>/home.php">
            <img src="<?= $B ?>/imagens/logo/logo.png" height="38" alt="Hemodat">
        </a>

        <!-- Toggler mobile -->
        <button class="navbar-toggler border-0 ms-auto me-0"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navMenu"
                aria-controls="navMenu"
                aria-expanded="false"
                aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Links -->
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-md-center gap-md-1 py-2 py-md-0">

                <li class="nav-item">
                    <a class="nav-link px-3 <?= $active === 'home'      ? 'active-page' : '' ?>"
                       href="<?= $B ?>/home.php">
                        <i class="bi bi-house me-1"></i>Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3 <?= $active === 'entrada'   ? 'active-page' : '' ?>"
                       href="<?= $B ?>/entrada.php">
                        <i class="bi bi-box-arrow-in-down me-1"></i>Entrada
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3 <?= $active === 'saida'     ? 'active-page' : '' ?>"
                       href="<?= $B ?>/saida.php">
                        <i class="bi bi-box-arrow-up me-1"></i>Saída
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3 <?= $active === 'relatorio' ? 'active-page' : '' ?>"
                       href="<?= $B ?>/relatorio.php">
                        <i class="bi bi-bar-chart-line me-1"></i>Relatório
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3 <?= $active === 'historico' ? 'active-page' : '' ?>"
                       href="<?= $B ?>/historico.php">
                        <i class="bi bi-clock-history me-1"></i>Histórico
                    </a>
                </li>

                <?php if ($role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link px-3 <?= $active === 'admin' ? 'active-page' : '' ?>"
                       href="<?= $B ?>/admin.php">
                        <i class="bi bi-shield-lock me-1"></i>Admin
                    </a>
                </li>
                <?php endif; ?>

                <!-- Nome do usuário -->
                <li class="nav-item ms-md-1 d-flex align-items-center">
                    <span class="text-white opacity-75 small px-2 d-none d-md-inline">
                        <i class="bi bi-person-circle me-1"></i><?= $nome ?>
                    </span>
                </li>

                <li class="nav-item ms-md-1">
                    <span id="logout" role="button"
                          class="btn btn-outline-light btn-sm px-3">
                        <i class="bi bi-box-arrow-right me-1"></i>Sair
                    </span>
                </li>

            </ul>
        </div>
    </div>
</nav>
