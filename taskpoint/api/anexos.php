<?php
// api/anexos.php
include '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['card_id'])) {
            // Obter anexos de um cartão específico
            $card_id = $conn->real_escape_string($_GET['card_id']);
            $sql = "SELECT ID_Anexo, Nome, URL, fk_Cartao_ID_Cartao FROM Anexo WHERE fk_Cartao_ID_Cartao = $card_id";
            $result = $conn->query($sql);
            $anexos = [];
            while($row = $result->fetch_assoc()) {
                $anexos[] = $row;
            }
            echo json_encode($anexos);
        } else if (isset($_GET['id'])) {
            // Obter um anexo específico pelo ID
            $id_anexo = $conn->real_escape_string($_GET['id']);
            $sql = "SELECT ID_Anexo, Nome, URL, fk_Cartao_ID_Cartao FROM Anexo WHERE ID_Anexo = $id_anexo";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Anexo não encontrado."]);
            }
        } else {
            // Obter todos os anexos
            $sql = "SELECT ID_Anexo, Nome, URL, fk_Cartao_ID_Cartao FROM Anexo";
            $result = $conn->query($sql);
            $anexos = [];
            while($row = $result->fetch_assoc()) {
                $anexos[] = $row;
            }
            echo json_encode($anexos);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['Nome']) || !isset($data['URL']) || !isset($data['fk_Cartao_ID_Cartao'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para criar o anexo."]);
            exit();
        }

        $nome = $conn->real_escape_string($data['Nome']);
        $url = $conn->real_escape_string($data['URL']);
        $fk_cartao_id_cartao = (int)$data['fk_Cartao_ID_Cartao'];

        $sql = "INSERT INTO Anexo (Nome, URL, fk_Cartao_ID_Cartao) VALUES ('$nome', '$url', $fk_cartao_id_cartao)";

        if ($conn->query($sql) === TRUE) {
            http_response_code(201);
            echo json_encode(["message" => "Anexo criado com sucesso", "id" => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao criar anexo: " . $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Anexo'])) {
            http_response_code(400);
            echo json_encode(["message" => "ID do Anexo não fornecido para atualização."]);
            exit();
        }

        $id_anexo = (int)$data['ID_Anexo'];
        $update_fields = [];

        if (isset($data['Nome'])) {
            $update_fields[] = "Nome = '" . $conn->real_escape_string($data['Nome']) . "'";
        }
        if (isset($data['URL'])) {
            $update_fields[] = "URL = '" . $conn->real_escape_string($data['URL']) . "'";
        }

        if (empty($update_fields)) {
            http_response_code(400);
            echo json_encode(["message" => "Nenhum dado para atualizar o anexo."]);
            exit();
        }

        $sql = "UPDATE Anexo SET " . implode(", ", $update_fields) . " WHERE ID_Anexo = $id_anexo";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Anexo atualizado com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Anexo não encontrado ou nenhum dado alterado."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar anexo: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Anexo'])) {
            http_response_code(400);
            echo json_encode(["message" => "ID do Anexo não fornecido para exclusão."]);
            exit();
        }

        $id_anexo = (int)$data['ID_Anexo'];
        $sql = "DELETE FROM Anexo WHERE ID_Anexo = $id_anexo";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Anexo excluído com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Anexo não encontrado para exclusão."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir anexo: " . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}

$conn->close();
?>
