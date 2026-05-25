<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
    echo json_encode([
        'status'         => 'success',
        'usuario_logado' => true,
        'email'          => $_SESSION['usuario_email'] ?? '',
    ]);
} else {
    echo json_encode([
        'status'         => 'error',
        'usuario_logado' => false,
        'message'        => 'Nenhum usuário logado.',
    ]);
}
