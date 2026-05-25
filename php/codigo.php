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

$pdo    = db_connect();
$email  = trim($_SESSION['usuario_email']);
$codigo = strtoupper(trim($_POST['code'] ?? ''));

if (empty($codigo)) {
    echo json_encode(['status' => 'error', 'message' => 'Por favor, insira o código de validação.']);
    exit;
}

// Busca código válido e verifica TTL (RESET_CODE_TTL minutos)
$stmt = $pdo->prepare(
    "SELECT id, criado_em FROM recuperar_senha
     WHERE email = :email AND codigo = :codigo
     LIMIT 1"
);

try {
    $stmt->execute([':email' => $email, ':codigo' => $codigo]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Coluna criado_em ausente (migração pendente) — valida sem TTL
    $stmt2 = $pdo->prepare("SELECT id FROM recuperar_senha WHERE email = :email AND codigo = :codigo LIMIT 1");
    $stmt2->execute([':email' => $email, ':codigo' => $codigo]);
    $record = $stmt2->fetch(PDO::FETCH_ASSOC);
}

if (!$record) {
    echo json_encode(['status' => 'error', 'message' => 'Código inválido ou expirado.']);
    exit;
}

// Verifica TTL se coluna criado_em existe
if (!empty($record['criado_em'])) {
    $criado   = new DateTime($record['criado_em']);
    $agora    = new DateTime();
    $diff_min = ($agora->getTimestamp() - $criado->getTimestamp()) / 60;

    if ($diff_min > RESET_CODE_TTL) {
        // Remove código expirado
        $pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email")->execute([':email' => $email]);
        echo json_encode(['status' => 'error', 'message' => 'Código expirado. Solicite um novo.']);
        exit;
    }
}

// Código válido — remove e segue para alterar senha
$pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email")->execute([':email' => $email]);

echo json_encode([
    'status'   => 'success',
    'message'  => 'Código validado com sucesso!',
    'redirect' => './alterar_senha.html',
]);
