<?php
// api/equipes.php
// Retorna dados de equipes (para dropdowns)

include '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        // Obter uma equipe específica pelo ID
        $id_equipe = $conn->real_escape_string($_GET['id']);
        $sql = "SELECT ID_Equipe, Nome, Dt_Criacao FROM Equipe WHERE ID_Equipe = $id_equipe";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Equipe não encontrada."]);
        }
    } else {
        // Obter todas as equipes
        $sql = "SELECT ID_Equipe, Nome, Dt_Criacao FROM Equipe ORDER BY Nome ASC";
        $result = $conn->query($sql);
        $equipes = [];
        while($row = $result->fetch_assoc()) {
            $equipes[] = $row;
        }
        echo json_encode($equipes);
    }
} else {
    http_response_code(405); // Método não permitido
    echo json_encode(["message" => "Método não permitido. Utilize GET."]);
}

$conn->close();
?>
