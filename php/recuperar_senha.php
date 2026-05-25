<?php
session_start();

require_once __DIR__ . '/../includes/functions/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido.']);
    exit;
}

$pdo     = db_connect();
$usuario = trim($_POST['usuario'] ?? '');
$email   = trim($_POST['email']   ?? '');

if (empty($usuario) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'E-mail inválido.']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nome = :usuario AND email = :email LIMIT 1");
$stmt->execute([':usuario' => $usuario, ':email' => $email]);

if (!$stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário ou e-mail não encontrado.']);
    exit;
}

$_SESSION['usuario_email'] = $email;

$codigo = strtoupper(bin2hex(random_bytes(4)));

$pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email")->execute([':email' => $email]);

try {
    $stmt = $pdo->prepare(
        "INSERT INTO recuperar_senha (usuario, email, codigo, criado_em) VALUES (:usuario, :email, :codigo, NOW())"
    );
    $stmt->execute([':usuario' => $usuario, ':email' => $email, ':codigo' => $codigo]);
} catch (PDOException $e) {
    $stmt = $pdo->prepare(
        "INSERT INTO recuperar_senha (usuario, email, codigo) VALUES (:usuario, :email, :codigo)"
    );
    $stmt->execute([':usuario' => $usuario, ':email' => $email, ':codigo' => $codigo]);
}

$subject = mb_encode_mimeheader('Código de Recuperação de Senha', 'UTF-8', 'B', "\r\n");
$message = "
<html><body>
    <p>Olá $usuario,</p>
    <p>Seu código de recuperação: <strong>$codigo</strong></p>
    <p>Expira em <strong>" . RESET_CODE_TTL . " minutos</strong>.</p>
    <p>Se não foi você, ignore este e-mail.</p>
    <p>Equipe Hemodat</p>
</body></html>";

$headers  = "From: no-reply@hemodatgp.com\r\n";
$headers .= "Reply-To: suporte@hemodatgp.com\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "MIME-Version: 1.0\r\n";

if (mail($email, $subject, $message, $headers)) {
    echo json_encode(['status' => 'success', 'message' => 'Código enviado com sucesso!', 'redirect' => '/codigo.php']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar o e-mail. Tente novamente.']);
}
