<?php
/**
 * bolsas.php — Actions de gestão de bolsas de sangue.
 * Rota via ?action=entrada | saida | buscar_tipo | buscar_total
 */

session_start();

require_once __DIR__ . '/../functions/config.php';
require_once __DIR__ . '/../functions/csrf.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'entrada':      action_entrada();      break;
    case 'saida':        action_saida();        break;
    case 'buscar_tipo':  action_buscar_tipo();  break;
    case 'buscar_total': action_buscar_total(); break;
    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Ação inválida.']);
}

// ─── Helpers de sessão ────────────────────────────────────────────────────────

function requer_sessao(): void {
    if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Sessão inválida.']);
        exit;
    }
}

// ─── Handlers ────────────────────────────────────────────────────────────────

function action_entrada(): void {
    requer_sessao();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido.']); exit;
    }

    csrf_validate();

    $pdo      = db_connect();
    $tipo     = trim($_POST['tipo']     ?? '');
    $litros   = trim($_POST['litros']   ?? '');
    $coleta   = trim($_POST['coleta']   ?? '');
    $validade = trim($_POST['validade'] ?? '');

    if (empty($tipo) || empty($litros) || empty($coleta) || empty($validade)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']); return;
    }
    if (!in_array($tipo, TIPOS_VALIDOS, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Tipo sanguíneo inválido.']); return;
    }

    $litros = (float) $litros;
    if ($litros <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Quantidade deve ser maior que zero.']); return;
    }

    $data_coleta   = DateTime::createFromFormat('d/m/Y', $coleta);
    $data_validade = DateTime::createFromFormat('d/m/Y', $validade);

    if (!$data_coleta || !$data_validade) {
        echo json_encode(['status' => 'error', 'message' => 'Formato de data inválido. Use DD/MM/AAAA.']); return;
    }
    if ($data_validade <= $data_coleta) {
        echo json_encode(['status' => 'error', 'message' => 'Data de validade deve ser posterior à data de coleta.']); return;
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
}

function action_saida(): void {
    requer_sessao();
    if (!isset($_SESSION['usuario_email'])) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão inválida.']); exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido.']); exit;
    }

    csrf_validate();

    $tipo       = trim($_POST['tipo']   ?? '');
    $quantidade = (float) ($_POST['litros'] ?? 0);
    $data_saida = trim($_POST['saida']  ?? '');

    if (!$tipo || !$quantidade || !$data_saida) {
        echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']); return;
    }
    if (!in_array($tipo, TIPOS_VALIDOS, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Tipo sanguíneo inválido.']); return;
    }
    if ($quantidade <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Quantidade deve ser maior que zero.']); return;
    }

    $data_saida_obj = DateTime::createFromFormat('d/m/Y', $data_saida);
    if (!$data_saida_obj) {
        echo json_encode(['status' => 'error', 'message' => 'Formato de data inválido. Use DD/MM/AAAA.']); return;
    }
    $data_saida_fmt = $data_saida_obj->format('Y-m-d');
    $data_atual     = date('Y-m-d');

    $pdo = db_connect();

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "SELECT id, quantidade, data_validade
             FROM bolsas_sangue
             WHERE tipo_sanguineo = :tipo
             ORDER BY data_validade ASC
             LIMIT 1 FOR UPDATE"
        );
        $stmt->execute([':tipo' => $tipo]);
        $bolsa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$bolsa) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Tipo sanguíneo não encontrado no estoque.']); return;
        }
        if ($quantidade > $bolsa['quantidade']) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => "Quantidade indisponível. Estoque atual: {$bolsa['quantidade']} litros."]); return;
        }
        if ($data_saida_fmt > $bolsa['data_validade'] || $data_saida_fmt < $data_atual) {
            $pdo->rollBack();
            $val = DateTime::createFromFormat('Y-m-d', $bolsa['data_validade'])->format('d/m/Y');
            echo json_encode(['status' => 'error', 'message' => "Data de saída inválida. Deve ser entre hoje e {$val}."]); return;
        }

        $pdo->prepare(
            "INSERT INTO saida_bolsas_sangue (email, tipo_sanguineo, quantidade, data_saida)
             VALUES (:email, :tipo, :qtd, :saida)"
        )->execute([
            ':email' => $_SESSION['usuario_email'],
            ':tipo'  => $tipo,
            ':qtd'   => $quantidade,
            ':saida' => $data_saida_fmt,
        ]);

        $nova = $bolsa['quantidade'] - $quantidade;
        if ($nova <= 0) {
            $pdo->prepare("DELETE FROM bolsas_sangue WHERE id = :id")->execute([':id' => $bolsa['id']]);
        } else {
            $pdo->prepare("UPDATE bolsas_sangue SET quantidade = :qtd WHERE id = :id")
                ->execute([':qtd' => $nova, ':id' => $bolsa['id']]);
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Registro de saída realizado com sucesso!']);

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Erro ao registrar saída: ' . $e->getMessage()]);
    }
}

function action_buscar_tipo(): void {
    $pdo  = db_connect();
    $stmt = $pdo->prepare(
        "SELECT DISTINCT tipo_sanguineo FROM bolsas_sangue WHERE quantidade > 0 ORDER BY tipo_sanguineo"
    );
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
}

function action_buscar_total(): void {
    $pdo  = db_connect();
    $stmt = $pdo->prepare(
        "SELECT tipo_sanguineo, SUM(quantidade) AS quantidade
         FROM bolsas_sangue WHERE quantidade > 0
         GROUP BY tipo_sanguineo ORDER BY tipo_sanguineo"
    );
    $stmt->execute();
    $bolsas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'tipos_sanguineos' => array_column($bolsas, 'tipo_sanguineo'),
        'quantidades'      => array_column($bolsas, 'quantidade'),
    ]);
}
