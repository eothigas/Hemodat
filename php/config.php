<?php
/**
 * config.php — Configuração central do banco de dados e constantes da aplicação.
 * Inclua este arquivo via require_once em todos os arquivos PHP que precisam de DB.
 */

define('DB_HOST',     'localhost');
define('DB_NAME',     'efegduik_gphemodat');
define('DB_USER',     'efegduik_gphemodat');
define('DB_PASS',     'fHCXpD4sACYN8EyEd4QG');
define('DB_CHARSET',  'utf8');

// Tipos sanguíneos aceitos (whitelist)
define('TIPOS_VALIDOS', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);

// TTL do código de recuperação de senha (em minutos)
define('RESET_CODE_TTL', 15);

/**
 * Cria e retorna uma conexão PDO reutilizável.
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
