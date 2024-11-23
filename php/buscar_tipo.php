<?php
// Configuração do banco de dados
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

// Consultar os tipos sanguíneos
$stmt = $pdo->prepare("SELECT tipo_sanguineo FROM bolsas_sangue");
$stmt->execute();
$tiposSanguineos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Extrair apenas os valores de tipo_sanguineo em um array
$tiposSanguineosArray = array_column($tiposSanguineos, 'tipo_sanguineo');

// Remover duplicatas
$tiposSanguineosUnicos = array_unique($tiposSanguineosArray);

// Enviar a resposta em formato JSON
echo json_encode($tiposSanguineosUnicos);
?>
