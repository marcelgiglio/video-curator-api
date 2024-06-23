<?php

require_once __DIR__ . '/RouteRoute.php';

header('Content-Type: application/json');

Route::add('/api/channels/add', function() {
    require_once __DIR__ . '/../service/AddChannelService.php';
    $identifier = $_GET['id'] ?? null;
    
    if ($identifier) {
        $result = addNewChannel($identifier);
        echo json_encode($result);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing channel ID or username']);
    }
}, 'get');

Route::add('/api/channels/delete', function() {
    require_once __DIR__ . '/../service/DeleteChannelService.php';
    $identifier = $_GET['id'] ?? null;
    
    if ($identifier) {
        $result = deleteChannel($identifier);
        echo json_encode($result);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing channel ID or username']);
    }
}, 'get');

Route::add('/api/channels/recent', function() {
    require_once __DIR__ . '/../service/RecentChannelsService.php';
    $result = getRecentChannels();
    echo json_encode($result);
}, 'get');

Route::add('/api/channels/counts/language', function() {
    require_once __DIR__ . '/../service/ChannelCountsService.php';
    $result = countChannelsByLanguage();
    echo json_encode($result);
}, 'get');

Route::add('/api/channels/counts/country', function() {
    require_once __DIR__ . '/../service/ChannelCountsService.php';
    $result = countChannelsByCountry();
    echo json_encode($result);
}, 'get');

Route::add('/api/channels/search', function() {
    require_once __DIR__ . '/../service/SearchChannelService.php';
    $params = $_GET;
    if (isset($params['name'])) {
        $result = getChannelByName($params['name']);
    } else {
        echo json_encode(['error' => 'Name parameter is required.']);
        return;
    }
    echo json_encode($result);
}, 'get');

Route::add('/api/channels/([a-zA-Z0-9_-]+)', function($id) {
    require_once __DIR__ . '/../service/GetChannelService.php';
    $result = getChannelById($id);
    echo json_encode($result);
}, 'get');

Route::add('/api/channels', function() {
    require_once __DIR__ . '/../service/ListChannelsService.php';
    $params = $_GET;
    if (isset($params['language']) && isset($params['country'])) {
        $result = getChannelsByLanguageAndCountry($params['language'], $params['country']);
    } elseif (isset($params['language'])) {
        $result = getChannelsByLanguageGroupedByCountry($params['language']);
    } elseif (isset($params['country'])) {
        $result = getChannelsByCountryGroupedByLanguage($params['country']);
    } else {
        $result = listChannels();
    }
    echo json_encode($result);
}, 'get');

Route::run();

?>
