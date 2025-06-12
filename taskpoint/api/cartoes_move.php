<?php
// api/cartoes_move.php
// Endpoint para chamar a Stored Procedure MoverCartaoEntreListas

include '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['ID_Cartao']) || !isset($data['ID_Lista_Destino'])) {
        http_response_code(400);
        echo json_encode(["message" => "Dados incompletos para mover o cartão. (ID_Cartao, ID_Lista_Destino são obrigatórios)"]);
        exit();
    }

    $id_cartao = (int)$data['ID_Cartao'];
    $id_lista_destino = (int)$data['ID_Lista_Destino'];

    // Chamada da Stored Procedure
    // É importante sanitizar os inputs mesmo para stored procedures.
    $sql = "CALL MoverCartaoEntreListas($id_cartao, $id_lista_destino)";

    if ($conn->query($sql) === TRUE) {
        http_response_code(200);
        echo json_encode(["message" => "Cartão movido com sucesso para a lista de destino."]);
    } else {
        // A stored procedure pode retornar um SIGNAL SQLSTATE em caso de erro
        // Capturar isso aqui é um pouco mais complexo sem aprofundar muito.
        // A mensagem de erro do $conn->error pode ser útil.
        http_response_code(500);
        echo json_encode(["message" => "Erro ao mover cartão: " . $conn->error]);
    }

} else {
    http_response_code(405); // Método não permitido
    echo json_encode(["message" => "Método não permitido. Utilize POST."]);
}

$conn->close();
?>
