<?php
/**
 * bolsas.php - Actions de gestão de bolsas de sangue.
 * Rota via ?action=entrada | saida | buscar_tipo | buscar_total
 */

session_start();

require_once __DIR__ . '/../functions/config.php';
require_once __DIR__ . '/../functions/csrf.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'entrada':         action_entrada();         break;
    case 'saida':           action_saida();           break;
    case 'buscar_tipo':     action_buscar_tipo();     break;
    case 'buscar_total':    action_buscar_total();    break;
    case 'vencimento':      action_vencimento();      break;
    case 'estoque_alerta':  action_estoque_alerta();  break;
    case 'historico':       action_historico();       break;
    case 'estoque_min_get': action_estoque_min_get(); break;
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

    $coletaFmt   = $data_coleta->format('Y-m-d');
    $validadeFmt = $data_validade->format('Y-m-d');

    $pdo->prepare(
        "INSERT INTO bolsas_sangue (tipo_sanguineo, quantidade, data_coleta, data_validade)
         VALUES (:tipo, :litros, :coleta, :validade)"
    )->execute([
        ':tipo'    => $tipo,
        ':litros'  => $litros,
        ':coleta'  => $coletaFmt,
        ':validade'=> $validadeFmt,
    ]);

    // Log para histórico (silencioso se tabela não existir ainda)
    try {
        $pdo->prepare(
            "INSERT INTO entradas_log (tipo_sanguineo, quantidade, data_coleta, data_validade)
             VALUES (:tipo, :litros, :coleta, :validade)"
        )->execute([
            ':tipo'    => $tipo,
            ':litros'  => $litros,
            ':coleta'  => $coletaFmt,
            ':validade'=> $validadeFmt,
        ]);
    } catch (PDOException $ignored) {}

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

// ─── Novos handlers P2 ───────────────────────────────────────────────────────

/**
 * Retorna bolsas que vencem em até DIAS_ALERTA_VENCIMENTO dias.
 */
