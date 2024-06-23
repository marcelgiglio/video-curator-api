<?php

require_once __DIR__ . '/../repository/ChannelRepository.php';
require_once __DIR__ . '/../repository/VideoRepository.php';
require_once __DIR__ . '/../repository/VideoTranslationRepository.php';
require_once __DIR__ . '/../service/AddChannelService.php';
require_once __DIR__ . '/RouteRoute.php';
require_once __DIR__ . '/../log/log.php';

header('Content-Type: application/json');

$channelRepository = new ChannelRepository();
$videoRepository = new VideoRepository();
$videoTranslationRepository = new VideoTranslationRepository();


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

Route::add('/api/channels/delete', function() use ($channelRepository, $videoRepository, $videoTranslationRepository) {
    $channelId = $_GET['id'] ?? null;
    
    if ($channelId) {
        $channel = $channelRepository->getChannelById($channelId);
        
        if ($channel) {
            // Deletar traduções de vídeos
            $videos = $videoRepository->listVideosByChannelId($channelId);
            foreach ($videos as $video) {
                $translations = $videoTranslationRepository->listTranslationsForVideo($video['video_id']);
                foreach ($translations as $translation) {
                    $videoTranslationRepository->deleteVideoTranslation($translation['translation_id']);
                }
                // Deletar vídeos
                $videoRepository->deleteVideo($video['video_id']);
            }
            // Deletar o canal
            $channelRepository->deleteChannel($channelId);
            echo json_encode(['status' => 'success', 'message' => 'Channel and related data deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Channel not found']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing channel ID']);
    }
}, 'get');

Route::add('/api/channels/recent', function() use ($channelRepository) {
    //ainda não funciona
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
    //ainda não funciona, nem tentei...
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
    //ainda não funciona, dá erro 404
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

Route::run();

?>
