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

// Verificar se o usuário está logado (se o email está na sessão)
if (!isset($_SESSION['usuario_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sessão inválida.']);
    exit;
} 

// Verificar se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar os dados do formulário
    $tipo = trim($_POST['tipo']);
    $litros = trim($_POST['litros']);
    $coleta = trim($_POST['coleta']);
    $validade = trim($_POST['validade']);

    // Validação básica
    if (empty($tipo) || empty($litros) || empty($coleta) || empty($validade)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']);
        exit;
    }

    // Converter as datas para o formato 'Y-m-d'
    $data_coleta = DateTime::createFromFormat('d/m/Y', $coleta)->format('Y-m-d');
    $data_validade = DateTime::createFromFormat('d/m/Y', $validade)->format('Y-m-d');

    // Inserir os dados na tabela bolsas_sangue
    $stmt = $pdo->prepare("INSERT INTO bolsas_sangue (tipo_sanguineo, quantidade, data_coleta, data_validade) VALUES (:tipo, :litros, :coleta, :validade)");

    // Bind os parâmetros
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':litros', $litros);
    $stmt->bindParam(':coleta', $data_coleta); // Bind a data convertida
    $stmt->bindParam(':validade', $data_validade); // Bind a data convertida

    // Executar a query
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Dados registrados com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao registrar dados.']);
    }
}
?>
