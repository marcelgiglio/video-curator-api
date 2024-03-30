<?php

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        // Carrega configurações do banco de dados
        $this->loadDbConfig();

        // Estabelece conexão com o banco de dados
        $this->connect();

        // Configurações iniciais
        $this->setInitialSettings();
    }

    private function loadDbConfig() {
        // Supondo que as configurações estão definidas em variáveis de ambiente
        $this->host = getenv('DB_HOST');
        $this->db_name = getenv('DB_NAME');
        $this->username = getenv('DB_USER');
        $this->password = getenv('DB_PASS');
    }

    private function connect() {
        $this->conn = null;

        try {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->logError('Connection Error: ' . $e->getMessage());
        }
    }

    private function setInitialSettings() {
        // Define o fuso horário padrão, se necessário, ou outras configurações iniciais
        date_default_timezone_set('America/Sao_Paulo'); // Exemplo de fuso horário
    }

    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            // Loga o erro internamente, sem expor detalhes ao usuário.
            // A função logError é um placeholder para sua implementação de log.
            $this->logError($e->getMessage());
    
            // Retorna false para indicar falha na execução.
            return false;
        }
    }

    private function logError($message) {
        // Define o caminho do arquivo de log
        $logFilePath = __DIR__ . '/../log/error.log';
    
        // Obtém a data e hora atual
        $currentTime = date('Y-m-d H:i:s');
    
        // Formata a mensagem de log
        $logMessage = "[$currentTime] ERROR: $message\n";
    
        // Escreve a mensagem de erro no arquivo de log
        // Usando o flag FILE_APPEND para adicionar ao arquivo em vez de sobrescrevê-lo
        // E LOCK_EX para evitar que qualquer outro processo escreva no arquivo ao mesmo tempo
        file_put_contents($logFilePath, $logMessage, FILE_APPEND | LOCK_EX);
    }

    // Métodos CRUD genéricos...
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function fetchOne($sql, $params = []) {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function insert($table, $data) {
        $keys = array_keys($data);
        $fields = implode(", ", $keys);
        $placeholders = ":" . implode(", :", $keys);

        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        $stmt = $this->executeQuery($sql, $data);
        
        return $stmt ? $this->conn->lastInsertId() : false;
    }

    public function update($table, $data, $condition) {
        $updates = array_map(function($key) {
            return "$key = :$key";
        }, array_keys($data));

        $sql = "UPDATE $table SET " . implode(", ", $updates) . " WHERE $condition";
        return $this->executeQuery($sql, $data) !== false;
    }

    public function delete($table, $condition) {
        $sql = "DELETE FROM $table WHERE $condition";
        return $this->executeQuery($sql) !== false;
    }
}
