<?php

require_once __DIR__ . '/../repository/VideoRepository.php';

function listVideos() {
    $videoRepository = new VideoRepository();

    try {
        $videos = $videoRepository->listVideos();
        
        if (!empty($videos)) {
            return ['status' => 'success', 'message' => 'Videos retrieved successfully', 'data' => $videos];
        } else {
            return ['status' => 'error', 'message' => 'No videos found'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving videos', 'error' => $e->getMessage()];
    }
}

?>
