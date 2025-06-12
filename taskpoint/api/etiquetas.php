<?php
// api/etiquetas.php
include '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['board_id'])) {
            // Obter etiquetas de um quadro específico
            $board_id = $conn->real_escape_string($_GET['board_id']);
            $sql = "SELECT ID_Etiqueta, Nome, Cor, fk_Quadro_ID_Quadro FROM Etiqueta WHERE fk_Quadro_ID_Quadro = $board_id";
            $result = $conn->query($sql);
            $etiquetas = [];
            while($row = $result->fetch_assoc()) {
                $etiquetas[] = $row;
            }
            echo json_encode($etiquetas);
        } else if (isset($_GET['id'])) {
            // Obter uma etiqueta específica pelo ID
            $id_etiqueta = $conn->real_escape_string($_GET['id']);
            $sql = "SELECT ID_Etiqueta, Nome, Cor, fk_Quadro_ID_Quadro FROM Etiqueta WHERE ID_Etiqueta = $id_etiqueta";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Etiqueta não encontrada."]);
            }
        } else {
            // Obter todas as etiquetas
            $sql = "SELECT ID_Etiqueta, Nome, Cor, fk_Quadro_ID_Quadro FROM Etiqueta";
            $result = $conn->query($sql);
            $etiquetas = [];
            while($row = $result->fetch_assoc()) {
                $etiquetas[] = $row;
            }
            echo json_encode($etiquetas);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['Nome']) || !isset($data['Cor']) || !isset($data['fk_Quadro_ID_Quadro'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para criar a etiqueta."]);
            exit();
        }

        $nome = $conn->real_escape_string($data['Nome']);
        $cor = $conn->real_escape_string($data['Cor']);
        $fk_quadro_id_quadro = (int)$data['fk_Quadro_ID_Quadro'];

        $sql = "INSERT INTO Etiqueta (Nome, Cor, fk_Quadro_ID_Quadro) VALUES ('$nome', '$cor', $fk_quadro_id_quadro)";

        if ($conn->query($sql) === TRUE) {
            http_response_code(201);
            echo json_encode(["message" => "Etiqueta criada com sucesso", "id" => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao criar etiqueta: " . $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Etiqueta']) || !isset($data['Nome']) || !isset($data['Cor'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para atualizar a etiqueta."]);
            exit();
        }

        $id_etiqueta = (int)$data['ID_Etiqueta'];
        $nome = $conn->real_escape_string($data['Nome']);
        $cor = $conn->real_escape_string($data['Cor']);

        $sql = "UPDATE Etiqueta SET Nome = '$nome', Cor = '$cor' WHERE ID_Etiqueta = $id_etiqueta";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Etiqueta atualizada com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Etiqueta não encontrada ou nenhum dado alterado."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar etiqueta: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Etiqueta'])) {
            http_response_code(400);
            echo json_encode(["message" => "ID da Etiqueta não fornecido para exclusão."]);
            exit();
        }

        $id_etiqueta = (int)$data['ID_Etiqueta'];
        $sql = "DELETE FROM Etiqueta WHERE ID_Etiqueta = $id_etiqueta";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Etiqueta excluída com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Etiqueta não encontrada para exclusão."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir etiqueta: " . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}

$conn->close();
?>
