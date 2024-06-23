<?php

require_once __DIR__ . '/../repository/VideoSearchRepository.php';

function searchVideos($params) {
    $videoSearchRepository = new VideoSearchRepository();

    try {
        $videoSearchRepository->applyFilters($params);
        $videos = $videoSearchRepository->execute();
        
        if (!empty($videos)) {
            return ['status' => 'success', 'message' => 'Videos retrieved successfully', 'data' => $videos];
        } else {
            return ['status' => 'error', 'message' => 'No videos found'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while searching for videos', 'error' => $e->getMessage()];
    }
}

?>
