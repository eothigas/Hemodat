<?php
/**
 * sidebar.php — Sidebar fixa HEMODAT v2
 * Vars esperadas (defina antes de incluir):
 *   $active        string  'home'|'entrada'|'saida'|'relatorio'|'historico'|'admin'
 *   $page_title    string  Título exibido na topbar
 *   $page_subtitle string  Subtítulo na topbar
 */
$active         = $active         ?? 'home';
$page_title     = $page_title     ?? 'HEMODAT';
$page_subtitle  = $page_subtitle  ?? '';
$B              = BASE_URL;
$nome           = htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário');
$role           = $_SESSION['usuario_role'] ?? 'operador';

// Iniciais do usuário para o avatar
$partes  = explode(' ', trim($nome));
$iniciais = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));

// Contagem de bolsas vencendo (sidebar alert)
$vencendo_count = 0;
try {
    $pdo_sb   = db_connect();
    $stmt_sb  = $pdo_sb->prepare(
        "SELECT COUNT(*) FROM bolsas_sangue
         WHERE quantidade > 0
           AND data_validade BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :dias DAY)"
    );
    $stmt_sb->execute([':dias' => DIAS_ALERTA_VENCIMENTO]);
    $vencendo_count = (int) $stmt_sb->fetchColumn();
} catch (Exception $ignored) {}
?>

<!-- ── Sidebar ─────────────────────────────────────────────── -->
<aside class="app-sidebar">

    <!-- Logo -->
    <a class="sidebar-logo" href="<?= $B ?>/home.php">
        <img src="<?= $B ?>/imagens/logo/logo.png" height="28" alt="HEMODAT"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <!-- Fallback text logo -->
        <span style="display:none; align-items:center; gap:8px;">
            <svg width="22" height="22" viewBox="0 0 26 28" fill="none">
                <rect x="0"    y="20" width="3.5" height="8"  rx="1.2" fill="#DC2626" opacity="0.6"/>
                <rect x="4.5"  y="13" width="3.5" height="15" rx="1.2" fill="#DC2626" opacity="0.75"/>
                <rect x="9"    y="6"  width="3.5" height="22" rx="1.2" fill="#DC2626" opacity="0.88"/>
                <rect x="13.5" y="2"  width="3.5" height="26" rx="1.2" fill="#DC2626"/>
                <rect x="18"   y="7"  width="3.5" height="21" rx="1.2" fill="#DC2626" opacity="0.85"/>
                <rect x="22.5" y="14" width="3.5" height="14" rx="1.2" fill="#DC2626" opacity="0.65"/>
            </svg>
            <span class="sidebar-logo-text">HEMODAT</span>
        </span>
    </a>

    <!-- Nav -->
    <nav class="sidebar-nav">
        <div class="sidebar-section">Operação</div>

        <a href="<?= $B ?>/home"
           class="sidebar-item <?= $active === 'home' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i>
            Dashboard
        </a>

        <a href="<?= $B ?>/entrada"
           class="sidebar-item <?= $active === 'entrada' ? 'active' : '' ?>">
            <i class="bi bi-arrow-down-circle"></i>
            Entrada
        </a>

        <a href="<?= $B ?>/saida"
           class="sidebar-item <?= $active === 'saida' ? 'active' : '' ?>">
            <i class="bi bi-arrow-up-circle"></i>
            Saída
        </a>

        <a href="<?= $B ?>/relatorio"
           class="sidebar-item <?= $active === 'relatorio' ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-line"></i>
            Relatórios
        </a>

        <a href="<?= $B ?>/historico"
           class="sidebar-item <?= $active === 'historico' ? 'active' : '' ?>">
            <i class="bi bi-clock-history"></i>
            Histórico
        </a>

        <div class="sidebar-section">Sistema</div>

        <a href="<?= $B ?>/admin"
           class="sidebar-item <?= $active === 'admin' ? 'active' : '' ?>">
            <i class="bi bi-gear"></i>
            Configurações
            <?php if ($role !== 'admin'): ?>
                <i class="bi bi-lock ms-auto" style="font-size:11px; opacity:.5;"></i>
            <?php endif; ?>
        </a>

    </nav>

    <!-- Alerta vencimento -->
    <?php if ($vencendo_count > 0): ?>
    <div class="sidebar-alert">
        <div class="sidebar-alert-title">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= $vencendo_count ?> bolsa<?= $vencendo_count > 1 ? 's' : '' ?> próxima<?= $vencendo_count > 1 ? 's' : '' ?> do vencimento
        </div>
        <div class="sidebar-alert-sub">Revisar antes de <?= DIAS_ALERTA_VENCIMENTO * 24 ?>h</div>
    </div>
    <?php endif; ?>

    <!-- Usuário -->
    <div class="sidebar-user">
        <div class="sidebar-user-avatar"><?= $iniciais ?></div>
        <div style="min-width:0;">
            <div class="sidebar-user-name"><?= $nome ?></div>
            <div class="sidebar-user-role"><?= ucfirst($role) ?></div>
        </div>
        <button id="logout" class="sidebar-user-logout" title="Sair">
            <i class="bi bi-box-arrow-right"></i>
        </button>
    </div>

</aside>

<!-- ── Main area ───────────────────────────────────────────── -->
<div class="app-main">

    <!-- Topbar -->
    <header class="app-topbar">
        <div class="topbar-title">
            <h2><?= htmlspecialchars($page_title) ?></h2>
            <?php if ($page_subtitle): ?>
                <p><?= htmlspecialchars($page_subtitle) ?></p>
            <?php endif; ?>
        </div>

        <!-- Search -->
        <div class="topbar-search">
            <div class="topbar-search-wrap">
                <i class="bi bi-search topbar-search-icon"></i>
                <input type="text" class="topbar-search-input"
                       placeholder="Buscar bolsas, doadores, lotes…"
                       readonly>
                <span class="topbar-search-kbd">⌘K</span>
            </div>
        </div>

        <!-- Ícones -->
        <div class="topbar-actions">
            <button id="btn-tema" class="topbar-icon-btn" title="Modo escuro"><i class="bi bi-moon"></i></button>
            <button class="topbar-icon-btn topbar-notif-badge" title="Alertas">
                <i class="bi bi-bell"></i>
            </button>
            <button class="topbar-icon-btn" title="Ajuda"><i class="bi bi-question-circle"></i></button>
        </div>
    </header>

<!-- app-content aberto pela página -->
