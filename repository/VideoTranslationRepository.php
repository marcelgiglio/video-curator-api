<?php

require_once __DIR__ . '/../db/DatabaseDb.php';
require_once __DIR__ . '/../log/log.php';

class VideoTranslationRepository {
    private $db;
    private $table = 'video_translations';

    public function __construct() {
        $this->db = new Database();
    }

    // Adiciona uma nova tradução de vídeo ao banco de dados
    public function addVideoTranslation($video_id, $language, $translated_title, $translated_description) {
        $data = [
            'video_id' => $video_id,
            'language' => $language,
            'translated_title' => $translated_title,
            'translated_description' => $translated_description
        ];
        return $this->db->insert($this->table, $data);
    }

    // Busca uma tradução de vídeo pelo ID da tradução
    public function getVideoTranslationById($translation_id) {
        $sql = "SELECT * FROM {$this->table} WHERE translation_id = :translation_id";
        $params = ['translation_id' => $translation_id];
        return $this->db->fetchOne($sql, $params);
    }

    // Atualiza os dados de uma tradução de vídeo
    public function updateVideoTranslation($translation_id, $video_id, $language, $translated_title, $translated_description) {
        $data = [
            'video_id' => $video_id,
            'language' => $language,
            'translated_title' => $translated_title,
            'translated_description' => $translated_description
        ];
        $condition = "translation_id = :translation_id";
        $data['translation_id'] = $translation_id; // Adiciona o translation_id ao array de dados para usar no placeholder da condição
        return $this->db->update($this->table, $data, $condition);
    }

    // Exclui uma tradução de vídeo pelo ID da tradução
    public function deleteVideoTranslation($translation_id) {
        $condition = "translation_id = :translation_id";
        $params = ['translation_id' => $translation_id];
        return $this->db->delete($this->table, $condition, $params);
    }        

    // Lista todas as traduções de um vídeo específico
    public function listTranslationsForVideo($video_id) {
        $sql = "SELECT * FROM {$this->table} WHERE video_id = :video_id";
        $params = ['video_id' => $video_id];
        return $this->db->fetchAll($sql, $params);
    }

}