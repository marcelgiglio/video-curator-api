<?php

set_time_limit(1000); // Aumenta o limite de tempo para 1000 segundos

require_once __DIR__ . '/../gateway/YouTubeGateway.php';
require_once __DIR__ . '/../repository/ChannelRepository.php';
require_once __DIR__ . '/../repository/VideoRepository.php';
require_once __DIR__ . '/VideoProcessorService.php';
require_once __DIR__ . '/../log/log.php';

// Inicializar dependências
$youtubeApi = new YouTubeDataAPI();
$channelRepository = new ChannelRepository();
$videoRepository = new VideoRepository();
$videoProcessor = new VideoProcessor($youtubeApi);

function backfillVideos($youtubeApi, $channelRepository, $videoRepository, $videoProcessor) {
    $channels = $channelRepository->listChannels();

    foreach ($channels as $channel) {
        $nextPageToken = null;

        do {
            $videos = $youtubeApi->getVideosByChannel($channel['channel_id'], null, $nextPageToken);

            if (isset($videos['items'])) {
                foreach ($videos['items'] as $video) {
                    if (isset($video['id']['videoId'])) {
                        $videoId = $video['id']['videoId'];
                        if (!$videoRepository->videoExists($videoId)) {
                            $videoProcessor->processVideo($channel['channel_id'], $videoId);
                        }
                    } else {
                        logError("Video ID missing for a video in channel: " . $channel['channel_id']);
                    }
                }
            }

            $nextPageToken = $videos['nextPageToken'] ?? null;
        } while ($nextPageToken);
    }
}

// Chamar a função de atualização de vídeos
backfillVideos($youtubeApi, $channelRepository, $videoRepository, $videoProcessor);

?>
