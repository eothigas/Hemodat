<?php
/**
 * senha.php - Actions de recuperação e alteração de senha.
 * Rota via ?action=recuperar | validar | alterar
 */

session_start();

require_once __DIR__ . '/../functions/config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'recuperar': action_recuperar(); break;
    case 'validar':   action_validar();   break;
    case 'alterar':   action_alterar();   break;
    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Ação inválida.']);
}

// ─── Handlers ────────────────────────────────────────────────────────────────

function action_recuperar(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido.']); exit;
    }

    $pdo     = db_connect();
    $usuario = trim($_POST['usuario'] ?? '');
    $email   = trim($_POST['email']   ?? '');

    if (empty($usuario) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']); return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'E-mail inválido.']); return;
    }

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nome = :usuario AND email = :email LIMIT 1");
    $stmt->execute([':usuario' => $usuario, ':email' => $email]);
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Usuário ou e-mail não encontrado.']); return;
    }

    $_SESSION['usuario_email'] = $email;

    $codigo = strtoupper(bin2hex(random_bytes(4)));
    $pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email")->execute([':email' => $email]);

    try {
        $pdo->prepare(
            "INSERT INTO recuperar_senha (usuario, email, codigo, criado_em) VALUES (:usuario, :email, :codigo, NOW())"
        )->execute([':usuario' => $usuario, ':email' => $email, ':codigo' => $codigo]);
    } catch (PDOException $e) {
        $pdo->prepare(
            "INSERT INTO recuperar_senha (usuario, email, codigo) VALUES (:usuario, :email, :codigo)"
        )->execute([':usuario' => $usuario, ':email' => $email, ':codigo' => $codigo]);
    }

    $subject  = mb_encode_mimeheader('Código de Recuperação de Senha', 'UTF-8', 'B', "\r\n");
    $message  = "<html><body>
        <p>Olá $usuario,</p>
        <p>Seu código de recuperação: <strong>$codigo</strong></p>
        <p>Expira em <strong>" . RESET_CODE_TTL . " minutos</strong>.</p>
        <p>Se não foi você, ignore este e-mail.</p>
        <p>Equipe Hemodat</p>
    </body></html>";
    $headers  = "From: no-reply@hemodatgp.com\r\nReply-To: suporte@hemodatgp.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\nMIME-Version: 1.0\r\n";

    if (mail($email, $subject, $message, $headers)) {
        echo json_encode([
            'status'   => 'success',
            'message'  => 'Código enviado com sucesso!',
            'redirect' => BASE_URL . '/codigo.php',
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar o e-mail. Tente novamente.']);
    }
}

function action_validar(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido.']); exit;
    }
    if (!isset($_SESSION['usuario_email'])) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão inválida.']); return;
    }

    $pdo    = db_connect();
    $email  = trim($_SESSION['usuario_email']);
    $codigo = strtoupper(trim($_POST['code'] ?? ''));

    if (empty($codigo)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, insira o código.']); return;
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
        echo json_encode(['status' => 'error', 'message' => 'Código inválido ou expirado.']); return;
    }

    if (!empty($record['criado_em'])) {
        $diff_min = (time() - strtotime($record['criado_em'])) / 60;
        if ($diff_min > RESET_CODE_TTL) {
            $pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email")->execute([':email' => $email]);
            echo json_encode(['status' => 'error', 'message' => 'Código expirado. Solicite um novo.']); return;
        }
    }

    $pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email")->execute([':email' => $email]);
    echo json_encode([
        'status'   => 'success',
        'message'  => 'Código validado!',
        'redirect' => BASE_URL . '/alterar_senha.php',
    ]);
}

function action_alterar(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido.']); exit;
    }
    if (!isset($_SESSION['usuario_email'])) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão inválida.']); return;
    }

    $pdo          = db_connect();
    $email        = $_SESSION['usuario_email'];
    $senha        = trim($_POST['senha']         ?? '');
    $confirmSenha = trim($_POST['confirm-senha'] ?? '');

    if (empty($senha) || empty($confirmSenha)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos.']); return;
    }
    if ($senha !== $confirmSenha) {
        echo json_encode(['status' => 'error', 'message' => 'As senhas não coincidem.']); return;
    }
    if (strlen($senha) < 9) {
        echo json_encode(['status' => 'error', 'message' => 'A senha deve ter pelo menos 9 caracteres.']); return;
    }

    $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE email = :email")
        ->execute([':senha' => password_hash($senha, PASSWORD_BCRYPT), ':email' => $email]);

    session_unset();
    session_destroy();

    echo json_encode([
        'status'   => 'success',
        'message'  => 'Senha alterada! Faça login novamente.',
        'redirect' => BASE_URL . '/login.php',
    ]);
}
