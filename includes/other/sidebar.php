<?php
/**
 * sidebar.php - Sidebar + Topbar HEMODAT (design system v2).
 * Vars esperadas:
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

$NAV_PRIMARY = [
    ['key' => 'home',      'href' => 'home',      'label' => 'Dashboard',  'icon' => 'dashboard'],
    ['key' => 'entrada',   'href' => 'entrada',   'label' => 'Entrada',    'icon' => 'arrow-down'],
    ['key' => 'saida',     'href' => 'saida',     'label' => 'Saída',      'icon' => 'arrow-up'],
    ['key' => 'relatorio', 'href' => 'relatorio', 'label' => 'Relatórios','icon' => 'chart'],
    ['key' => 'historico', 'href' => 'historico', 'label' => 'Histórico', 'icon' => 'clock'],
];

// Contagem de bolsas vencendo (alerta na sidebar)
$vencendo_count = 0;
try {
    $pdo_sb  = db_connect();
    $stmt_sb = $pdo_sb->prepare(
        "SELECT COUNT(*) FROM bolsas_sangue
         WHERE quantidade > 0
           AND data_validade BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :dias DAY)"
    );
    $stmt_sb->execute([':dias' => DIAS_ALERTA_VENCIMENTO]);
    $vencendo_count = (int) $stmt_sb->fetchColumn();
} catch (Exception $ignored) {}
?>

<!-- ── Sidebar ─────────────────────────────────────────────── -->
<aside class="sidebar">

    <a class="sb-brand" href="<?= $B ?>/home" style="text-decoration:none;">
        <?= logo_horizontal(26) ?>
    </a>

    <nav class="sb-nav">
        <div class="sb-section-label">Operação</div>
        <?php foreach ($NAV_PRIMARY as $it): ?>
            <a href="<?= $B ?>/<?= $it['href'] ?>"
               class="sb-item<?= $active === $it['key'] ? ' active' : '' ?>">
                <?= icon($it['icon'], ['size' => 17, 'class' => 'sb-ic']) ?>
                <span><?= $it['label'] ?></span>
            </a>
        <?php endforeach; ?>

        <div class="sb-section-label">Sistema</div>
        <a href="<?= $B ?>/admin"
           class="sb-item<?= $active === 'admin' ? ' active' : '' ?>">
            <?= icon('settings', ['size' => 17, 'class' => 'sb-ic']) ?>
            <span>Configurações</span>
            <?php if ($role !== 'admin'): ?>
                <?= icon('lock', ['size' => 12, 'class' => 'sb-badge']) ?>
            <?php endif; ?>
        </a>
    </nav>

    <div class="sb-foot">
        <?php if ($vencendo_count > 0): ?>
        <div class="alert alert-amber" style="margin-bottom:10px; font-size:12px;">
            <?= icon('alert', ['size' => 16]) ?>
            <div>
                <div style="font-weight:600;">
                    <?= $vencendo_count ?> bolsa<?= $vencendo_count > 1 ? 's' : '' ?> próxima<?= $vencendo_count > 1 ? 's' : '' ?> do vencimento
                </div>
                <div style="opacity:.8; margin-top:2px;">Revisar antes de <?= DIAS_ALERTA_VENCIMENTO * 24 ?>h</div>
            </div>
        </div>
        <?php endif; ?>

        <div class="sb-user">
            <span class="sb-avatar"><?= strtoupper(mb_substr($nome, 0, 1)) ?></span>
            <div class="sb-user-meta">
                <span class="sb-user-name"><?= $nome ?></span>
                <span class="sb-user-role"><?= ucfirst($role) ?></span>
            </div>
            <button id="logout" class="tb-icon-btn" style="margin-left:auto;" title="Sair">
                <?= icon('logout', ['size' => 16]) ?>
            </button>
        </div>
    </div>

</aside>

<!-- ── Main area ───────────────────────────────────────────── -->
<div class="main">

    <header class="topbar">
        <div>
            <div class="tb-title"><?= htmlspecialchars($page_title) ?></div>
            <?php if ($page_subtitle): ?>
                <div class="small muted" style="margin-top:2px;"><?= htmlspecialchars($page_subtitle) ?></div>
            <?php endif; ?>
        </div>
        <div class="tb-spacer"></div>
        <div class="tb-search">
            <?= icon('search', ['size' => 15]) ?>
            <input placeholder="Buscar bolsas, doadores, lotes…" readonly>
            <kbd>⌘K</kbd>
        </div>
        <div class="tb-actions">
            <button id="btn-tema" class="tb-icon-btn" title="Modo escuro"><?= icon('moon', ['size' => 17]) ?></button>
            <button class="tb-icon-btn" title="Alertas">
                <?= icon('bell', ['size' => 17]) ?>
                <?php if ($vencendo_count > 0): ?><span class="dot"></span><?php endif; ?>
            </button>
            <button class="tb-icon-btn" title="Ajuda"><?= icon('help', ['size' => 17]) ?></button>
        </div>
    </header>
