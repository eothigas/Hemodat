<?php
session_start();

require_once __DIR__ . '/../includes/functions/config.php';
require_once __DIR__ . '/../includes/functions/csrf.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true ||
    !isset($_SESSION['usuario_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sessão inválida.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido.']);
    exit;
}

csrf_validate();

$tipo       = trim($_POST['tipo']   ?? '');
$quantidade = trim($_POST['litros'] ?? '');
$data_saida = trim($_POST['saida']  ?? '');

if (!$tipo || !$quantidade || !$data_saida) {
    echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
    exit;
}

if (!in_array($tipo, TIPOS_VALIDOS, true)) {
    echo json_encode(['status' => 'error', 'message' => 'Tipo sanguíneo inválido.']);
    exit;
}

$quantidade = (float) $quantidade;
if ($quantidade <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Quantidade deve ser maior que zero.']);
    exit;
}

$data_saida_obj = DateTime::createFromFormat('d/m/Y', $data_saida);
if (!$data_saida_obj) {
    echo json_encode(['status' => 'error', 'message' => 'Formato de data inválido. Use DD/MM/AAAA.']);
    exit;
}
$data_saida_fmt = $data_saida_obj->format('Y-m-d');
$data_atual     = date('Y-m-d');

$pdo = db_connect();

try {
    $pdo->beginTransaction();

    // FIFO: lote mais antigo por validade
    $stmt = $pdo->prepare(
        "SELECT id, quantidade, data_validade
         FROM bolsas_sangue
         WHERE tipo_sanguineo = :tipo
         ORDER BY data_validade ASC
         LIMIT 1
         FOR UPDATE"
    );
    $stmt->execute([':tipo' => $tipo]);
    $bolsa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bolsa) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Tipo sanguíneo não encontrado no estoque.']);
        exit;
    }

    if ($quantidade > $bolsa['quantidade']) {
        $pdo->rollBack();
        echo json_encode([
            'status'  => 'error',
            'message' => "Quantidade indisponível. Estoque atual: {$bolsa['quantidade']} litros.",
        ]);
        exit;
    }

    if ($data_saida_fmt > $bolsa['data_validade'] || $data_saida_fmt < $data_atual) {
        $pdo->rollBack();
        $validade_fmt = DateTime::createFromFormat('Y-m-d', $bolsa['data_validade'])->format('d/m/Y');
        echo json_encode([
            'status'  => 'error',
            'message' => "Data de saída inválida. Deve ser entre hoje e {$validade_fmt}.",
        ]);
        exit;
    }

    $email = $_SESSION['usuario_email'];
    $stmt  = $pdo->prepare(
        "INSERT INTO saida_bolsas_sangue (email, tipo_sanguineo, quantidade, data_saida)
         VALUES (:email, :tipo, :qtd, :saida)"
    );
    $stmt->execute([':email' => $email, ':tipo' => $tipo, ':qtd' => $quantidade, ':saida' => $data_saida_fmt]);

    $nova_quantidade = $bolsa['quantidade'] - $quantidade;
    if ($nova_quantidade <= 0) {
        $pdo->prepare("DELETE FROM bolsas_sangue WHERE id = :id")->execute([':id' => $bolsa['id']]);
    } else {
        $pdo->prepare("UPDATE bolsas_sangue SET quantidade = :qtd WHERE id = :id")
            ->execute([':qtd' => $nova_quantidade, ':id' => $bolsa['id']]);
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'Registro de saída realizado com sucesso!']);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Erro ao registrar a saída: ' . $e->getMessage()]);
}
