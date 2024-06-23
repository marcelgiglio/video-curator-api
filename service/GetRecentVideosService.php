<?php

require_once __DIR__ . '/../repository/VideoRepository.php';

function getRecentVideos() {
    $videoRepository = new VideoRepository();

    try {
        $videos = $videoRepository->getRecentVideos();
        
        if (!empty($videos)) {
            return ['status' => 'success', 'message' => 'Recent videos retrieved successfully', 'data' => $videos];
        } else {
            return ['status' => 'error', 'message' => 'No recent videos found'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving recent videos', 'error' => $e->getMessage()];
    }
}

?>
