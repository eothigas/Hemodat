<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$pdo = db_connect();

// DISTINCT direto no SQL — mais eficiente que array_unique() no PHP
$stmt = $pdo->prepare(
    "SELECT DISTINCT tipo_sanguineo
     FROM bolsas_sangue
     WHERE quantidade > 0
     ORDER BY tipo_sanguineo"
);
$stmt->execute();
$tipos = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($tipos);
