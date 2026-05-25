<?php
session_start();

require_once __DIR__ . '/../includes/functions/config.php';
require_once __DIR__ . '/../includes/functions/csrf.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido.']);
    exit;
}

csrf_validate();

$pdo   = db_connect();
$nome  = trim($_POST['nome']  ?? '');
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (empty($nome) || empty($email) || empty($senha)) {
    echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'E-mail inválido.']);
    exit;
}

if (strlen($senha) < 9) {
    echo json_encode(['status' => 'error', 'message' => 'A senha deve ter pelo menos 9 caracteres (8 alfanuméricos + 1 especial).']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);

if ($stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'O e-mail informado já está cadastrado.']);
    exit;
}

$senhaHash = password_hash($senha, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
$stmt->execute([':nome' => $nome, ':email' => $email, ':senha' => $senhaHash]);

echo json_encode(['status' => 'success', 'message' => 'Usuário cadastrado com sucesso!']);
