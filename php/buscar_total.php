<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$pdo = db_connect();

// SUM + GROUP BY agrega corretamente múltiplas entradas do mesmo tipo
$stmt = $pdo->prepare(
    "SELECT tipo_sanguineo, SUM(quantidade) AS quantidade
     FROM bolsas_sangue
     WHERE quantidade > 0
     GROUP BY tipo_sanguineo
     ORDER BY tipo_sanguineo"
);
$stmt->execute();
$bolsas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tipos_sanguineos = array_column($bolsas, 'tipo_sanguineo');
$quantidades      = array_column($bolsas, 'quantidade');

echo json_encode([
    'tipos_sanguineos' => $tipos_sanguineos,
    'quantidades'      => $quantidades,
]);
