<?php
/**
 * auth.php - Actions de autenticação.
 * Rota via ?action=login | cadastro | logout | session
 */

session_start();

require_once __DIR__ . '/../functions/config.php';
require_once __DIR__ . '/../functions/csrf.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'login':            action_login();            break;
    case 'cadastro':         action_cadastro();         break;
    case 'logout':           action_logout();           break;
    case 'session':          action_session();          break;
    case 'listar_usuarios':  action_listar_usuarios();  break;
    case 'alterar_role':     action_alterar_role();     break;
    case 'salvar_estoque_min': action_salvar_estoque_min(); break;
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

    $stmt = $pdo->prepare("SELECT id, nome, senha, role FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_email']  = $email;
        $_SESSION['usuario_nome']   = $usuario['nome'];
        $_SESSION['usuario_role']   = $usuario['role'] ?? 'operador';
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
            'nome'           => $_SESSION['usuario_nome']  ?? '',
            'role'           => $_SESSION['usuario_role']  ?? 'operador',
        ]);
    } else {
        echo json_encode([
            'status'         => 'error',
            'usuario_logado' => false,
            'message'        => 'Nenhum usuário logado.',
        ]);
    }
}

// ─── Admin helpers ────────────────────────────────────────────────────────────

function requer_admin(): void {
    if (
        empty($_SESSION['usuario_logado']) ||
        ($_SESSION['usuario_role'] ?? '') !== 'admin'
    ) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Acesso negado.']);
        exit;
    }
}

function action_listar_usuarios(): void {
    requer_admin();
    $pdo  = db_connect();
    $stmt = $pdo->query("SELECT id, nome, email, role FROM usuarios ORDER BY nome");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function action_alterar_role(): void {
    requer_admin();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido.']); exit;
    }

    $id   = (int) ($_POST['id']   ?? 0);
    $role = trim($_POST['role'] ?? '');

    if (!in_array($role, ['admin', 'operador'], true)) {
        echo json_encode(['status' => 'error', 'message' => 'Role inválida.']); return;
    }
    if ($id === (int) ($_SESSION['usuario_id'] ?? 0) && $role !== 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'Não é possível remover seu próprio acesso de admin.']); return;
    }

    $pdo = db_connect();
    $pdo->prepare("UPDATE usuarios SET role = :role WHERE id = :id")
        ->execute([':role' => $role, ':id' => $id]);
    echo json_encode(['status' => 'success', 'message' => 'Permissão atualizada.']);
}

function action_salvar_estoque_min(): void {
    requer_admin();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido.']); exit;
    }

    $pdo    = db_connect();
    $minimos = $_POST['minimos'] ?? [];

    if (!is_array($minimos)) {
        echo json_encode(['status' => 'error', 'message' => 'Dados inválidos.']); return;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO estoque_minimo (tipo_sanguineo, minimo_litros)
         VALUES (:tipo, :min)
         ON DUPLICATE KEY UPDATE minimo_litros = :min2"
    );

    foreach ($minimos as $tipo => $min) {
        if (!in_array($tipo, TIPOS_VALIDOS, true)) continue;
        $val = max(0, (float) $min);
        $stmt->execute([':tipo' => $tipo, ':min' => $val, ':min2' => $val]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Configurações salvas.']);
}
