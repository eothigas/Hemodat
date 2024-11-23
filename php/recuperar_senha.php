<?php
session_start(); // Inicia a sessão

// Configuração do banco de dados
$host = "localhost";
$dbname = "efegduik_gphemodat";
$username = "efegduik_gphemodat";
$password = "fHCXpD4sACYN8EyEd4QG";

header('Content-Type: application/json'); // Define o retorno como JSON

try {
    // Conexão com o banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao conectar ao banco de dados.']);
    exit;
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $email = trim($_POST['email']);

    // Validação básica
    if (empty($usuario) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']);
        exit;
    }

    // Verificar se o usuário e o e-mail existem na tabela 'usuarios'
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nome = :usuario AND email = :email");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuarioExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuarioExistente) {
        echo json_encode(['status' => 'error', 'message' => 'Usuário ou e-mail não encontrado.']);
        exit;
    }

    // Armazenar o e-mail na sessão
    $_SESSION['usuario_email'] = $email;

    // Gerar código alfanumérico de 8 dígitos
    $codigo = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);

    // Inserir na tabela recuperar_senha
    $stmt = $pdo->prepare("INSERT INTO recuperar_senha (usuario, email, codigo) VALUES (:usuario, :email, :codigo)");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':codigo', $codigo);

    if ($stmt->execute()) {
        // Enviar e-mail com o código de recuperação
        $to = $email;
        $subject = mb_encode_mimeheader("Código de Recuperação de Senha", "UTF-8", "B", "\r\n");

        // Corpo do e-mail em HTML
        $message = "
        <html>
        <head>
            <title>Código de Recuperação de Senha</title>
        </head>
        <body>
            <p>Olá $usuario,</p>
            <p>Seu código de recuperação de senha é: <strong>$codigo</strong></p>
            <p>Atenciosamente,<br>Equipe Hemodat</p>
        </body>
        </html>
        ";

        // Cabeçalhos do e-mail
        $headers = "From: no-reply@hemodatgp.com\r\n";
        $headers .= "Reply-To: suporte@hemodatgp.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "MIME-Version: 1.0\r\n";

        // Enviar o e-mail
        if (mail($to, $subject, $message, $headers)) {
            echo json_encode(['status' => 'success', 'message' => 'Código de recuperação enviado com sucesso!', 'redirect' => './codigo.html']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar o e-mail. Tente novamente mais tarde.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar os dados.']);
    }
}
?>
