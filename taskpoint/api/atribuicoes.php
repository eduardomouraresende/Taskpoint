<?php
// api/atribuicoes.php
include '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['card_id'])) {
            // Obter atribuições de um cartão específico
            $card_id = $conn->real_escape_string($_GET['card_id']);
            $sql = "SELECT fk_Cartao_ID_Cartao, fk_Usuario_ID_Usuario FROM Atribuicao WHERE fk_Cartao_ID_Cartao = $card_id";
            $result = $conn->query($sql);
            $atribuicoes = [];
            while($row = $result->fetch_assoc()) {
                $atribuicoes[] = $row;
            }
            echo json_encode($atribuicoes);
        } else if (isset($_GET['user_id'])) {
            // Obter atribuições de um usuário específico
            $user_id = $conn->real_escape_string($_GET['user_id']);
            $sql = "SELECT fk_Cartao_ID_Cartao, fk_Usuario_ID_Usuario FROM Atribuicao WHERE fk_Usuario_ID_Usuario = $user_id";
            $result = $conn->query($sql);
            $atribuicoes = [];
            while($row = $result->fetch_assoc()) {
                $atribuicoes[] = $row;
            }
            echo json_encode($atribuicoes);
        } else {
            // Obter todas as atribuições
            $sql = "SELECT fk_Cartao_ID_Cartao, fk_Usuario_ID_Usuario FROM Atribuicao";
            $result = $conn->query($sql);
            $atribuicoes = [];
            while($row = $result->fetch_assoc()) {
                $atribuicoes[] = $row;
            }
            echo json_encode($atribuicoes);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['fk_Cartao_ID_Cartao']) || !isset($data['fk_Usuario_ID_Usuario'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para criar a atribuição."]);
            exit();
        }

        $fk_cartao_id_cartao = (int)$data['fk_Cartao_ID_Cartao'];
        $fk_usuario_id_usuario = (int)$data['fk_Usuario_ID_Usuario'];

        // Evitar duplicação de atribuições
        $check_sql = "SELECT COUNT(*) FROM Atribuicao WHERE fk_Cartao_ID_Cartao = $fk_cartao_id_cartao AND fk_Usuario_ID_Usuario = $fk_usuario_id_usuario";
        $check_result = $conn->query($check_sql);
        $count = $check_result->fetch_row()[0];

        if ($count > 0) {
            http_response_code(409); // Conflict
            echo json_encode(["message" => "Atribuição já existe para este cartão e usuário."]);
            exit();
        }

        $sql = "INSERT INTO Atribuicao (fk_Cartao_ID_Cartao, fk_Usuario_ID_Usuario) VALUES ($fk_cartao_id_cartao, $fk_usuario_id_usuario)";

        if ($conn->query($sql) === TRUE) {
            http_response_code(201);
            echo json_encode(["message" => "Atribuição criada com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao criar atribuição: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['fk_Cartao_ID_Cartao']) || !isset($data['fk_Usuario_ID_Usuario'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para excluir a atribuição."]);
            exit();
        }

        $fk_cartao_id_cartao = (int)$data['fk_Cartao_ID_Cartao'];
        $fk_usuario_id_usuario = (int)$data['fk_Usuario_ID_Usuario'];

        $sql = "DELETE FROM Atribuicao WHERE fk_Cartao_ID_Cartao = $fk_cartao_id_cartao AND fk_Usuario_ID_Usuario = $fk_usuario_id_usuario";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Atribuição excluída com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Atribuição não encontrada para exclusão."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir atribuição: " . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}

$conn->close();
?>
