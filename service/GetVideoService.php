<?php

require_once __DIR__ . '/../repository/VideoRepository.php';

function getVideoById($id) {
    $videoRepository = new VideoRepository();

    try {
        $video = $videoRepository->getVideoById($id);
        
        if (!empty($video)) {
            return ['status' => 'success', 'message' => 'Video retrieved successfully', 'data' => $video];
        } else {
            return ['status' => 'error', 'message' => 'No video found'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving the video', 'error' => $e->getMessage()];
    }
}

?>
