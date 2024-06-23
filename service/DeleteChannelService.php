<?php

require_once __DIR__ . '/../repository/ChannelRepository.php';
require_once __DIR__ . '/../repository/VideoRepository.php';
require_once __DIR__ . '/../repository/VideoTranslationRepository.php';
require_once __DIR__ . '/../gateway/YouTubeGateway.php';
require_once __DIR__ . '/../log/log.php';

function deleteChannel($identifier) {
    $youtubeApi = new YouTubeDataAPI();
    $channelRepository = new ChannelRepository();
    $videoRepository = new VideoRepository();
    $videoTranslationRepository = new VideoTranslationRepository();
    
    // Verifica se o identificador começa com '@', indicando um nome amigável
    if (strpos($identifier, '@') === 0) {
        $channelDetails = $youtubeApi->getChannelDetailsByUsername($identifier);
        if (!$channelDetails) {
            return ['status' => 'error', 'message' => 'Channel not found'];
        }
        $channelId = $channelDetails['items'][0]['id'];
    } else {
        $channelId = $identifier;
    }
    
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
        return ['status' => 'success', 'message' => 'Channel and related data deleted successfully'];
    } else {
        return ['status' => 'error', 'message' => 'Channel not found'];
    }
}

?>
