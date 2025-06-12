<?php
// api/cartaoetiqueta.php
// Gerencia a tabela de associação CartaoEtiqueta

include '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['card_id'])) {
            // Obter etiquetas associadas a um cartão específico
            $card_id = $conn->real_escape_string($_GET['card_id']);
            $sql = "SELECT fk_Cartao_ID_Cartao, fk_Etiqueta_ID_Etiqueta FROM CartaoEtiqueta WHERE fk_Cartao_ID_Cartao = $card_id";
            $result = $conn->query($sql);
            $cartao_etiquetas = [];
            while($row = $result->fetch_assoc()) {
                $cartao_etiquetas[] = $row;
            }
            echo json_encode($cartao_etiquetas);
        } else if (isset($_GET['label_id'])) {
            // Obter cartões associados a uma etiqueta específica
            $label_id = $conn->real_escape_string($_GET['label_id']);
            $sql = "SELECT fk_Cartao_ID_Cartao, fk_Etiqueta_ID_Etiqueta FROM CartaoEtiqueta WHERE fk_Etiqueta_ID_Etiqueta = $label_id";
            $result = $conn->query($sql);
            $cartao_etiquetas = [];
            while($row = $result->fetch_assoc()) {
                $cartao_etiquetas[] = $row;
            }
            echo json_encode($cartao_etiquetas);
        } else {
            // Obter todas as associações
            $sql = "SELECT fk_Cartao_ID_Cartao, fk_Etiqueta_ID_Etiqueta FROM CartaoEtiqueta";
            $result = $conn->query($sql);
            $cartao_etiquetas = [];
            while($row = $result->fetch_assoc()) {
                $cartao_etiquetas[] = $row;
            }
            echo json_encode($cartao_etiquetas);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['fk_Cartao_ID_Cartao']) || !isset($data['fk_Etiqueta_ID_Etiqueta'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para criar a associação Cartão-Etiqueta."]);
            exit();
        }

        $fk_cartao_id_cartao = (int)$data['fk_Cartao_ID_Cartao'];
        $fk_etiqueta_id_etiqueta = (int)$data['fk_Etiqueta_ID_Etiqueta'];

        // Evitar duplicação
        $check_sql = "SELECT COUNT(*) FROM CartaoEtiqueta WHERE fk_Cartao_ID_Cartao = $fk_cartao_id_cartao AND fk_Etiqueta_ID_Etiqueta = $fk_etiqueta_id_etiqueta";
        $check_result = $conn->query($check_sql);
        $count = $check_result->fetch_row()[0];

        if ($count > 0) {
            http_response_code(409); // Conflict
            echo json_encode(["message" => "Associação Cartão-Etiqueta já existe."]);
            exit();
        }

        $sql = "INSERT INTO CartaoEtiqueta (fk_Cartao_ID_Cartao, fk_Etiqueta_ID_Etiqueta) VALUES ($fk_cartao_id_cartao, $fk_etiqueta_id_etiqueta)";

        if ($conn->query($sql) === TRUE) {
            http_response_code(201);
            echo json_encode(["message" => "Associação Cartão-Etiqueta criada com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao criar associação Cartão-Etiqueta: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['fk_Cartao_ID_Cartao']) || !isset($data['fk_Etiqueta_ID_Etiqueta'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para excluir a associação Cartão-Etiqueta."]);
            exit();
        }

        $fk_cartao_id_cartao = (int)$data['fk_Cartao_ID_Cartao'];
        $fk_etiqueta_id_etiqueta = (int)$data['fk_Etiqueta_ID_Etiqueta'];

        $sql = "DELETE FROM CartaoEtiqueta WHERE fk_Cartao_ID_Cartao = $fk_cartao_id_cartao AND fk_Etiqueta_ID_Etiqueta = $fk_etiqueta_id_etiqueta";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Associação Cartão-Etiqueta excluída com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Associação Cartão-Etiqueta não encontrada para exclusão."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir associação Cartão-Etiqueta: " . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}

$conn->close();
?>
