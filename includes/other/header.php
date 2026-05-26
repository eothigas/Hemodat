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

$titulo        = $titulo        ?? 'Hemodat';
$css_pagina    = $css_pagina    ?? '';
$body_class    = $body_class    ?? '';
$requer_sessao = $requer_sessao ?? false;
$head_extras   = $head_extras   ?? [];

$B = BASE_URL;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts: Inter + Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap">

    <!-- Bootstrap 5.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS global (overrides Bootstrap) -->
    <link rel="stylesheet" href="<?= $B ?>/assets/css/padrao.css">
    <link rel="stylesheet" href="<?= $B ?>/assets/css/componentes/toast.css">

    <!-- CSS específico da página -->
    <?php if ($css_pagina): ?>
        <link rel="stylesheet" href="<?= $B . htmlspecialchars($css_pagina) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" href="<?= $B ?>/imagens/favicon/logo.ico" type="image/x-icon">

    <!-- Tags extras (Chart.js, etc.) -->
    <?php foreach ($head_extras as $extra): ?>
        <?= $extra . "\n" ?>
    <?php endforeach; ?>

    <title><?= htmlspecialchars($titulo) ?></title>

    <!-- BASE_URL disponível globalmente nos scripts -->
    <script>const BASE_URL = '<?= $B ?>';</script>

    <!-- Bootstrap JS (defer para não bloquear render) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body class="<?= htmlspecialchars($body_class) ?>">

<?php if ($requer_sessao): ?>
    <script src="<?= $B ?>/assets/js/padrao/verificar_sessao.js"></script>
<?php endif; ?>
