<?php
session_start();

require_once __DIR__ . '/../includes/functions/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido.']);
    exit;
}

if (!isset($_SESSION['usuario_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sessão inválida.']);
    exit;
}

$pdo    = db_connect();
$email  = trim($_SESSION['usuario_email']);
$codigo = strtoupper(trim($_POST['code'] ?? ''));

if (empty($codigo)) {
    echo json_encode(['status' => 'error', 'message' => 'Por favor, insira o código de validação.']);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "SELECT id, criado_em FROM recuperar_senha WHERE email = :email AND codigo = :codigo LIMIT 1"
    );
    $stmt->execute([':email' => $email, ':codigo' => $codigo]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $stmt = $pdo->prepare("SELECT id FROM recuperar_senha WHERE email = :email AND codigo = :codigo LIMIT 1");
    $stmt->execute([':email' => $email, ':codigo' => $codigo]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$record) {
    echo json_encode(['status' => 'error', 'message' => 'Código inválido ou expirado.']);
    exit;
}

if (!empty($record['criado_em'])) {
    $diff_min = (time() - strtotime($record['criado_em'])) / 60;
    if ($diff_min > RESET_CODE_TTL) {
        $pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email")->execute([':email' => $email]);
        echo json_encode(['status' => 'error', 'message' => 'Código expirado. Solicite um novo.']);
        exit;
    }
}

$pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email")->execute([':email' => $email]);

echo json_encode(['status' => 'success', 'message' => 'Código validado!', 'redirect' => '/alterar_senha.php']);
