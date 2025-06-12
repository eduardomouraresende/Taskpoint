<?php
// api/itemchecklists.php
include '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['checklist_id'])) {
            // Obter itens de checklist de uma checklist específica
            $checklist_id = $conn->real_escape_string($_GET['checklist_id']);
            $sql = "SELECT ID_ItemChecklist, Nome, Status, fk_Checklist_ID_Checklist FROM ItemChecklist WHERE fk_Checklist_ID_Checklist = $checklist_id ORDER BY ID_ItemChecklist ASC";
            $result = $conn->query($sql);
            $items = [];
            while($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            echo json_encode($items);
        } else if (isset($_GET['id'])) {
            // Obter um item de checklist específico pelo ID
            $id_item = $conn->real_escape_string($_GET['id']);
            $sql = "SELECT ID_ItemChecklist, Nome, Status, fk_Checklist_ID_Checklist FROM ItemChecklist WHERE ID_ItemChecklist = $id_item";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Item de Checklist não encontrado."]);
            }
        } else {
            // Obter todos os itens de checklist
            $sql = "SELECT ID_ItemChecklist, Nome, Status, fk_Checklist_ID_Checklist FROM ItemChecklist";
            $result = $conn->query($sql);
            $items = [];
            while($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            echo json_encode($items);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['Nome']) || !isset($data['fk_Checklist_ID_Checklist'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para criar o item de checklist."]);
            exit();
        }

        $nome = $conn->real_escape_string($data['Nome']);
        $status = isset($data['Status']) ? $conn->real_escape_string($data['Status']) : 'Pendente';
        $fk_checklist_id_checklist = (int)$data['fk_Checklist_ID_Checklist'];

        $sql = "INSERT INTO ItemChecklist (Nome, Status, fk_Checklist_ID_Checklist) VALUES ('$nome', '$status', $fk_checklist_id_checklist)";

        if ($conn->query($sql) === TRUE) {
            http_response_code(201);
            echo json_encode(["message" => "Item de Checklist criado com sucesso", "id" => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao criar item de checklist: " . $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_ItemChecklist'])) {
            http_response_code(400);
            echo json_encode(["message" => "ID do Item de Checklist não fornecido para atualização."]);
            exit();
        }

        $id_item = (int)$data['ID_ItemChecklist'];
        $update_fields = [];

        if (isset($data['Nome'])) {
            $update_fields[] = "Nome = '" . $conn->real_escape_string($data['Nome']) . "'";
        }
        if (isset($data['Status'])) {
            $update_fields[] = "Status = '" . $conn->real_escape_string($data['Status']) . "'";
        }

        if (empty($update_fields)) {
            http_response_code(400);
            echo json_encode(["message" => "Nenhum dado para atualizar o item de checklist."]);
            exit();
        }

        $sql = "UPDATE ItemChecklist SET " . implode(", ", $update_fields) . " WHERE ID_ItemChecklist = $id_item";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Item de Checklist atualizado com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Item de Checklist não encontrado ou nenhum dado alterado."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar item de checklist: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_ItemChecklist'])) {
            http_response_code(400);
            echo json_encode(["message" => "ID do Item de Checklist não fornecido para exclusão."]);
            exit();
        }

        $id_item = (int)$data['ID_ItemChecklist'];
        $sql = "DELETE FROM ItemChecklist WHERE ID_ItemChecklist = $id_item";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Item de Checklist excluído com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Item de Checklist não encontrado para exclusão."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir item de checklist: " . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}

$conn->close();
?>
