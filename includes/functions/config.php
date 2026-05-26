<?php
/**
 * config.php - Configuração central da aplicação Hemodat.
 */

// ─── Sessão ───────────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── URLs ────────────────────────────────────────────────────────────────────
define('BASE_URL_LOCAL',  'http://localhost/_Pessoal/Hemodat');
define('BASE_URL_ONLINE', 'https://hemodatgp.com');

// Detecção automática de ambiente por hostname
$_host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('IS_LOCAL',  in_array($_host, ['localhost', '127.0.0.1'], true));
define('BASE_URL',  IS_LOCAL ? BASE_URL_LOCAL : BASE_URL_ONLINE);

/**
 * Protege página: redireciona para login se não estiver autenticado.
 * Chame no topo de qualquer página que exija login (após include config.php).
 */
function require_auth(): void {
    if (empty($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

// ─── Banco de dados (local × produção) ───────────────────────────────────────
if (IS_LOCAL) {
    // XAMPP local — MySQL padrão: root sem senha
    // Crie o banco "hemodat" via phpMyAdmin ou rode: CREATE DATABASE hemodat CHARACTER SET utf8mb4;
    define('DB_HOST',    'localhost');
    define('DB_NAME',    'efegduik_gphemodat');
    define('DB_USER',    'root');
    define('DB_PASS',    '');
} else {
    // Produção (Hostinger / servidor online)
    define('DB_HOST',    'localhost');
    define('DB_NAME',    'efegduik_gphemodat');
    define('DB_USER',    'efegduik_gphemodat');
    define('DB_PASS',    'fHCXpD4sACYN8EyEd4QG');
}
define('DB_CHARSET', 'utf8mb4');

// ─── Constantes de negócio ───────────────────────────────────────────────────
// Tipos sanguíneos aceitos (whitelist)
define('TIPOS_VALIDOS', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);

// TTL do código de recuperação de senha (em minutos)
define('RESET_CODE_TTL', 15);

// Dias de antecedência para alerta de vencimento de bolsas
define('DIAS_ALERTA_VENCIMENTO', 7);

/**
 * Cria e retorna uma conexão PDO.
 * Em caso de falha, emite JSON de erro e encerra.
 */
function db_connect(): PDO {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,        PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Erro ao conectar ao banco de dados.']);
        exit;
    }
}
