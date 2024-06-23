<?php

require_once __DIR__ . '/../repository/ChannelRepository.php';

function getRecentChannels() {
    $channelRepository = new ChannelRepository();
    
    try {
        $recentChannels = $channelRepository->getRecentChannels();
        
        if (!empty($recentChannels)) {
            return ['status' => 'success', 'message' => 'Recent channels retrieved successfully', 'data' => $recentChannels];
        } else {
            return ['status' => 'error', 'message' => 'No recent channels found'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving recent channels', 'error' => $e->getMessage()];
    }
}

?>
