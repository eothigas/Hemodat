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
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (empty($email) || empty($senha)) {
    echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, senha FROM usuarios WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && password_verify($senha, $usuario['senha'])) {
    session_regenerate_id(true);
    $_SESSION['usuario_id']     = $usuario['id'];
    $_SESSION['usuario_email']  = $email;
    $_SESSION['usuario_logado'] = true;

    echo json_encode([
        'status'         => 'success',
        'message'        => 'Login bem-sucedido!',
        'usuario_logado' => true,
        'redirect'       => '/home.php',
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email ou senha incorretos. Tente novamente!']);
}
