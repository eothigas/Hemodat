<?php
/**
 * nav.php — Navegação compartilhada (páginas autenticadas).
 * Defina $active antes de incluir: 'home' | 'entrada' | 'saida' | 'relatorio'
 * BASE_URL já disponível via header.php (config.php incluído lá).
 */
$active = $active ?? '';
$B      = BASE_URL;
?>
<nav id="menu-up">
    <div id="btn-home">
        <i class="bi bi-list"></i>
    </div>

    <i class="open bi bi-x"></i>

    <div id="list">
        <ul>
            <a href="<?= $B ?>/home.php">
                <li class="<?= $active === 'home'      ? 'nav-active' : '' ?>">HOME</li>
            </a>
            <a href="<?= $B ?>/entrada.php">
                <li class="<?= $active === 'entrada'   ? 'nav-active' : '' ?>">ENTRADA</li>
            </a>
            <a href="<?= $B ?>/saida.php">
                <li class="<?= $active === 'saida'     ? 'nav-active' : '' ?>">SAÍDA</li>
            </a>
            <a href="<?= $B ?>/relatorio.php">
                <li class="<?= $active === 'relatorio' ? 'nav-active' : '' ?>">RELATÓRIO</li>
            </a>
            <li id="logout">SAIR</li>
        </ul>
    </div>

    <div id="logo">
        <img src="<?= $B ?>/imagens/logo/logo.png" width="130px" height="40px" alt="Hemodat">
    </div>
</nav>
