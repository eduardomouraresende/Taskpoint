<?php
// api/cartoes.php
include '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['list_id'])) {
            // Obter cartões de uma lista específica
            $list_id = $conn->real_escape_string($_GET['list_id']);
            $sql = "SELECT ID_Cartao, Titulo, Descricao, Dt_Criacao, Dt_Vencimento, fk_Lista_ID_Lista, fk_Usuario_ID_Usuario FROM Cartao WHERE fk_Lista_ID_Lista = $list_id ORDER BY ID_Cartao ASC"; // Ordena por ID ou outro critério relevante
            $result = $conn->query($sql);
            $cartoes = [];
            while($row = $result->fetch_assoc()) {
                $cartoes[] = $row;
            }
            echo json_encode($cartoes);
        } else if (isset($_GET['id'])) {
            // Obter um cartão específico pelo ID
            $id_cartao = $conn->real_escape_string($_GET['id']);
            $sql = "SELECT ID_Cartao, Titulo, Descricao, Dt_Criacao, Dt_Vencimento, fk_Lista_ID_Lista, fk_Usuario_ID_Usuario FROM Cartao WHERE ID_Cartao = $id_cartao";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Cartão não encontrado."]);
            }
        } else {
            // Obter todos os cartões (geralmente não usado para a UI do Trello)
            $sql = "SELECT ID_Cartao, Titulo, Descricao, Dt_Criacao, Dt_Vencimento, fk_Lista_ID_Lista, fk_Usuario_ID_Usuario FROM Cartao ORDER BY Dt_Criacao DESC";
            $result = $conn->query($sql);
            $cartoes = [];
            while($row = $result->fetch_assoc()) {
                $cartoes[] = $row;
            }
            echo json_encode($cartoes);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['Titulo']) || !isset($data['fk_Lista_ID_Lista']) || !isset($data['fk_Usuario_ID_Usuario'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para criar o cartão."]);
            exit();
        }

        $titulo = $conn->real_escape_string($data['Titulo']);
        $descricao = isset($data['Descricao']) ? $conn->real_escape_string($data['Descricao']) : '';
        $dt_criacao = $conn->real_escape_string($data['Dt_Criacao']); // Frontend deve enviar a data atual
        $dt_vencimento = isset($data['Dt_Vencimento']) && !empty($data['Dt_Vencimento']) ? "'" . $conn->real_escape_string($data['Dt_Vencimento']) . "'" : "NULL";
        $fk_lista_id_lista = (int)$data['fk_Lista_ID_Lista'];
        $fk_usuario_id_usuario = (int)$data['fk_Usuario_ID_Usuario'];

        $sql = "INSERT INTO Cartao (Titulo, Descricao, Dt_Criacao, Dt_Vencimento, fk_Lista_ID_Lista, fk_Usuario_ID_Usuario)
                VALUES ('$titulo', '$descricao', '$dt_criacao', $dt_vencimento, $fk_lista_id_lista, $fk_usuario_id_usuario)";

        if ($conn->query($sql) === TRUE) {
            http_response_code(201);
            echo json_encode(["message" => "Cartão criado com sucesso", "id" => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao criar cartão: " . $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Cartao']) || !isset($data['Titulo'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para atualizar o cartão."]);
            exit();
        }

        $id_cartao = (int)$data['ID_Cartao'];
        $titulo = $conn->real_escape_string($data['Titulo']);
        $descricao = isset($data['Descricao']) ? $conn->real_escape_string($data['Descricao']) : '';
        $dt_vencimento = isset($data['Dt_Vencimento']) && !empty($data['Dt_Vencimento']) ? "'" . $conn->real_escape_string($data['Dt_Vencimento']) . "'" : "NULL";
        $fk_usuario_id_usuario_update = isset($data['fk_Usuario_ID_Usuario']) ? (int)$data['fk_Usuario_ID_Usuario'] : null;

        $sql = "UPDATE Cartao SET Titulo = '$titulo', Descricao = '$descricao', Dt_Vencimento = $dt_vencimento";
        if ($fk_usuario_id_usuario_update !== null) {
             $sql .= ", fk_Usuario_ID_Usuario = $fk_usuario_id_usuario_update";
        }
        $sql .= " WHERE ID_Cartao = $id_cartao";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Cartão atualizado com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Cartão não encontrado ou nenhum dado alterado."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar cartão: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Cartao'])) {
            http_response_code(400);
            echo json_encode(["message" => "ID do cartão não fornecido para exclusão."]);
            exit();
        }

        $id_cartao = (int)$data['ID_Cartao'];
        $sql = "DELETE FROM Cartao WHERE ID_Cartao = $id_cartao";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Cartão excluído com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Cartão não encontrado para exclusão."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir cartão: " . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}

$conn->close();
?>
