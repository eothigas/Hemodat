<?php
session_start(); // Inicia a sessão

// Definindo a resposta como um array
$response = [];

// Verifica se o usuário está logado
if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
    $response['status'] = 'success';
    $response['usuario_logado'] = true; // Explicitamente define como true
    $response['email'] = $_SESSION['usuario_email']; 
} else {
    $response['status'] = 'error';
    $response['usuario_logado'] = false; // Explicitamente define como false
    $response['message'] = 'Nenhum usuário logado.';
}

// Retorna a resposta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
