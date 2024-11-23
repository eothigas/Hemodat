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
    // Retornar erro de conexão como JSON
    echo json_encode(['status' => 'error', 'message' => 'Erro ao conectar ao banco de dados.']);
    exit;
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // Validação básica
    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']);
        exit;
    }

    // Verificar se o e-mail já está cadastrado
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuarioExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuarioExistente) {
        echo json_encode(['status' => 'error', 'message' => 'O e-mail informado já está cadastrado. Tente novamente.']);
    } else {
        // Inserir no banco de dados
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");

        // Hash da senha para maior segurança
        $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaHash);

        if ($stmt->execute()) {
            // Responder com sucesso
            echo json_encode(['status' => 'success', 'message' => 'Usuário cadastrado com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar o usuário.']);
        }
    }
}
?>
