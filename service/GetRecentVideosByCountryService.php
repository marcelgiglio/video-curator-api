<?php

require_once __DIR__ . '/../repository/VideoRepository.php';

function getRecentVideosByCountry($country) {
    $videoRepository = new VideoRepository();

    try {
        $videos = $videoRepository->getRecentVideosByCountry($country);
        
        if (!empty($videos)) {
            return ['status' => 'success', 'message' => 'Recent videos by country retrieved successfully', 'data' => $videos];
        } else {
            return ['status' => 'error', 'message' => 'No recent videos found for this country'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving recent videos by country', 'error' => $e->getMessage()];
    }
}

?>
