<?php
// api/checklists.php
include '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['card_id'])) {
            // Obter checklists de um cartão específico
            $card_id = $conn->real_escape_string($_GET['card_id']);
            $sql = "SELECT ID_Checklist, Nome, fk_Cartao_ID_Cartao FROM Checklist WHERE fk_Cartao_ID_Cartao = $card_id";
            $result = $conn->query($sql);
            $checklists = [];
            while($row = $result->fetch_assoc()) {
                $checklists[] = $row;
            }
            echo json_encode($checklists);
        } else if (isset($_GET['id'])) {
            // Obter uma checklist específica pelo ID
            $id_checklist = $conn->real_escape_string($_GET['id']);
            $sql = "SELECT ID_Checklist, Nome, fk_Cartao_ID_Cartao FROM Checklist WHERE ID_Checklist = $id_checklist";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Checklist não encontrada."]);
            }
        } else {
            // Obter todas as checklists
            $sql = "SELECT ID_Checklist, Nome, fk_Cartao_ID_Cartao FROM Checklist";
            $result = $conn->query($sql);
            $checklists = [];
            while($row = $result->fetch_assoc()) {
                $checklists[] = $row;
            }
            echo json_encode($checklists);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['Nome']) || !isset($data['fk_Cartao_ID_Cartao'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para criar a checklist."]);
            exit();
        }

        $nome = $conn->real_escape_string($data['Nome']);
        $fk_cartao_id_cartao = (int)$data['fk_Cartao_ID_Cartao'];

        $sql = "INSERT INTO Checklist (Nome, fk_Cartao_ID_Cartao) VALUES ('$nome', $fk_cartao_id_cartao)";

        if ($conn->query($sql) === TRUE) {
            http_response_code(201);
            echo json_encode(["message" => "Checklist criada com sucesso", "id" => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao criar checklist: " . $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Checklist']) || !isset($data['Nome'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para atualizar a checklist."]);
            exit();
        }

        $id_checklist = (int)$data['ID_Checklist'];
        $nome = $conn->real_escape_string($data['Nome']);

        $sql = "UPDATE Checklist SET Nome = '$nome' WHERE ID_Checklist = $id_checklist";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Checklist atualizada com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Checklist não encontrada ou nenhum dado alterado."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar checklist: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Checklist'])) {
            http_response_code(400);
            echo json_encode(["message" => "ID da checklist não fornecido para exclusão."]);
            exit();
        }

        $id_checklist = (int)$data['ID_Checklist'];
        $sql = "DELETE FROM Checklist WHERE ID_Checklist = $id_checklist";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Checklist excluída com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Checklist não encontrada para exclusão."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir checklist: " . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}

$conn->close();
?>
