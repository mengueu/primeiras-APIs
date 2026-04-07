<?php

require_once 'src/connection/database.php';
$database = new Database();

$method   = $_SERVER['REQUEST_METHOD'];
$path     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path     = trim($path, '/');
$segments = explode('/', $path);


if (isset($segments[3])) { // cafeteria-api/backend/produtos/ID
    $id = $segments[3]; //      [0]       [1]         [2]
} else {
    $id = null;
}

switch($method){

    case 'GET':
        if ($id) {
            $resultado = $database->executeQuery('SELECT * FROM pedido_itens WHERE pedido_id = :id', [':id' => $id]);
        } else {
            $resultado = $database->executeQuery('SELECT * FROM pedido_itens');
        }
        $itens = $resultado->fetchAll();

        echo json_encode([
            'status' => 'success',
            'data'   => $itens
        ]);
        break;

    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);
        
        $pedido_id  = $body['pedido_id'] ?? null;
        $produto_id = $body['produto_id'] ?? null;
        $quantidade = $body['quantidade'] ?? null;
        $preco      = $body['preco'] ?? null;

        if(!$pedido_id || !$produto_id || !$quantidade || !$preco){
            echo json_encode([
                'status' => 'error',
                'message' => 'Informações do item incompletas'
            ]);
            break;
        }

        $stmtINSERT = "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco) VALUES (:pedido_id, :produto_id, :quantidade, :preco)";
        $database->executeQuery($stmtINSERT, [':pedido_id' => $pedido_id, ':produto_id' => $produto_id, ':quantidade' => $quantidade, ':preco' => $preco]);

        $lastInsertId = $database->lastInsertId(); // lastInsertId deve estar imediatamente depois do INSERT
        
        $stmtUPDATE = "UPDATE pedidos SET total = (SELECT SUM(quantidade * preco) FROM pedido_itens WHERE pedido_id = :pedido_id) WHERE id = :pedido_id";
        $database->executeQuery($stmtUPDATE, [':pedido_id' => $pedido_id]);

        
        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Item ADICIONADO ao pedido com sucesso',
            'idItem'  => $lastInsertId
        ]);
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Informe o id do item na URL.']);
            break;
        }

        $buscaItem = $database->executeQuery('SELECT pedido_id FROM pedido_itens WHERE id = :id', [':id' => $id]);
        $item = $buscaItem->fetch();

        if (!$item) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Item não encontrado.']);
            break;
        }
        $idPedido = $item['pedido_id'];
        $stmtDELETE = $database->executeQuery('DELETE FROM pedido_itens WHERE id = :id', [':id' => $id]);

        // Usamos COALESCE para que, se o pedido ficar vazio, o total vire 0 em vez de NULL
        $stmtUPDATE = "UPDATE pedidos SET total = (SELECT COALESCE(SUM(quantidade * preco), 0) FROM pedido_itens WHERE pedido_id = :pedido_id) WHERE id = :pedido_id";
        $database->executeQuery($stmtUPDATE, [':pedido_id' => $idPedido]);
 
        echo json_encode([
            'status'  => 'success',
            'message' => 'Item REMOVIDO com sucesso e total atualizado.',
            'idItem'  => $id
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Método não permitido.'
        ]);
}
?>