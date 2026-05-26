<?php
/**
 * nav.php - Navbar HEMODAT v2 (dark navy + logo SVG Pixel Drop)
 * $active: 'home' | 'entrada' | 'saida' | 'relatorio' | 'historico' | 'admin'
 */
$active = $active ?? '';
$B      = BASE_URL;
$nome   = htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário');
$role   = $_SESSION['usuario_role'] ?? 'operador';
?>
<nav class="navbar navbar-expand-md navbar-hemodat py-0" style="min-height:60px;">
    <div class="container">

        <!-- Brand -->
        <a class="navbar-brand py-2" href="<?= $B ?>/home.php">
            <!-- Pixel Drop logo SVG -->
            <svg width="26" height="28" viewBox="0 0 26 28" fill="none" aria-hidden="true">
                <rect x="0"   y="20" width="3.5" height="8"  rx="1.2" fill="white" opacity="0.40"/>
                <rect x="4.5" y="13" width="3.5" height="15" rx="1.2" fill="white" opacity="0.58"/>
                <rect x="9"   y="6"  width="3.5" height="22" rx="1.2" fill="white" opacity="0.80"/>
                <rect x="13.5" y="2" width="3.5" height="26" rx="1.2" fill="white"/>
                <rect x="18"  y="7"  width="3.5" height="21" rx="1.2" fill="white" opacity="0.78"/>
                <rect x="22.5" y="14" width="3.5" height="14" rx="1.2" fill="white" opacity="0.55"/>
            </svg>
            HEMODAT
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
                        <i class="bi bi-arrow-down-circle me-1"></i>Entrada
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3 <?= $active === 'saida'     ? 'active-page' : '' ?>"
                       href="<?= $B ?>/saida.php">
                        <i class="bi bi-arrow-up-circle me-1"></i>Saída
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

                <!-- Divider + nome -->
                <li class="nav-item d-none d-md-flex align-items-center ms-2"
                    style="height:28px; border-left:1px solid rgba(255,255,255,.15);">
                </li>

                <li class="nav-item d-none d-md-flex align-items-center px-2">
                    <span style="font-size:12px; color:rgba(255,255,255,.55); font-weight:500;">
                        <i class="bi bi-person-circle me-1"></i><?= $nome ?>
                    </span>
                </li>

                <li class="nav-item">
                    <span id="logout" role="button" class="btn btn-sm px-3" style="min-height:34px;">
                        <i class="bi bi-box-arrow-right me-1"></i>Sair
                    </span>
                </li>

            </ul>
        </div>
    </div>
</nav>
