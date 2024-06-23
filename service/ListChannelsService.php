<?php

require_once __DIR__ . '/../repository/ChannelRepository.php';

function listChannels() {
    $channelRepository = new ChannelRepository();

    try {
        $channels = $channelRepository->listChannels();
        
        if (!empty($channels)) {
            return ['status' => 'success', 'message' => 'Channels retrieved successfully', 'data' => $channels];
        } else {
            return ['status' => 'error', 'message' => 'No channels found'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving channels', 'error' => $e->getMessage()];
    }
}

function getChannelsByLanguageAndCountry($language, $country) {
    $channelRepository = new ChannelRepository();

    try {
        $channels = $channelRepository->getChannelsByLanguageAndCountry($language, $country);
        
        if (!empty($channels)) {
            return ['status' => 'success', 'message' => 'Channels retrieved successfully', 'data' => $channels];
        } else {
            return ['status' => 'error', 'message' => 'No channels found'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving channels', 'error' => $e->getMessage()];
    }
}

function getChannelsByLanguageGroupedByCountry($language) {
    $channelRepository = new ChannelRepository();

    try {
        $channels = $channelRepository->getChannelsByLanguageGroupedByCountry($language);
        
        if (!empty($channels)) {
            return ['status' => 'success', 'message' => 'Channels retrieved successfully', 'data' => $channels];
        } else {
            return ['status' => 'error', 'message' => 'No channels found'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving channels', 'error' => $e->getMessage()];
    }
}

function getChannelsByCountryGroupedByLanguage($country) {
    $channelRepository = new ChannelRepository();

    try {
        $channels = $channelRepository->getChannelsByCountryGroupedByLanguage($country);
        
        if (!empty($channels)) {
            return ['status' => 'success', 'message' => 'Channels retrieved successfully', 'data' => $channels];
        } else {
            return ['status' => 'error', 'message' => 'No channels found'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'An error occurred while retrieving channels', 'error' => $e->getMessage()];
    }
}

?>
