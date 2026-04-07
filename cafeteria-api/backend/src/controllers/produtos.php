<?php
require_once 'src/connection/database.php';
$database = new Database();

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');
$segments = explode('/', $path);

if (isset($segments[3])) { // cafeteria-api/backend/produtos/ID
    $id = $segments[3]; //      [0]       [1]         [2]
} else {
    $id = null;
}

switch($method){

    // Visualizar produtos do banco de dados através do método GET
    case 'GET':
        $resultado = $database->executeQuery('SELECT * FROM produtos'); // Executa a consulta SQL para pegar todos os itens da tabela.
        $produtos = $resultado->fetchAll(); // fetchAll() transforma o resultado do banco em um Array do PHP.

        echo json_encode([ // Envia a resposta em formato JSON para o JavaScript ler.
            'status' => 'success',
            'data' => $produtos
        ]);
        break;
    
    // Cadastrar novos produtos através do método POST
    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true); // file_get_contents('php://input') lê o corpo (body) do JSON enviado pelo JavaScript.
                                                                     // json_decode(..., true) transforma esse JSON em um Array Associativo do PHP.

        // Capturamos os dados do array e usamos o trim() para remover espaços vazios acidentais.
        // O operador '?' verifica se o dado existe, evitando erros de "index undefined".                                                             
        $nome = isset($body['nome']) ? trim($body['nome']) : '';
        $preco = isset($body['preco']) ? trim($body['preco']) : '';
        $categorias = isset($body['categorias']) ? trim($body['categorias']) : '';

        if(!$nome || !$preco || !$categorias){ // Validação simples: se algum campo estiver vazio, para a execução e avisa o usuário.
            echo json_encode([
                'status' => 'error',
                'message' => 'Algum campo não informado.'
            ]);
            break;
        }
        $database->executeQuery( // Executa o INSERT usando parâmetros para segurança total contra SQL Injection.
            "INSERT INTO produtos (nome, preco, categoria_id, disponivel) VALUES (:nome, :preco, :categoria_id, :disponivel)",
            [ ':nome' => $nome, ':preco' => $preco, ':categoria_id' => $categorias, ':disponivel' => 1 ]
        );

        http_response_code(201); // Código 201 significa "Created" (Criado com sucesso).
        echo json_encode([
            'status' => 'success',
            'message' => 'Produto CADASTRADO com sucesso',
            'idProduto' => $database->lastInsertId()
        ]);
        
        break;
    
    // Remover algum produto através do método DELETE
    case 'DELETE':
        if (!$id) { // Verifica se o ID foi passado na URL (ex: .../produtos/15).
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Informe o id do produto na URL.'
            ]);
            break;
        }
        $stmt = $database->executeQuery('DELETE FROM produtos WHERE id = :id', [':id' => $id]); // Executa a remoção baseada no ID capturado no topo do arquivo.
 
        // rowCount() verifica quantas linhas foram afetadas no banco. 
        // Se for 0, significa que o ID enviado não existia no banco
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Produto não encontrado.',
                'idProduto' => $id
            ]);
            break;
        }
 
        echo json_encode([
            'status'  => 'success',
            'message' => 'Produto REMOVIDO com sucesso.',
            'idProduto' => $id
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