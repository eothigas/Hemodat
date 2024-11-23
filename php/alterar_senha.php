<?php
session_start(); // Inicia a sessão

// Configuração da conexão com o banco de dados
$host = "localhost";
$dbname = "efegduik_gphemodat";
$username = "efegduik_gphemodat";
$password = "fHCXpD4sACYN8EyEd4QG";

try {
    // Conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao conectar ao banco de dados.']);
    exit;
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se o email está na sessão
    if (!isset($_SESSION['usuario_email'])) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão inválida. Por favor, tente novamente.']);
        exit;
    }

    $email = $_SESSION['usuario_email'];
    $senha = trim($_POST['senha']);
    $confirmSenha = trim($_POST['confirm-senha']);

    // Validação básica
    if (empty($senha) || empty($confirmSenha)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos.']);
        exit;
    }

    if ($senha !== $confirmSenha) {
        echo json_encode(['status' => 'error', 'message' => 'As senhas não coincidem.']);
        exit;
    }

    if (strlen($senha) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'A senha deve ter pelo menos 8 caracteres.']);
        exit;
    }

    // Atualizar a senha no banco de dados
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE email = :email");
    $stmt->bindParam(':senha', $senhaHash);
    $stmt->bindParam(':email', $email);

    if ($stmt->execute()) {
        // Destruir a sessão
        session_unset(); // Remove todas as variáveis de sessão
        session_destroy(); // Destrói a sessão

        echo json_encode(['status' => 'success', 'message' => 'Senha alterada com sucesso! Faça login novamente.', 'redirect' => './login.html']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao alterar a senha. Tente novamente mais tarde.']);
    }
}
?>
