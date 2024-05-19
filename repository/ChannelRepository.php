<?php

require_once __DIR__ . '/../db/database.php';

class ChannelRepository {
    private $db;
    private $table = 'channels';

    public function __construct() {
        $this->db = new Database();
    }

    // Adiciona um novo canal ao banco de dados
    public function addChannel($name, $country, $language, $url, $description) {
        $data = [
            'name' => $name,
            'country' => $country,
            'language' => $language,
            'url' => $url,
            'description' => $description
        ];
        return $this->db->insert($this->table, $data);
    }

    // Busca um canal pelo ID
    public function getChannelById($channel_id) {
        $sql = "SELECT * FROM {$this->table} WHERE channel_id = :channel_id";
        $params = ['channel_id' => $channel_id];
        return $this->db->fetchOne($sql, $params);
    }

    // Atualiza os dados de um canal
    public function updateChannel($channel_id, $name, $country, $language, $url, $description) {
        $data = [
            'name' => $name,
            'country' => $country,
            'language' => $language,
            'url' => $url,
            'description' => $description
        ];
        $condition = "channel_id = :channel_id";
        $data['channel_id'] = $channel_id; // Adiciona o channel_id ao array de dados para usar no placeholder da condição
        return $this->db->update($this->table, $data, $condition);
    }

    // Exclui um canal pelo ID
    public function deleteChannel($channel_id) {
        $condition = "channel_id = :channel_id";
        $params = ['channel_id' => $channel_id];
        return $this->db->delete($this->table, $condition, $params);
    }

    // Lista todos os canais
    public function listChannels() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }

    // Busca canais por idioma, agrupando-os por país
    public function getChannelsByLanguageGroupedByCountry($language) {
        $sql = "SELECT country, GROUP_CONCAT(name ORDER BY name ASC SEPARATOR ', ') AS channels
                FROM {$this->table}
                WHERE language = :language
                GROUP BY country
                ORDER BY country ASC";
        $params = ['language' => $language];
        return $this->db->fetchAll($sql, $params);
    }

    // Busca canais por país, agrupando-os por idioma
    public function getChannelsByCountryGroupedByLanguage($country) {
        $sql = "SELECT language, GROUP_CONCAT(name ORDER BY name ASC SEPARATOR ', ') AS channels
                FROM {$this->table}
                WHERE country = :country
                GROUP BY language
                ORDER BY language ASC";
        $params = ['country' => $country];
        return $this->db->fetchAll($sql, $params);
    }

    // Busca canais por idioma e país
    public function getChannelsByLanguageAndCountry($language, $country) {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE language = :language AND country = :country
                ORDER BY name ASC";
        $params = [
            'language' => $language,
            'country' => $country
        ];
        return $this->db->fetchAll($sql, $params);
    }

    // Busca um canal pelo nome
    public function getChannelByName($name) {
        $sql = "SELECT * FROM {$this->table} WHERE name = :name";
        $params = ['name' => $name];
        return $this->db->fetchOne($sql, $params);
    }

    // Busca canais recentemente adicionados (últimos 30 dias)
    public function getRecentChannels() {
        $sql = "SELECT * FROM {$this->table} WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        return $this->db->fetchAll($sql);
    }

    // Conta canais por idioma
    public function countChannelsByLanguage() {
        $sql = "SELECT language, COUNT(*) as count FROM {$this->table} GROUP BY language ORDER BY count DESC";
        return $this->db->fetchAll($sql);
    }

    // Conta canais por país
    public function countChannelsByCountry() {
        $sql = "SELECT country, COUNT(*) as count FROM {$this->table} GROUP BY country ORDER BY count DESC";
        return $this->db->fetchAll($sql);
    }
}

