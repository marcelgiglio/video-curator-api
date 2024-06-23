<?php

require_once __DIR__ . '/../db/DatabaseDb.php';
require_once __DIR__ . '/../log/log.php';

class LanguageRepository {
    private $db;
    private $table = 'languages';

    public function __construct() {
        $this->db = new Database();
    }

    // Adiciona um novo idioma ao banco de dados
    public function addLanguage($code, $name) {
        $data = [
            'code' => $code,
            'name' => $name
        ];
        return $this->db->insert($this->table, $data);
    }

    // Busca um idioma pelo ID
    public function getLanguageById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $params = ['id' => $id];
        return $this->db->fetchOne($sql, $params);
    }

    // Atualiza os dados de um idioma
    public function updateLanguage($id, $code, $name) {
        $data = [
            'code' => $code,
            'name' => $name
        ];
        $condition = "id = :id";
        $data['id'] = $id; // Adiciona o id ao array de dados para usar no placeholder da condição
        return $this->db->update($this->table, $data, $condition);
    }

    // Exclui um idioma pelo ID
    public function deleteLanguage($id) {
        $condition = "id = :id";
        $params = ['id' => $id];
        return $this->db->delete($this->table, $condition, $params);
    }

    // Lista todos os idiomas
    public function listLanguages() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }
}
