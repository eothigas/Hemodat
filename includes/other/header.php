<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS padrão -->
    <link rel="stylesheet" href="/assets/css/padrao.css">
    <link rel="stylesheet" href="/assets/css/componentes/toast.css">

    <!-- CSS específico da página -->
    <?php if (!empty($css_pagina)): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css_pagina) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" href="/imagens/favicon/logo.ico" type="image/ico">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tags extras (ex: Chart.js, fontes, etc.) -->
    <?php if (!empty($head_extras) && is_array($head_extras)): ?>
        <?php foreach ($head_extras as $extra): ?>
            <?= $extra . "\n" ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <title><?= htmlspecialchars($titulo ?? 'Hemodat') ?></title>
</head>
<body>

<?php if (!empty($requer_sessao)): ?>
    <script src="/assets/js/padrao/verificar_sessao.js"></script>
<?php endif; ?>
