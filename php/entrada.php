<?php
session_start();

require_once __DIR__ . '/../includes/functions/config.php';
require_once __DIR__ . '/../includes/functions/csrf.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Sessão inválida.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido.']);
    exit;
}

csrf_validate();

$pdo      = db_connect();
$tipo     = trim($_POST['tipo']     ?? '');
$litros   = trim($_POST['litros']   ?? '');
$coleta   = trim($_POST['coleta']   ?? '');
$validade = trim($_POST['validade'] ?? '');

if (empty($tipo) || empty($litros) || empty($coleta) || empty($validade)) {
    echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']);
    exit;
}

if (!in_array($tipo, TIPOS_VALIDOS, true)) {
    echo json_encode(['status' => 'error', 'message' => 'Tipo sanguíneo inválido.']);
    exit;
}

$litros = (float) $litros;
if ($litros <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Quantidade deve ser maior que zero.']);
    exit;
}

$data_coleta   = DateTime::createFromFormat('d/m/Y', $coleta);
$data_validade = DateTime::createFromFormat('d/m/Y', $validade);

if (!$data_coleta || !$data_validade) {
    echo json_encode(['status' => 'error', 'message' => 'Formato de data inválido. Use DD/MM/AAAA.']);
    exit;
}

if ($data_validade <= $data_coleta) {
    echo json_encode(['status' => 'error', 'message' => 'Data de validade deve ser posterior à data de coleta.']);
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO bolsas_sangue (tipo_sanguineo, quantidade, data_coleta, data_validade)
     VALUES (:tipo, :litros, :coleta, :validade)"
);
$stmt->execute([
    ':tipo'    => $tipo,
    ':litros'  => $litros,
    ':coleta'  => $data_coleta->format('Y-m-d'),
    ':validade'=> $data_validade->format('Y-m-d'),
]);

echo json_encode(['status' => 'success', 'message' => 'Dados registrados com sucesso!']);
