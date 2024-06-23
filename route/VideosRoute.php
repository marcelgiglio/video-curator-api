<?php

require_once __DIR__ . '/RouteRoute.php';

header('Content-Type: application/json');

// Rotas específicas primeiro
Route::add('/api/videos/recent/country/([a-zA-Z0-9_-]+)', function($country) {
    require_once __DIR__ . '/../service/GetRecentVideosByCountryService.php';
    $result = getRecentVideosByCountry($country);
    echo json_encode($result);
}, 'get');

Route::add('/api/videos/recent', function() {
    require_once __DIR__ . '/../service/GetRecentVideosService.php';
    $result = getRecentVideos();
    echo json_encode($result);
}, 'get');

Route::add('/api/videos/search', function() {
    require_once __DIR__ . '/../service/SearchVideosService.php';
    $params = $_GET;
    $result = searchVideos($params);
    echo json_encode($result);
}, 'get');

Route::add('/api/videos/([a-zA-Z0-9_-]+)/translations', function($videoId) {
    require_once __DIR__ . '/../service/GetVideoTranslationsService.php';
    $result = getVideoTranslations($videoId);
    echo json_encode($result);
}, 'get');

Route::add('/api/videos/channel/([a-zA-Z0-9_-]+)', function($channelId) {
    require_once __DIR__ . '/../service/GetVideosByChannelService.php';
    $result = getVideosByChannel($channelId);
    echo json_encode($result);
}, 'get');

Route::add('/api/videos/([a-zA-Z0-9_-]+)', function($id) {
    require_once __DIR__ . '/../service/GetVideoService.php';
    $result = getVideoById($id);
    echo json_encode($result);
}, 'get');

Route::add('/api/videos', function() {
    require_once __DIR__ . '/../service/ListVideosService.php';
    $result = listVideos();
    echo json_encode($result);
}, 'get');

Route::run();

?>
