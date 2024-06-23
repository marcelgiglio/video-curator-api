<?php

require_once __DIR__ . '/../repository/VideoTranslationRepository.php';

function getVideoTranslations($videoId) {
    $videoTranslationRepository = new VideoTranslationRepository();

    try {
        $translations = $videoTranslationRepository->listTranslationsForVideo($videoId);
        
        if (!empty($translations)) {
            return ['status' => 'success', 'message' => 'Video translations retrieved successfully', 'data' => $translations];
        } else {
            return ['status' => 'error', 'message' => 'No translations found for this video'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving video translations', 'error' => $e->getMessage()];
    }
}

?>