function action_vencimento(): void {
    $pdo  = db_connect();
    $dias = DIAS_ALERTA_VENCIMENTO;
    $stmt = $pdo->prepare(
        "SELECT tipo_sanguineo,
                SUM(quantidade)    AS quantidade,
                MIN(data_validade) AS data_validade
         FROM bolsas_sangue
         WHERE quantidade > 0
           AND data_validade BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :dias DAY)
         GROUP BY tipo_sanguineo
         ORDER BY data_validade ASC"
    );
    $stmt->execute([':dias' => $dias]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

/**
 * Retorna tipos cujo estoque total está abaixo do mínimo configurado.
 */
function action_estoque_alerta(): void {
    $pdo = db_connect();

    // Minimums (fallback 2.0 se tabela não existir)
    try {
        $mins = $pdo->query("SELECT tipo_sanguineo, minimo_litros FROM estoque_minimo")
                    ->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        $mins = array_fill_keys(TIPOS_VALIDOS, 2.0);
    }

    $estoques = $pdo->query(
        "SELECT tipo_sanguineo, SUM(quantidade) AS total
         FROM bolsas_sangue WHERE quantidade > 0
         GROUP BY tipo_sanguineo"
    )->fetchAll(PDO::FETCH_KEY_PAIR);

    $alertas = [];
    foreach ($mins as $tipo => $min) {
        $atual = (float) ($estoques[$tipo] ?? 0);
        if ($atual < (float) $min) {
            $alertas[] = [
                'tipo'    => $tipo,
                'atual'   => round($atual, 2),
                'minimo'  => round((float) $min, 2),
            ];
        }
    }
    echo json_encode($alertas);
}

/**
 * Histórico paginado de entradas + saídas.
 * GET params: page, tipo, operacao (entrada|saida|'')
 */
function action_historico(): void {
    requer_sessao();
    $pdo    = db_connect();
    $page   = max(1, (int) ($_GET['page']     ?? 1));
    $limit  = 15;
    $offset = ($page - 1) * $limit;
    $tipo   = trim($_GET['tipo']     ?? '');
    $oper   = trim($_GET['operacao'] ?? '');

    if ($tipo && !in_array($tipo, TIPOS_VALIDOS, true)) $tipo = '';
    if (!in_array($oper, ['entrada', 'saida', ''], true)) $oper = '';

    // Filtro de tipo como SQL literal (validado contra whitelist)
    $tipo_sql_e = $tipo ? ("AND tipo_sanguineo = " . $pdo->quote($tipo)) : '';
    $tipo_sql_s = $tipo ? ("AND tipo_sanguineo = " . $pdo->quote($tipo)) : '';

    $parts = [];
    if ($oper !== 'saida') {
        $parts[] = "SELECT 'Entrada'        AS operacao,
                           tipo_sanguineo,
                           quantidade,
                           data_coleta       AS data_evento,
                           NULL              AS responsavel,
                           criado_em
                    FROM entradas_log
                    WHERE 1=1 $tipo_sql_e";
    }
    if ($oper !== 'entrada') {
        $parts[] = "SELECT 'Saída'           AS operacao,
                           tipo_sanguineo,
                           quantidade,
                           data_saida        AS data_evento,
                           email             AS responsavel,
                           NULL              AS criado_em
                    FROM saida_bolsas_sangue
                    WHERE 1=1 $tipo_sql_s";
    }

    if (empty($parts)) {
        echo json_encode(['rows' => [], 'total' => 0, 'page' => 1, 'pages' => 0]);
        return;
    }

    $union = implode(' UNION ALL ', $parts);

    try {
        $stmt = $pdo->prepare(
            "SELECT * FROM ($union) t
             ORDER BY COALESCE(criado_em, data_evento) DESC, data_evento DESC
             LIMIT :lim OFFSET :off"
        );
        $stmt->bindValue(':lim', $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = (int) $pdo->query("SELECT COUNT(*) FROM ($union) t")->fetchColumn();

        echo json_encode([
            'rows'  => $rows,
            'total' => $total,
            'page'  => $page,
            'pages' => (int) ceil($total / $limit),
        ]);

    } catch (PDOException $e) {
        // entradas_log ainda não existe — mostrar só saídas
        $w    = $tipo ? ("AND tipo_sanguineo = " . $pdo->quote($tipo)) : '';
        $stmt = $pdo->prepare(
            "SELECT 'Saída' AS operacao, tipo_sanguineo, quantidade,
                    data_saida AS data_evento, email AS responsavel
             FROM saida_bolsas_sangue WHERE 1=1 $w
             ORDER BY data_saida DESC
             LIMIT :lim OFFSET :off"
        );
        $stmt->bindValue(':lim', $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows  = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = (int) $pdo->query("SELECT COUNT(*) FROM saida_bolsas_sangue WHERE 1=1 $w")->fetchColumn();

        echo json_encode([
            'rows'  => $rows,
            'total' => $total,
            'page'  => $page,
            'pages' => (int) ceil($total / $limit),
        ]);
    }
}

/**
 * Retorna configurações de estoque mínimo (admin).
 */
function action_estoque_min_get(): void {
    requer_sessao();
    $pdo = db_connect();
    try {
        $rows = $pdo->query("SELECT tipo_sanguineo, minimo_litros FROM estoque_minimo ORDER BY tipo_sanguineo")
                    ->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $rows = array_map(fn($t) => ['tipo_sanguineo' => $t, 'minimo_litros' => '2.00'], TIPOS_VALIDOS);
    }
    echo json_encode($rows);
}

// ─────────────────────────────────────────────────────────────────────────────

function action_buscar_tipo(): void {
    $pdo  = db_connect();
    $stmt = $pdo->prepare(
        "SELECT DISTINCT tipo_sanguineo FROM bolsas_sangue WHERE quantidade > 0 ORDER BY tipo_sanguineo"
    );
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
}

function action_buscar_total(): void {
    $pdo      = db_connect();
    $tipo     = trim($_GET['tipo']     ?? '');
    $data_ini = trim($_GET['data_ini'] ?? '');
    $data_fim = trim($_GET['data_fim'] ?? '');

    // Validações
    if ($tipo && !in_array($tipo, TIPOS_VALIDOS, true)) $tipo = '';
    $data_ini_obj = $data_ini ? DateTime::createFromFormat('Y-m-d', $data_ini) : null;
    $data_fim_obj = $data_fim ? DateTime::createFromFormat('Y-m-d', $data_fim) : null;
    if (!$data_ini_obj) $data_ini = '';
    if (!$data_fim_obj) $data_fim = '';

    $conditions = ['quantidade > 0'];
    $params     = [];

    if ($tipo) {
        $conditions[] = 'tipo_sanguineo = :tipo';
        $params[':tipo'] = $tipo;
    }
    if ($data_ini) {
        $conditions[] = 'data_coleta >= :data_ini';
        $params[':data_ini'] = $data_ini;
    }
    if ($data_fim) {
        $conditions[] = 'data_coleta <= :data_fim';
        $params[':data_fim'] = $data_fim;
    }

    $where = 'WHERE ' . implode(' AND ', $conditions);
    $stmt  = $pdo->prepare(
        "SELECT tipo_sanguineo, SUM(quantidade) AS quantidade
         FROM bolsas_sangue $where
         GROUP BY tipo_sanguineo ORDER BY tipo_sanguineo"
    );
    $stmt->execute($params);
    $bolsas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'tipos_sanguineos' => array_column($bolsas, 'tipo_sanguineo'),
        'quantidades'      => array_column($bolsas, 'quantidade'),
    ]);
}
