<?php
/**
 * nav.php — Navegação compartilhada entre páginas autenticadas.
 * Defina $active antes de incluir: 'home' | 'entrada' | 'saida' | 'relatorio'
 *
 * Uso:
 *   $active = 'home';
 *   require_once __DIR__ . '/../../includes/other/nav.php';
 */
$active = $active ?? '';
?>
<nav id="menu-up">
    <div id="btn-home">
        <i class="bi bi-list"></i>
    </div>

    <i class="open bi bi-x"></i>

    <div id="list">
        <ul>
            <a href="/home.php">
                <li class="<?= $active === 'home'      ? 'nav-active' : '' ?>">HOME</li>
            </a>
            <a href="/entrada.php">
                <li class="<?= $active === 'entrada'   ? 'nav-active' : '' ?>">ENTRADA</li>
            </a>
            <a href="/saida.php">
                <li class="<?= $active === 'saida'     ? 'nav-active' : '' ?>">SAÍDA</li>
            </a>
            <a href="/relatorio.php">
                <li class="<?= $active === 'relatorio' ? 'nav-active' : '' ?>">RELATÓRIO</li>
            </a>
            <li id="logout">SAIR</li>
        </ul>
    </div>

    <div id="logo">
        <img src="/imagens/logo/logo.png" width="130px" height="40px" alt="Hemodat">
    </div>
</nav>
