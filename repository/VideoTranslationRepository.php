<?php

require_once __DIR__ . '/../repository/ChannelRepository.php';
require_once __DIR__ . '/../repository/VideoRepository.php';
require_once __DIR__ . '/../repository/VideoTranslationRepository.php';
require_once __DIR__ . '/../translate/TranslateService.php';
require_once __DIR__ . '/YouTubeDataAPI.php';

class DailyVideoUpdater {
    private $youtubeApi;
    private $videoRepository;
    private $channelRepository;
    private $translationRepository;
    private $translateService;

    public function __construct() {
        $this->youtubeApi = new YouTubeDataAPI();
        $this->videoRepository = new VideoRepository();
        $this->channelRepository = new ChannelRepository();
        $this->translationRepository = new VideoTranslationRepository();
        $this->translateService = new TranslateService();
    }

    public function updateVideos() {
        $channels = $this->channelRepository->listChannels();

        foreach ($channels as $channel) {
            $lastVideo = $this->videoRepository->getLastVideoByChannelId($channel['channel_id']);
            $publishedAfter = $lastVideo ? $lastVideo['publication_date'] : null;

            $videos = $this->youtubeApi->getVideosByChannel($channel['channel_id'], $publishedAfter);

            foreach ($videos['items'] as $video) {
                $videoId = $video['id']['videoId'];
                if (!$this->videoRepository->videoExists($videoId)) {
                    $videoDetails = $this->youtubeApi->getVideoDetails($videoId);

                    // Download e salvamento da thumbnail
                    $thumbnailUrl = $videoDetails['items'][0]['snippet']['thumbnails']['default']['url'];
                    $thumbnailPath = $this->saveThumbnail($thumbnailUrl, $videoId);

                    $originalLanguage = $videoDetails['items'][0]['snippet']['defaultAudioLanguage'] ?? 'en';

                    $this->videoRepository->addVideo(
                        $channel['channel_id'],
                        $videoDetails['items'][0]['snippet']['title'],
                        $videoDetails['items'][0]['snippet']['description'],
                        $originalLanguage,
                        $videoDetails['items'][0]['snippet']['publishedAt'],
                        $videoDetails['items'][0]['contentDetails']['duration'],
                        'https://www.youtube.com/watch?v=' . $videoId,
                        $thumbnailPath
                    );

                    // Adiciona traduções
                    $this->translateAndSave(
                        $videoId,
                        $videoDetails['items'][0]['snippet']['title'],
                        $videoDetails['items'][0]['snippet']['description'],
                        $originalLanguage
                    );
                }
            }
        }
    }

    private function saveThumbnail($url, $videoId) {
        $thumbnailDir = __DIR__ . '/../thumbnails/';
        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
        }

        $thumbnailPath = $thumbnailDir . $videoId . '.jpg';
        file_put_contents($thumbnailPath, file_get_contents($url));

        return 'thumbnails/' . $videoId . '.jpg';
    }

    private function translateAndSave($videoId, $title, $description, $sourceLang) {
        $translationsTitle = $this->translateService->translateAll($title, $sourceLang);
        $translationsDescription = $this->translateService->translateAll($description, $sourceLang);
        
        foreach ($translationsTitle as $lang => $translatedTitle) {
            $translatedDescription = $translationsDescription[$lang];
            $this->translationRepository->addVideoTranslation(
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
