<?php

require_once __DIR__ . '/../repository/ChannelRepository.php';

function countChannelsByLanguage() {
    $channelRepository = new ChannelRepository();

    try {
        $counts = $channelRepository->countChannelsByLanguage();
        
        if (!empty($counts)) {
            return ['status' => 'success', 'message' => 'Channel counts by language retrieved successfully', 'data' => $counts];
        } else {
            return ['status' => 'error', 'message' => 'No channel counts found'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving channel counts by language', 'error' => $e->getMessage()];
    }
}

function countChannelsByCountry() {
    $channelRepository = new ChannelRepository();

    try {
        $counts = $channelRepository->countChannelsByCountry();
        
        if (!empty($counts)) {
            return ['status' => 'success', 'message' => 'Channel counts by country retrieved successfully', 'data' => $counts];
        } else {
            return ['status' => 'error', 'message' => 'No channel counts found'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving channel counts by country', 'error' => $e->getMessage()];
    }
}

?>
