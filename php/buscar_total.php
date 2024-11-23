<?php
// Conexão com o banco de dados
$host = "localhost";
$dbname = "efegduik_gphemodat";
$username = "efegduik_gphemodat";
$password = "fHCXpD4sACYN8EyEd4QG";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
    exit;
}

// Recuperar tipos sanguíneos e quantidades disponíveis
$sql = "SELECT tipo_sanguineo, quantidade FROM bolsas_sangue";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$bolsas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar os dados para o JavaScript
$tipos_sanguineos = [];
$quantidades = [];

foreach ($bolsas as $bolsa) {
    $tipos_sanguineos[] = $bolsa['tipo_sanguineo'];
    $quantidades[] = $bolsa['quantidade'];
}

// Passar os dados para o JavaScript
echo json_encode(['tipos_sanguineos' => $tipos_sanguineos, 'quantidades' => $quantidades]);
?>
