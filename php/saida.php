<?php
session_start(); // Inicia a sessão para pegar o email do usuário

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dados do formulário
    $tipo = $_POST['tipo'] ?? null;
    $quantidade = $_POST['litros'] ?? null;
    $data_saida = $_POST['saida'] ?? null;

    // Verifica se os campos obrigatórios foram preenchidos
    if (!$tipo || !$quantidade || !$data_saida) {
        echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
        exit;
    }

    // Converte a data para o formato YYYY-MM-DD
    $data_saida_formatada = DateTime::createFromFormat('d/m/Y', $data_saida);
    if (!$data_saida_formatada) {
        echo json_encode(['status' => 'error', 'message' => 'Formato de data inválido.']);
        exit;
    }
    $data_saida_formatada = $data_saida_formatada->format('Y-m-d'); // Formata para o formato correto

    // Conexão com o banco de dados
    $host = "localhost";
    $dbname = "efegduik_gphemodat";
    $username = "efegduik_gphemodat";
    $password = "fHCXpD4sACYN8EyEd4QG";

    try {
        // Conexão com o banco de dados
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
        exit;
    }

    // Verifica a quantidade disponível para o tipo sanguíneo
    $sql = "SELECT quantidade, data_validade FROM bolsas_sangue WHERE tipo_sanguineo = :tipo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':tipo' => $tipo]);
    $bolsa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bolsa) {
        echo json_encode(['status' => 'error', 'message' => 'Tipo sanguíneo não encontrado.']);
        exit;
    }

    // Verifica a quantidade disponível
    if ($quantidade > $bolsa['quantidade']) {
        echo json_encode(['status' => 'error', 'message' => 'Quantidade indisponível para este tipo sanguíneo.']);
        exit;
    }

    // Verifica a validade da data de saída
    $data_validade = $bolsa['data_validade'];
    $data_atual = date('Y-m-d');

    if ($data_saida_formatada > $data_validade || $data_saida_formatada < $data_atual) {
        echo json_encode(['status' => 'error', 'message' => 'Data de saída inválida. A data deve estar entre a data atual e a data de validade.']);
        exit;
    }

    // Insere as informações na tabela saída_bolsas_sangue
    $email = $_SESSION['usuario_email']; // Assume que o email já está na sessão

    $sql = "INSERT INTO saida_bolsas_sangue (email, tipo_sanguineo, quantidade, data_saida) 
            VALUES (:email, :tipo, :litros, :saida)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':tipo' => $tipo,
            ':email' => $email,
            ':litros' => $quantidade,
            ':saida' => $data_saida_formatada,
        ]);
        
        // Se a inserção for bem-sucedida
        echo json_encode(['status' => 'success', 'message' => 'Registro de saída realizado com sucesso!']);
    } catch (PDOException $e) {
        // Se houver erro na execução da query
        echo json_encode(['status' => 'error', 'message' => 'Erro ao registrar a saída: ' . $e->getMessage()]);
    }
}
?>
