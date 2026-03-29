<?php

header('Content-Type: application/json'); 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type'); 
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); 

$method = $_SERVER['REQUEST_METHOD']; 
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); 
$path = trim($path, '/');
$segments = explode('/', $path);
$endpoint = $segments[1] ?? ''; 

switch ($endpoint) {
    case 'hello':
        echo json_encode([
            'status' => 'success',
            'message' => 'Olá! A API está funcionando!'
        ]);
        break;
    
    case 'echo':
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
        } 
        else {
            $input = "Metodo GET";
        }
        echo json_encode([
            'status' => 'success',
            'echo' => $input
        ]);
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Endpoint nao encontrado.'
        ]);
}
?>