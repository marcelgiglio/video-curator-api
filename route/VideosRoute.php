<?php

require_once __DIR__ . '/../repository/VideoRepository.php';
require_once __DIR__ . '/../repository/VideoSearchRepository.php';
require_once __DIR__ . '/../repository/VideoTranslationRepository.php';
require_once __DIR__ . '/RouteRoute.php';

header('Content-Type: application/json');

$videoRepository = new VideoRepository();
$videoSearch = new VideoSearch();
$videoTranslationRepository = new VideoTranslationRepository();

Route::add('/api/videos', function() use ($videoRepository) {
    $videos = $videoRepository->listVideos();
    echo json_encode($videos);
}, 'get');

Route::add('/api/videos/([a-zA-Z0-9_-]+)', function($id) use ($videoRepository) {
    $video = $videoRepository->getVideoById($id);
    echo json_encode($video);
}, 'get');

Route::add('/api/videos/channel/([a-zA-Z0-9_-]+)', function($channelId) use ($videoRepository) {
    $videos = $videoRepository->getVideosByChannel($channelId);
    echo json_encode($videos);
}, 'get');

Route::add('/api/videos/recent', function() use ($videoRepository) {
    $videos = $videoRepository->getRecentVideos();
    echo json_encode($videos);
}, 'get');

Route::add('/api/videos/recent/country/([a-zA-Z0-9_-]+)', function($country) use ($videoRepository) {
    $videos = $videoRepository->getRecentVideosByCountry($country);
    echo json_encode($videos);
}, 'get');

Route::add('/api/videos/search', function() use ($videoSearch) {
    $params = $_GET;
    $videoSearch->applyFilters($params);
    $videos = $videoSearch->execute();
    echo json_encode($videos);
}, 'get');

Route::add('/api/videos/([a-zA-Z0-9_-]+)/translations', function($videoId) use ($videoTranslationRepository) {
    $translations = $videoTranslationRepository->listTranslationsForVideo($videoId);
    echo json_encode($translations);
}, 'get');

Route::run();

?>
