<?php
/**
 * auth.php — Actions de autenticação.
 * Rota via ?action=login | cadastro | logout | session
 */

session_start();

require_once __DIR__ . '/../functions/config.php';
require_once __DIR__ . '/../functions/csrf.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'login':    action_login();    break;
    case 'cadastro': action_cadastro(); break;
    case 'logout':   action_logout();   break;
    case 'session':  action_session();  break;
    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Ação inválida.']);
}

// ─── Handlers ────────────────────────────────────────────────────────────────

function action_login(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido.']); exit;
    }

    csrf_validate();

    $pdo   = db_connect();
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($email) || empty($senha)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']); return;
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
            'redirect'       => BASE_URL . '/home.php',
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Email ou senha incorretos. Tente novamente!']);
    }
}

function action_cadastro(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido.']); exit;
    }

    csrf_validate();

    $pdo   = db_connect();
    $nome  = trim($_POST['nome']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']); return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'E-mail inválido.']); return;
    }
    if (strlen($senha) < 9) {
        echo json_encode(['status' => 'error', 'message' => 'A senha deve ter pelo menos 9 caracteres (8 alfanuméricos + 1 especial).']); return;
    }

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'O e-mail informado já está cadastrado.']); return;
    }

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
    $stmt->execute([':nome' => $nome, ':email' => $email, ':senha' => password_hash($senha, PASSWORD_BCRYPT)]);

    echo json_encode(['status' => 'success', 'message' => 'Usuário cadastrado com sucesso!']);
}

function action_logout(): void {
    session_unset();
    session_destroy();
    echo json_encode(['status' => 'success', 'message' => 'Sessão encerrada.']);
}

function action_session(): void {
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
}
