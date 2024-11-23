<?php
session_start(); // Inicia a sessão

// Limpa todas as variáveis de sessão
session_unset();

// Destroi a sessão
session_destroy();

// Define a resposta como um array
$response = [
    'status' => 'success',
    'message' => 'Sessão destruída com sucesso.'
];

// Retorna a resposta como JSON
echo json_encode($response);
?>
