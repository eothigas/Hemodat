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

    $email = trim($_SESSION['usuario_email']);
    $codigo = trim($_POST['code']); // Nome do input do formulário deve ser 'code'

    // Verificar se o código foi preenchido
    if (empty($codigo)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, insira o código de validação.']);
        exit;
    }

    // Verificar se o código é válido no banco de dados
    $stmt = $pdo->prepare("SELECT id FROM recuperar_senha WHERE email = :email AND codigo = :codigo");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':codigo', $codigo);
    $stmt->execute();

    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($record) {
        // Código válido, remover o registro
        $deleteStmt = $pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email");
        $deleteStmt->bindParam(':email', $email);
        $deleteStmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Código validado com sucesso!', 'redirect' => './alterar_senha.html']);
    } else {
        // Código inválido ou expirado
        echo json_encode(['status' => 'error', 'message' => 'Código inválido ou expirado.']);
    }
}
?>
