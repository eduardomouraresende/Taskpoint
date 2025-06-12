<?php
// api/usuarios.php
// Retorna dados de usuários (para dropdowns e exibição de nomes)

include '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        // Obter um usuário específico pelo ID
        $id_usuario = $conn->real_escape_string($_GET['id']);
        $sql = "SELECT ID_Usuario, Nome, Email FROM Usuario WHERE ID_Usuario = $id_usuario";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Usuário não encontrado."]);
        }
    } else {
        // Obter todos os usuários
        $sql = "SELECT ID_Usuario, Nome, Email FROM Usuario ORDER BY Nome ASC";
        $result = $conn->query($sql);
        $usuarios = [];
        while($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        echo json_encode($usuarios);
    }
} else {
    http_response_code(405); // Método não permitido
    echo json_encode(["message" => "Método não permitido. Utilize GET."]);
}

$conn->close();
?>
