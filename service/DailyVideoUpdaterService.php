<?php

set_time_limit(1000); // Aumenta o limite de tempo para 1000 segundos

require_once __DIR__ . '/../gateway/YouTubeGateway.php';
require_once __DIR__ . '/../repository/ChannelRepository.php';
require_once __DIR__ . '/../repository/VideoRepository.php';
require_once __DIR__ . '/../service/VideoProcessorService.php';
require_once __DIR__ . '/../log/log.php';

// Inicializar dependências
$youtubeApi = new YouTubeDataAPI();
$channelRepository = new ChannelRepository();
$videoRepository = new VideoRepository();

function updateVideos($youtubeApi, $channelRepository, $videoRepository) {
    $channels = $channelRepository->listChannels();

    foreach ($channels as $channel) {
        $lastVideo = $videoRepository->getLastVideoByChannelId($channel['channel_id']);
        $publishedAfter = $lastVideo ? $lastVideo['publication_date'] : null;

        $videos = $youtubeApi->getVideosByChannel($channel['channel_id'], $publishedAfter);

        foreach ($videos['items'] as $video) {
            if (isset($video['id']['videoId'])) {
                $videoId = $video['id']['videoId'];
                if (!$videoRepository->videoExists($videoId)) {
                    processVideo($youtubeApi, $channel['channel_id'], $videoId);
                }
            } else {
                logError("Video ID missing for a video in channel: " . $channel['channel_id']);
            }
        }
    }
}

// Chamar a função de atualização de vídeos
updateVideos($youtubeApi, $channelRepository, $videoRepository);

?>
