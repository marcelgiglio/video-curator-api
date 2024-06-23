<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../log/log.php';

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
        // Usando as constantes definidas no config.php
        $this->host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
    }

    private function connect() {
        $this->conn = null;

        try {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Define o charset da conexão para UTF-8
            $this->conn->exec("SET NAMES 'utf8mb4'");
            $this->conn->exec("SET CHARACTER SET utf8mb4");
            $this->conn->exec("SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");
        } catch (PDOException $e) {
            logError('Connection Error: ' . $e->getMessage());
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
            logError($e->getMessage());
            return false;
        }
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

    public function delete($table, $condition, $params = []) {
        $sql = "DELETE FROM $table WHERE $condition";
        return $this->executeQuery($sql, $params) !== false;
    }

    public function closeConnection() {
        $this->conn = null;
    }
    
}
