<?php
session_start();

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido.']);
    exit;
}

if (!isset($_SESSION['usuario_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sessão inválida. Por favor, tente novamente.']);
    exit;
}

$pdo         = db_connect();
$email       = $_SESSION['usuario_email'];
$senha       = trim($_POST['senha']         ?? '');
$confirmSenha = trim($_POST['confirm-senha'] ?? '');

if (empty($senha) || empty($confirmSenha)) {
    echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos.']);
    exit;
}

if ($senha !== $confirmSenha) {
    echo json_encode(['status' => 'error', 'message' => 'As senhas não coincidem.']);
    exit;
}

// Padronizado: mínimo 9 caracteres (8 alfanuméricos + 1 especial)
if (strlen($senha) < 9) {
    echo json_encode(['status' => 'error', 'message' => 'A senha deve ter pelo menos 9 caracteres (8 alfanuméricos + 1 especial).']);
    exit;
}

$senhaHash = password_hash($senha, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE email = :email");
$stmt->execute([':senha' => $senhaHash, ':email' => $email]);

session_unset();
session_destroy();

echo json_encode([
    'status'   => 'success',
    'message'  => 'Senha alterada com sucesso! Faça login novamente.',
    'redirect' => './login.html',
]);
