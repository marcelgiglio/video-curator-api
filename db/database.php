<?php

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }

        return $this->conn;
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
        // Implemente a lógica de log aqui.
        // Pode ser um arquivo de log, um sistema de monitoramento de erros, etc.
        // Exemplo: error_log($message, 3, "/path/to/your/logs/error.log");
    }    

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
        $this->executeQuery($sql, $data);
        return $this->conn->lastInsertId();
    }

    public function update($table, $data, $condition) {
        $updates = array_map(function($key) {
            return "$key = :$key";
        }, array_keys($data));

        $sql = "UPDATE $table SET " . implode(", ", $updates) . " WHERE $condition";
        $this->executeQuery($sql, $data);
    }

    public function delete($table, $condition) {
        $sql = "DELETE FROM $table WHERE $condition";
        $this->executeQuery($sql);
    }
}
