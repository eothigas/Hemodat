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
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // Verificar se os campos estão preenchidos
    if (empty($email) || empty($senha)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']);
        exit;
    }

    // Verificar se o email e senha estão corretos
    $stmt = $pdo->prepare("SELECT id, senha FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Salvar o email e o ID do usuário na sessão
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_email'] = $email;

        // Variável para indicar que o usuário está logado
        $_SESSION['usuario_logado'] = true;

        // Retornar mensagem de sucesso e a variável indicando que o usuário está logado
        echo json_encode(['status' => 'success', 'message' => 'Login bem-sucedido!', 'usuario_logado' => true, 'redirect' => './home.html']);
    } else {
        // Credenciais inválidas
        echo json_encode(['status' => 'error', 'message' => 'Email ou senha incorretos. Tente novamente!']);
    }
}
?>
