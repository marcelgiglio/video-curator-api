<?php

require_once __DIR__ . '/../repository/ChannelRepository.php';
require_once __DIR__ . '/../repository/VideoRepository.php';
require_once __DIR__ . '/../repository/VideoTranslationRepository.php';
require_once __DIR__ . '/../translate/TranslateService.php';
require_once __DIR__ . '/YouTubeDataAPI.php';
require_once __DIR__ . '/../log/log.php';

class DailyVideoUpdater {
    private $youtubeApi;
    private $videoRepository;
    private $channelRepository;
    private $translateService;
    private $videoTranslationRepository;

    public function __construct() {
        $this->youtubeApi = new YouTubeDataAPI();
        $this->videoRepository = new VideoRepository();
        $this->channelRepository = new ChannelRepository();
        $this->translateService = new TranslateService();
        $this->videoTranslationRepository = new VideoTranslationRepository();
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
                        $this->processVideo($channel['channel_id'], $videoId);
                    }
                } else {
                    logError("Video ID missing for a video in channel: " . $channel['channel_id']);
                }
            }
        }
    }

    private function processVideo($channelId, $videoId) {
        $videoDetails = $this->youtubeApi->getVideoDetails($videoId);

        $title = $videoDetails['items'][0]['snippet']['title'] ?? 'No title';
        $description = $videoDetails['items'][0]['snippet']['description'] ?? 'No description';
        $originalLanguage = $videoDetails['items'][0]['snippet']['defaultAudioLanguage'] ?? 'unknown';
        $publishedAt = $videoDetails['items'][0]['snippet']['publishedAt'] ?? date('Y-m-d');
        $duration = $videoDetails['items'][0]['contentDetails']['duration'] ?? 'PT0M';

        $thumbnailUrl = $videoDetails['items'][0]['snippet']['thumbnails']['default']['url'] ?? null;
        $thumbnailPath = $this->saveThumbnail($thumbnailUrl, $videoId);

        $this->videoRepository->addVideo(
            $videoId,
            $channelId,
            $title,
            $description,
            $originalLanguage,
            $publishedAt,
            $duration,
            'https://www.youtube.com/watch?v=' . $videoId,
            $thumbnailPath
        );

        // Adiciona traduções
        $this->addTranslations($videoId, $title, $description, $originalLanguage);
    }

    private function saveThumbnail($url, $videoId) {
        $thumbnailDir = __DIR__ . '/../thumbnails/';
        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
        }

        $thumbnailPath = $thumbnailDir . $videoId . '.jpg';

        if ($url) {
            $thumbnailData = file_get_contents($url);
            if ($thumbnailData !== false) {
                file_put_contents($thumbnailPath, $thumbnailData);
            } else {
                logError("Failed to download thumbnail for video ID: $videoId, URL: $url");
                $thumbnailPath = $this->getDefaultThumbnailPath();
            }
        } else {
            $thumbnailPath = $this->getDefaultThumbnailPath();
        }

        return 'thumbnails/' . basename($thumbnailPath);
    }

    private function getDefaultThumbnailPath() {
        return __DIR__ . '/../thumbnails/default.jpg';
    }

    private function addTranslations($videoId, $title, $description, $originalLanguage) {
        $translationsTitle = $this->translateService->translateAll($title, $originalLanguage);
        $translationsDescription = $this->translateService->translateAll($description, $originalLanguage);

        foreach ($translationsTitle as $lang => $translatedTitle) {
            $translatedDescription = $translationsDescription[$lang];
            $this->videoTranslationRepository->addTranslation(
                $videoId,
                $lang,
                $translatedTitle,
                $translatedDescription
            );
        }
    }
}

// Exemplo de uso
$updater = new DailyVideoUpdater();
$updater->updateVideos();
?>
