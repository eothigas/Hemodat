<?php
/**
 * header.php — <head> compartilhado de todas as páginas.
 *
 * Variáveis aceitas (defina antes de incluir):
 *   $titulo       string   Título da aba
 *   $css_pagina   string   Path relativo ao CSS da página (ex: /assets/css/paginas/login.css)
 *   $requer_sessao bool    true = injeta verificar_sessao.js
 *   $head_extras  array    Tags HTML extras no <head> (ex: ['<script src="..."></script>'])
 */

require_once __DIR__ . '/../functions/config.php';

$titulo        = $titulo        ?? 'Hemodat';
$css_pagina    = $css_pagina    ?? '';
$requer_sessao = $requer_sessao ?? false;
$head_extras   = $head_extras   ?? [];

$B = BASE_URL; // atalho local para uso nos atributos
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS padrão -->
    <link rel="stylesheet" href="<?= $B ?>/assets/css/padrao.css">
    <link rel="stylesheet" href="<?= $B ?>/assets/css/componentes/toast.css">

    <!-- CSS específico da página -->
    <?php if ($css_pagina): ?>
        <link rel="stylesheet" href="<?= $B . htmlspecialchars($css_pagina) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" href="<?= $B ?>/imagens/favicon/logo.ico" type="image/ico">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tags extras (Chart.js, fontes, etc.) -->
    <?php foreach ($head_extras as $extra): ?>
        <?= $extra . "\n" ?>
    <?php endforeach; ?>

    <title><?= htmlspecialchars($titulo) ?></title>

    <!-- BASE_URL disponível para todos os scripts da página -->
    <script>const BASE_URL = '<?= $B ?>';</script>
</head>
<body>

<?php if ($requer_sessao): ?>
    <script src="<?= $B ?>/assets/js/padrao/verificar_sessao.js"></script>
<?php endif; ?>
