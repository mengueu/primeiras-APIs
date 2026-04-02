<?php
header('Content-Type: application/json'); // Informa ao navegador que a resposta desta API será sempre um objeto JSON (e não um texto ou HTML)

header('Access-Control-Allow-Origin: *'); // Permite o "CORS" (Cross-Origin Resource Sharing): autoriza que seu site (frontend) 
                                          // faça requisições para este servidor, mesmo estando em "origens" diferentes.

header('Access-Control-Allow-Headers: Content-Type'); // Autoriza que o frontend envie informações no cabeçalho, como o "Content-Type" (essencial para JSON)

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Define quais tipos de ações são permitidas nesta API (Buscar, Criar, Editar, Deletar e Pré-verificação)

$method = $_SERVER['REQUEST_METHOD']; // Descobre qual método foi usado (GET para listar, POST para criar, DELETE para apagar, etc.)
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Pega o caminho da URL (ex: /cafeteria-api/backend/produtos) e ignora parâmetros extras (?id=1)
$path = trim($path, '/'); // Remove as barras das extremidades da string para não gerar índices vazios no array
$segments = explode('/',$path); // Transforma a URL em um Array, quebrando-a a cada barra "/" encontrada

$endpoint = $segments[2] ?? ''; // /cafeteria-api/backend/produtos
//                                       [0]        [1]      [2]  

$log = date('d-m-Y H:i:s') . " | $method | $path\n"; // Cria uma linha de texto com Data, Hora, Método usado e o caminho acessado
file_put_contents('logs/log.txt', $log, FILE_APPEND); // Salva essa linha no arquivo log.txt dentro da pasta logs (FILE_APPEND evita apagar o que já existe)

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { // O navegador envia um "OPTIONS" antes do POST ou DELETE para saber se o servidor aceita a requisição.
    http_response_code(200); // Se for OPTIONS, respondemos "OK" (200).
    exit();
}

// O Switch olha para o $segments[2] e decide qual arquivo de controle (controller) deve carregar
switch ($endpoint) {
    case 'categorias':
        require_once 'src/controllers/categorias.php';
        break;

    case 'produtos':
        require_once 'src/controllers/produtos.php';
        break;

    case 'pedidos':
        require_once 'src/controllers/pedidos.php';
        break;

    default: // Caso o usuário digite algo que não existe, retorna erro 404
        http_response_code(404);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Endpoint nao encontrado.'
        ]);
}