<?php
require_once __DIR__ . '/../includes/functions/config.php';

header('Content-Type: application/json');

$pdo  = db_connect();
$stmt = $pdo->prepare(
    "SELECT DISTINCT tipo_sanguineo
     FROM bolsas_sangue
     WHERE quantidade > 0
     ORDER BY tipo_sanguineo"
);
$stmt->execute();

echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
