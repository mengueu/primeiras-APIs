<?php

class Database extends PDO
{
    // Configurações de acesso:
    private $DB_nome = 'cafeteria';
    private $DB_usuario = 'root';
    private $DB_senha = '';
    private $DB_host = '127.0.0.1';
    private $DB_porta = 3306;

    public function __construct()
    {
        try {
            parent::__construct(
                // DSN (Data Source Name). 
                "mysql:host=$this->DB_host;port=$this->DB_porta;dbname=$this->DB_nome;charset=utf8", // IMPORTANTE: Sem espaços entre os sinais de "=" e ";" para evitar o erro "No database selected".
                $this->DB_usuario,
                $this->DB_senha,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Configuração para o PDO lançar exceções (erros) caso algo falhe no SQL
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Configura o retorno padrão como Array Associativo (ex: ['nome' => 'Café'])
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500); // Se a conexão falhar (ex: MySQL desligado), retorna erro 500 para a API
            echo json_encode([
                'status'  => 'error',
                'message' => 'Falha na conexão com o banco.',
                'detalhe' => $e->getMessage()
            ]);
            exit;
        }
    }

    private function setParameters($statement, $key, $value){ // Método auxiliar para vincular valores aos parâmetros do SQL (proteção contra SQL Injection)
        $statement->bindValue($key, $value); // O bindValue garante que o que vier do usuário seja tratado apenas como "texto" ou "valor"
    }

    private function mountQuery($statement, $parameters){ // Método que percorre o array de parâmetros e chama o setParameters para cada um
        foreach ($parameters as $key => $value) {
            $this->setParameters($statement, $key, $value);
        }
    }

    public function executeQuery(string $query, array $parameters = []){ // Ele simplifica o processo de: Preparar -> Vincular Dados -> Executar

        $statement = $this->prepare($query); // 1. Prepara a query (ex: INSERT INTO produtos ... VALUES (:nome))
        $this->mountQuery($statement, $parameters);// 2. Coloca os valores reais no lugar dos "apelidos" (ex: :nome vira 'Expresso')
        $statement->execute(); // 3. Executa a tarefa no banco de dados
        return $statement; // Retorna o resultado para que o Controller possa ler (fetchAll) ou contar linhas (rowCount)
    }
}