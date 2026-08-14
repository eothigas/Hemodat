<?php
/**
 * header.php - <head> compartilhado de todas as páginas.
 *
 * Variáveis aceitas (defina antes de incluir):
 *   $titulo        string   Título da aba
 *   $css_pagina    string   Path relativo ao CSS extra da página (opcional)
 *   $body_class    string   Classes adicionais no <body>
 *   $requer_sessao bool     true = injeta verificar_sessao.js
 *   $head_extras   array    Tags HTML extras no <head> (ex: ['<script src="..."></script>'])
 */

require_once __DIR__ . '/../functions/config.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/logos.php';

$titulo        = $titulo        ?? 'Hemodat';
$css_pagina    = $css_pagina    ?? '';
$body_class    = $body_class    ?? '';
$requer_sessao = $requer_sessao ?? false;
$head_extras   = $head_extras   ?? [];

$B = BASE_URL;

// ── Minificação HTML ──────────────────────────────────────────────────────────
if (!ob_get_level()) {
    ob_start(function (string $html): string {
        // Remove comentários HTML (preserva condicionais IE <!--[if...)
        $html = preg_replace('/<!--(?!\[if\s)[\s\S]*?-->/U', '', $html);
        // Colapsa espaços redundantes entre tags
        $html = preg_replace('/>\s{2,}</s', '> <', $html);
        // Remove linhas em branco
        $html = preg_replace('/^\s*$/m', '', $html);
        return $html;
    });
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts: Inter + Plus Jakarta Sans + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap">

    <!-- Design system HEMODAT -->
    <link rel="stylesheet" href="<?= $B ?>/assets/css/design-system.css">
    <link rel="stylesheet" href="<?= $B ?>/assets/css/componentes/toast.css">

    <!-- CSS específico da página -->
    <?php if ($css_pagina): ?>
        <link rel="stylesheet" href="<?= $B . htmlspecialchars($css_pagina) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" href="<?= $B ?>/imagens/favicon/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="<?= $B ?>/imagens/favicon/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="<?= $B ?>/imagens/favicon/logo.ico" type="image/x-icon">

    <!-- Tags extras (Chart.js, etc.) -->
    <?php foreach ($head_extras as $extra): ?>
        <?= $extra . "\n" ?>
    <?php endforeach; ?>

    <title><?= htmlspecialchars($titulo) ?></title>

    <!-- BASE_URL disponível globalmente nos scripts -->
    <script>const BASE_URL = '<?= $B ?>';</script>

    <!-- Anti-FOUC: aplica tema dark ANTES do primeiro frame -->
    <script>
        (function(){
            var t = localStorage.getItem('hemodat_theme');
            if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            if (t === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
        })();
    </script>

    <!-- Dark mode (não-defer: precisa rodar cedo) -->
    <script src="<?= $B ?>/assets/js/padrao/darkmode.js"></script>

    <!-- Segurança: console warning + bloqueia atalhos em produção -->
    <script src="<?= $B ?>/assets/js/padrao/security.js" defer></script>
</head>
<body class="<?= htmlspecialchars($body_class) ?>">

<?php if ($requer_sessao): ?>
    <script src="<?= $B ?>/assets/js/padrao/verificar_sessao.js"></script>
<?php endif; ?>
<?php if (($body_class ?? '') === 'dashboard-page'): ?>
<div class="app">
<?php endif; ?>
