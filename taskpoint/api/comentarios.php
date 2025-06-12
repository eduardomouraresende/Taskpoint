<?php
// api/comentarios.php
include '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['card_id'])) {
            // Obter comentários de um cartão específico
            $card_id = $conn->real_escape_string($_GET['card_id']);
            $sql = "SELECT ID_Comentario, Conteudo, Dt_Criacao, fk_Usuario_ID_Usuario, fk_Cartao_ID_Cartao FROM Comentario WHERE fk_Cartao_ID_Cartao = $card_id ORDER BY Dt_Criacao ASC";
            $result = $conn->query($sql);
            $comentarios = [];
            while($row = $result->fetch_assoc()) {
                $comentarios[] = $row;
            }
            echo json_encode($comentarios);
        } else if (isset($_GET['id'])) {
            // Obter um comentário específico pelo ID
            $id_comentario = $conn->real_escape_string($_GET['id']);
            $sql = "SELECT ID_Comentario, Conteudo, Dt_Criacao, fk_Usuario_ID_Usuario, fk_Cartao_ID_Cartao FROM Comentario WHERE ID_Comentario = $id_comentario";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Comentário não encontrado."]);
            }
        } else {
            // Obter todos os comentários
            $sql = "SELECT ID_Comentario, Conteudo, Dt_Criacao, fk_Usuario_ID_Usuario, fk_Cartao_ID_Cartao FROM Comentario ORDER BY Dt_Criacao DESC";
            $result = $conn->query($sql);
            $comentarios = [];
            while($row = $result->fetch_assoc()) {
                $comentarios[] = $row;
            }
            echo json_encode($comentarios);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['Conteudo']) || !isset($data['fk_Usuario_ID_Usuario']) || !isset($data['fk_Cartao_ID_Cartao'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para criar o comentário."]);
            exit();
        }

        $conteudo = $conn->real_escape_string($data['Conteudo']);
        $dt_criacao = isset($data['Dt_Criacao']) ? "'" . $conn->real_escape_string($data['Dt_Criacao']) . "'" : "NOW()";
        $fk_usuario_id_usuario = (int)$data['fk_Usuario_ID_Usuario'];
        $fk_cartao_id_cartao = (int)$data['fk_Cartao_ID_Cartao'];

        $sql = "INSERT INTO Comentario (Conteudo, Dt_Criacao, fk_Usuario_ID_Usuario, fk_Cartao_ID_Cartao)
                VALUES ('$conteudo', $dt_criacao, $fk_usuario_id_usuario, $fk_cartao_id_cartao)";

        if ($conn->query($sql) === TRUE) {
            http_response_code(201);
            echo json_encode(["message" => "Comentário criado com sucesso", "id" => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao criar comentário: " . $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Comentario']) || !isset($data['Conteudo'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para atualizar o comentário."]);
            exit();
        }

        $id_comentario = (int)$data['ID_Comentario'];
        $conteudo = $conn->real_escape_string($data['Conteudo']);

        $sql = "UPDATE Comentario SET Conteudo = '$conteudo' WHERE ID_Comentario = $id_comentario";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Comentário atualizado com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Comentário não encontrado ou nenhum dado alterado."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar comentário: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Comentario'])) {
            http_response_code(400);
            echo json_encode(["message" => "ID do Comentário não fornecido para exclusão."]);
            exit();
        }

        $id_comentario = (int)$data['ID_Comentario'];
        $sql = "DELETE FROM Comentario WHERE ID_Comentario = $id_comentario";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Comentário excluído com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Comentário não encontrado para exclusão."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir comentário: " . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}

$conn->close();
?>
