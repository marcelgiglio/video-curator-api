<?php

require_once __DIR__ . '/../db/database.php';

class VideoRepository {
    private $db;
    private $table = 'videos';

    public function __construct() {
        $this->db = new Database();
    }

    // Adiciona um novo vídeo ao banco de dados
    public function addVideo($channel_id, $title, $description, $original_language, $publication_date, $duration, $url, $thumbnail) {
        $data = [
            'channel_id' => $channel_id,
            'title' => $title,
            'description' => $description,
            'original_language' => $original_language,
            'publication_date' => $publication_date,
            'duration' => $duration,
            'url' => $url,
            'thumbnail' => $thumbnail
        ];
        return $this->db->insert($this->table, $data);
    }
    
    // Busca o último vídeo por canal
    public function getLastVideoByChannelId($channel_id) {
        $sql = "SELECT * FROM {$this->table} WHERE channel_id = :channel_id ORDER BY publication_date DESC LIMIT 1";
        $params = ['channel_id' => $channel_id];
        return $this->db->fetchOne($sql, $params);
    }

    // Verifica se um vídeo existe no BD
    public function videoExists($video_id) {
        $sql = "SELECT video_id FROM {$this->table} WHERE video_id = :video_id";
        $params = ['video_id' => $video_id];
        return count($this->db->fetchAll($sql, $params)) > 0;
    }
    
    // Busca um vídeo pelo ID
    public function getVideoById($video_id) {
        $sql = "SELECT * FROM {$this->table} WHERE video_id = :video_id";
        $params = ['video_id' => $video_id];
        return $this->db->fetchOne($sql, $params);
    }

    // Atualiza os dados de um vídeo
    public function updateVideo($video_id, $channel_id, $title, $description, $original_language, $publication_date, $duration, $url, $thumbnail) {
        $data = [
            'channel_id' => $channel_id,
            'title' => $title,
            'description' => $description,
            'original_language' => $original_language,
            'publication_date' => $publication_date,
            'duration' => $duration,
            'url' => $url,
            'thumbnail' => $thumbnail
        ];
        $condition = "video_id = :video_id";
        $data['video_id'] = $video_id; // Adiciona o video_id ao array de dados para usar no placeholder da condição
        return $this->db->update($this->table, $data, $condition);
    }

    // Exclui um vídeo pelo ID
    public function deleteVideo($video_id) {
        $condition = "video_id = :video_id";
        $params = ['video_id' => $video_id];
        return $this->db->delete($this->table, $condition, $params);
    }

    // Lista todos os vídeos
    public function listVideos() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }

    // Lista todos os vídeos de um canal
    public function listVideosByChannelId($channel_id) {
        $sql = "SELECT * FROM {$this->table} WHERE channel_id = :channel_id";
        $params = ['channel_id' => $channel_id];
        return $this->db->fetchAll($sql, $params);
    }
    
    // Função para retornar vídeos recentes
    public function getRecentVideos() {
        $sql = "SELECT * FROM {$this->table} WHERE publication_date >= DATE_SUB(NOW(), INTERVAL 14 DAY) ORDER BY publication_date DESC";
        return $this->db->fetchAll($sql);
    }

    // Função para retornar vídeos recentes por país
    public function getRecentVideosByCountry($country) {
        $sql = "SELECT v.* FROM {$this->table} v
                JOIN channels c ON v.channel_id = c.channel_id
                WHERE c.country = :country AND v.publication_date >= DATE_SUB(NOW(), INTERVAL 14 DAY)
                ORDER BY v.publication_date DESC";
        $params = ['country' => $country];
        return $this->db->fetchAll($sql, $params);
    }
}
