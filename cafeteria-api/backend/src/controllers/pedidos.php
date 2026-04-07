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
        $resultado = $database->executeQuery('SELECT * FROM pedidos');
        $pedidos = $resultado->fetchAll();

        echo json_encode([
            'status' => 'success',
            'data'   => $pedidos
        ]);
        break;

    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);
        $cliente = trim($body['cliente']);

        if(!$cliente){
            echo json_encode([
                'status' => 'error',
                'message' => 'Campo não informado'
            ]);
            break;
        }
        $database->executeQuery("INSERT INTO pedidos (cliente) VALUES (:cliente)", [ ':cliente' => $cliente ]);

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Pedido CADASTRADO com sucesso',
            'idPedidos' => $database->lastInsertId()
        ]);
        
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Informe o id do pedido na URL.'
            ]);
            break;
        }
        $stmt = $database->executeQuery('DELETE FROM pedidos WHERE id = :id', [':id' => $id]);
 
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Pedido não encontrado.'
            ]);
            break;
        }
 
        echo json_encode([
            'status'  => 'success',
            'message' => 'Pedido REMOVIDO com sucesso.',
            'idPedidos' => $id
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