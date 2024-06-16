<?php

require_once __DIR__ . '/../repository/ChannelRepository.php';
require_once __DIR__ . '/../youtube/AddChannel.php';
require_once __DIR__ . '/../log/log.php';
require_once __DIR__ . '/route.php';

header('Content-Type: application/json');

$channelRepository = new ChannelRepository();

Route::add('/api/channels/add', function() {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $addChannel = new AddChannel();
        $result = $addChannel->addNewChannel($id);
        echo json_encode($result);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing channel ID']);
    }
}, 'get');

Route::add('/api/channels/recent', function() use ($channelRepository) {
    $result = $channelRepository->getRecentChannels();
    echo json_encode($result);
}, 'get');

Route::add('/api/channels/counts/language', function() use ($channelRepository) {
    $result = $channelRepository->countChannelsByLanguage();
    echo json_encode($result);
}, 'get');

Route::add('/api/channels/counts/country', function() use ($channelRepository) {
    $result = $channelRepository->countChannelsByCountry();
    echo json_encode($result);
}, 'get');

Route::add('/api/channels/search', function() use ($channelRepository) {
    $params = $_GET;
    if (isset($params['name'])) {
        $result = $channelRepository->getChannelByName($params['name']);
    } else {
        echo json_encode(['error' => 'Name parameter is required.']);
        return;
    }
    echo json_encode($result);
}, 'get');

Route::add('/api/channels', function() use ($channelRepository) {
    $params = $_GET;
    if (isset($params['language']) && isset($params['country'])) {
        $result = $channelRepository->getChannelsByLanguageAndCountry($params['language'], $params['country']);
    } elseif (isset($params['language'])) {
        $result = $channelRepository->getChannelsByLanguageGroupedByCountry($params['language']);
    } elseif (isset($params['country'])) {
        $result = $channelRepository->getChannelsByCountryGroupedByLanguage($params['country']);
    } else {
        $result = $channelRepository->listChannels();
    }
    echo json_encode($result);
}, 'get');

Route::add('/api/channels/([a-zA-Z0-9_-]+)', function($id) use ($channelRepository) {
    $result = $channelRepository->getChannelById($id);
    echo json_encode($result);
}, 'get');

Route::run('/');

?>
