<?php
require_once __DIR__ . '/../includes/functions/config.php';

header('Content-Type: application/json');

$pdo  = db_connect();
$stmt = $pdo->prepare(
    "SELECT tipo_sanguineo, SUM(quantidade) AS quantidade
     FROM bolsas_sangue
     WHERE quantidade > 0
     GROUP BY tipo_sanguineo
     ORDER BY tipo_sanguineo"
);
$stmt->execute();
$bolsas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'tipos_sanguineos' => array_column($bolsas, 'tipo_sanguineo'),
    'quantidades'      => array_column($bolsas, 'quantidade'),
]);
