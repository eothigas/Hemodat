<?php
/**
 * csrf.php — Geração e validação de tokens CSRF.
 *
 * GET  /php/csrf.php  → retorna {'token': '<hex>'} e armazena na sessão.
 * Inclua este arquivo e chame csrf_validate() em endpoints POST.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_generate(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_validate(): void {
    $token = $_POST['csrf_token'] ?? '';
    $stored = $_SESSION['csrf_token'] ?? '';

    if (empty($token) || empty($stored) || !hash_equals($stored, $token)) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Token de segurança inválido. Recarregue a página.']);
        exit;
    }
}

// Endpoint GET: retorna token para o JS incluir nos formulários
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    echo json_encode(['token' => csrf_generate()]);
    exit;
}
