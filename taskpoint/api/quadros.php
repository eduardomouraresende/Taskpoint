<?php
// api/quadros.php
include '../db_connection.php';

// Define o método da requisição HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Lógica para obter quadros (um ou todos)
        if (isset($_GET['id'])) {
            // Obter um quadro específico pelo ID
            $id_quadro = $conn->real_escape_string($_GET['id']);
            $sql = "SELECT ID_Quadro, Nome, Descricao, Dt_Criacao, fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe FROM Quadro WHERE ID_Quadro = $id_quadro";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["message" => "Quadro não encontrado."]);
            }
        } else {
            // Obter todos os quadros
            $sql = "SELECT ID_Quadro, Nome, Descricao, Dt_Criacao, fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe FROM Quadro ORDER BY Dt_Criacao DESC";
            $result = $conn->query($sql);
            $quadros = [];
            while($row = $result->fetch_assoc()) {
                $quadros[] = $row;
            }
            echo json_encode($quadros);
        }
        break;

    case 'POST':
        // Lógica para criar um novo quadro
        $data = json_decode(file_get_contents("php://input"), true);

        // Validação básica dos dados recebidos
        if (!isset($data['Nome']) || !isset($data['Dt_Criacao']) || !isset($data['fk_Usuario_ID_Usuario']) || !isset($data['fk_Equipe_ID_Equipe'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Dados incompletos para criar o quadro."]);
            exit();
        }

        $nome = $conn->real_escape_string($data['Nome']);
        $descricao = isset($data['Descricao']) ? $conn->real_escape_string($data['Descricao']) : '';
        $dt_criacao = $conn->real_escape_string($data['Dt_Criacao']);
        $fk_usuario_id_usuario = (int)$data['fk_Usuario_ID_Usuario'];
        $fk_equipe_id_equipe = (int)$data['fk_Equipe_ID_Equipe'];

        $sql = "INSERT INTO Quadro (Nome, Descricao, Dt_Criacao, fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe)
                VALUES ('$nome', '$descricao', '$dt_criacao', $fk_usuario_id_usuario, $fk_equipe_id_equipe)";

        if ($conn->query($sql) === TRUE) {
            http_response_code(201); // Created
            echo json_encode(["message" => "Quadro criado com sucesso", "id" => $conn->insert_id]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["message" => "Erro ao criar quadro: " . $conn->error]);
        }
        break;

    case 'PUT':
        // Lógica para atualizar um quadro existente
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Quadro']) || !isset($data['Nome']) || !isset($data['Descricao'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para atualizar o quadro."]);
            exit();
        }

        $id_quadro = (int)$data['ID_Quadro'];
        $nome = $conn->real_escape_string($data['Nome']);
        $descricao = $conn->real_escape_string($data['Descricao']);

        // Não é comum atualizar Dt_Criacao, fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe via PUT direto
        // Pode-se adicionar se o requisito de negócio permitir.
        $sql = "UPDATE Quadro SET Nome = '$nome', Descricao = '$descricao' WHERE ID_Quadro = $id_quadro";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200); // OK
                echo json_encode(["message" => "Quadro atualizado com sucesso."]);
            } else {
                http_response_code(404); // Not Found ou No Content (se não houve alteração)
                echo json_encode(["message" => "Quadro não encontrado ou nenhum dado alterado."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar quadro: " . $conn->error]);
        }
        break;

    case 'DELETE':
        // Lógica para deletar um quadro
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Quadro'])) {
            http_response_code(400);
            echo json_encode(["message" => "ID do quadro não fornecido para exclusão."]);
            exit();
        }

        $id_quadro = (int)$data['ID_Quadro'];

        // A exclusão em cascata deve ser configurada no banco de dados (FOREIGN KEY ON DELETE CASCADE)
        // Isso garante que listas, cartões, etc., sejam removidos automaticamente.
        $sql = "DELETE FROM Quadro WHERE ID_Quadro = $id_quadro";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Quadro excluído com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Quadro não encontrado para exclusão."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir quadro: " . $conn->error]);
        }
        break;

    default:
        // Método não permitido
        http_response_code(405); // Method Not Allowed
        echo json_encode(["message" => "Método não permitido."]);
        break;
}

$conn->close(); // Fecha a conexão com o banco de dados
?>
