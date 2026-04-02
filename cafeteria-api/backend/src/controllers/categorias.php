<?php

require_once 'src/connection/database.php';
$database = new Database();

$method   = $_SERVER['REQUEST_METHOD'];
$path     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path     = trim($path, '/');
$segments = explode('/', $path);

if (isset($segments[3])) { // /cafeteria-api/backend/categorias/id
    $id = $segments[3];//          [0]         [1]       [2]    [3]
} else {
    $id = null;
}

switch($method){

    case 'GET':
        $resultado = $database->executeQuery('SELECT * FROM categorias');
        $categorias = $resultado->fetchAll();

        echo json_encode([
            'status' => 'success',
            'data'   => $categorias
        ]);
        break;

    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);
        $nome = trim($body['nome']);

        if(!$nome){
            echo json_encode([
                'status' => 'error',
                'message' => 'Campo não informado'
            ]);
            break;
        }
        $database->executeQuery(
            "INSERT INTO categorias (nome) VALUES (:nome)",
            [ ':nome' => $nome ]
        );

        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Categoria CADASTRADA com sucesso',
            'idCategoria' => $database->lastInsertId()
        ]);
        
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Informe o id da categoria na URL.'
            ]);
            break;
        }
        $stmt = $database->executeQuery('DELETE FROM categorias WHERE id = :id', [':id' => $id]);
 
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Categoria não encontrada.'
            ]);
            break;
        }
 
        echo json_encode([
            'status'  => 'success',
            'message' => 'Categoria REMOVIDA com sucesso.',
            'idCategoria' => $id
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