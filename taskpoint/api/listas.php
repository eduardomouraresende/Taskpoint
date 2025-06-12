<?php
// api/listas.php
include '../db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['board_id'])) {
            // Obter listas de um quadro específico
            $board_id = $conn->real_escape_string($_GET['board_id']);
            $sql = "SELECT ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro FROM Lista WHERE fk_Quadro_ID_Quadro = $board_id ORDER BY Ordem ASC";
            $result = $conn->query($sql);
            $listas = [];
            while($row = $result->fetch_assoc()) {
                $listas[] = $row;
            }
            echo json_encode($listas);
        } else if (isset($_GET['id'])) {
            // Obter uma lista específica pelo ID
            $id_lista = $conn->real_escape_string($_GET['id']);
            $sql = "SELECT ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro FROM Lista WHERE ID_Lista = $id_lista";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Lista não encontrada."]);
            }
        } else {
            // Obter todas as listas (geralmente não usado para a UI do Trello)
            $sql = "SELECT ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro FROM Lista ORDER BY Ordem ASC";
            $result = $conn->query($sql);
            $listas = [];
            while($row = $result->fetch_assoc()) {
                $listas[] = $row;
            }
            echo json_encode($listas);
        }
        break;

        case 'POST':
          $data = json_decode(file_get_contents("php://input"), true);
      
          if (!isset($data['Nome']) || !isset($data['fk_Quadro_ID_Quadro'])) {
              http_response_code(400);
              echo json_encode(["message" => "Dados incompletos para criar a lista."]);
              exit();
          }
      
          $nome = $conn->real_escape_string($data['Nome']);
          $fk_quadro_id_quadro = (int)$data['fk_Quadro_ID_Quadro'];
          // Definir uma ordem inicial (pode ser ajustada via drag and drop posteriormente)
          $ordem = isset($data['Ordem']) ? (int)$data['Ordem'] : 1; // <--- ATENÇÃO AQUI
      
          $sql = "INSERT INTO Lista (Nome, Ordem, fk_Quadro_ID_Quadro) VALUES ('$nome', $ordem, $fk_quadro_id_quadro)";
      
          if ($conn->query($sql) === TRUE) {
              http_response_code(201);
              echo json_encode(["message" => "Lista criada com sucesso", "id" => $conn->insert_id]);
          } else {
              http_response_code(500);
              echo json_encode(["message" => "Erro ao criar lista: " . $conn->error]);
          }
          break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Lista']) || !isset($data['Nome'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos para atualizar a lista."]);
            exit();
        }

        $id_lista = (int)$data['ID_Lista'];
        $nome = $conn->real_escape_string($data['Nome']);
        $ordem_clause = "";

        if (isset($data['Ordem'])) {
            $ordem = (int)$data['Ordem'];
            $ordem_clause = ", Ordem = $ordem";
        }

        $sql = "UPDATE Lista SET Nome = '$nome' $ordem_clause WHERE ID_Lista = $id_lista";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Lista atualizada com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Lista não encontrada ou nenhum dado alterado."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar lista: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ID_Lista'])) {
            http_response_code(400);
            echo json_encode(["message" => "ID da lista não fornecido para exclusão."]);
            exit();
        }

        $id_lista = (int)$data['ID_Lista'];
        $sql = "DELETE FROM Lista WHERE ID_Lista = $id_lista";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Lista excluída com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Lista não encontrada para exclusão."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir lista: " . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}

$conn->close();
?>
