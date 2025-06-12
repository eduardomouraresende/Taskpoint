<?php
// api/db_connection.php

// Configurações do banco de dados
$servername = "localhost"; 
$username = "root"; // Seu nome de usuário do MySQL
$password = ""; // Sua senha do MySQL (deixe em branco se não houver)
$dbname = "taskpoint"; // O nome do banco de dados que você criou (taskpoint)

// Cria a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    // Em caso de erro de conexão, retorna um erro JSON e encerra o script
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Define o cabeçalho para JSON para todas as respostas das APIs
header('Content-Type: application/json; charset=utf-8');

// Opcional: Define o charset da conexão para UTF-8
$conn->set_charset("utf8mb4");

?>
