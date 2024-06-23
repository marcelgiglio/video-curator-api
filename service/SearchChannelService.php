<?php

require_once __DIR__ . '/../repository/ChannelRepository.php';

function getChannelByName($name) {
    $channelRepository = new ChannelRepository();

    try {
        $channel = $channelRepository->searchChannelByName($name);
        
        if (!empty($channel)) {
            return ['status' => 'success', 'message' => 'Channel retrieved successfully', 'data' => $channel];
        } else {
            return ['status' => 'error', 'message' => 'Channel not found'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving the channel', 'error' => $e->getMessage()];
    }
}

?>
