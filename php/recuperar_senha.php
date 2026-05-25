<?php
session_start();

require_once __DIR__ . '/config.php';

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

// Verifica se usuário + email existem
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nome = :usuario AND email = :email LIMIT 1");
$stmt->execute([':usuario' => $usuario, ':email' => $email]);

if (!$stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário ou e-mail não encontrado.']);
    exit;
}

// Armazena email na sessão para os próximos passos
$_SESSION['usuario_email'] = $email;

// Gera código seguro de 8 caracteres hexadecimais (random_bytes — criptograficamente seguro)
$codigo = strtoupper(bin2hex(random_bytes(4)));

// Remove códigos anteriores do mesmo e-mail antes de inserir novo
$pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email")->execute([':email' => $email]);

// Insere com timestamp para validação de TTL
$stmt = $pdo->prepare(
    "INSERT INTO recuperar_senha (usuario, email, codigo, criado_em)
     VALUES (:usuario, :email, :codigo, NOW())"
);

// Fallback: se coluna criado_em não existir ainda (migração pendente), usa sem ela
try {
    $stmt->execute([':usuario' => $usuario, ':email' => $email, ':codigo' => $codigo]);
} catch (PDOException $e) {
    // Coluna criado_em ausente — inserção sem TTL (execute migrate.sql para corrigir)
    $stmt2 = $pdo->prepare(
        "INSERT INTO recuperar_senha (usuario, email, codigo) VALUES (:usuario, :email, :codigo)"
    );
    $stmt2->execute([':usuario' => $usuario, ':email' => $email, ':codigo' => $codigo]);
}

// Envia e-mail
$subject = mb_encode_mimeheader('Código de Recuperação de Senha', 'UTF-8', 'B', "\r\n");
$message = "
<html>
<body>
    <p>Olá $usuario,</p>
    <p>Seu código de recuperação de senha é: <strong>$codigo</strong></p>
    <p>Este código expira em <strong>" . RESET_CODE_TTL . " minutos</strong>.</p>
    <p>Se não foi você, ignore este e-mail.</p>
    <p>Atenciosamente,<br>Equipe Hemodat</p>
</body>
</html>";

$headers  = "From: no-reply@hemodatgp.com\r\n";
$headers .= "Reply-To: suporte@hemodatgp.com\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "MIME-Version: 1.0\r\n";

if (mail($email, $subject, $message, $headers)) {
    echo json_encode([
        'status'   => 'success',
        'message'  => 'Código de recuperação enviado com sucesso!',
        'redirect' => './codigo.html',
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar o e-mail. Tente novamente mais tarde.']);
}
