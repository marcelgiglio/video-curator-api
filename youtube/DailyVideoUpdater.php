<?php

set_time_limit(1000); // Aumenta o limite de tempo para 1000 segundos

require_once __DIR__ . '/../repository/ChannelRepository.php';
require_once __DIR__ . '/YouTubeDataAPI.php';
require_once __DIR__ . '/../log/log.php';
require_once __DIR__ . '/VideoProcessor.php';

class DailyVideoUpdater {
    private $youtubeApi;
    private $videoProcessor;
    private $channelRepository;

    public function __construct() {
        $this->youtubeApi = new YouTubeDataAPI();
        $this->channelRepository = new ChannelRepository();
        $this->videoProcessor = new VideoProcessor($this->youtubeApi);
    }

    public function updateVideos() {
        $channels = $this->channelRepository->listChannels();

        foreach ($channels as $channel) {
            $lastVideo = $this->videoRepository->getLastVideoByChannelId($channel['channel_id']);
            $publishedAfter = $lastVideo ? $lastVideo['publication_date'] : null;

            $videos = $this->youtubeApi->getVideosByChannel($channel['channel_id'], $publishedAfter);

            foreach ($videos['items'] as $video) {
                if (isset($video['id']['videoId'])) {
                    $videoId = $video['id']['videoId'];
                    if (!$this->videoRepository->videoExists($videoId)) {
                        $this->videoProcessor->processVideo($channel['channel_id'], $videoId);
                    }
                } else {
                    logError("Video ID missing for a video in channel: " . $channel['channel_id']);
                }
            }
        }
    }
}

// Exemplo de uso
$updater = new DailyVideoUpdater();
$updater->updateVideos();

