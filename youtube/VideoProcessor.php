<?php

require_once __DIR__ . '/YouTubeDataAPI.php';
require_once __DIR__ . '/../repository/VideoRepository.php';
require_once __DIR__ . '/../repository/VideoTranslationRepository.php';
require_once __DIR__ . '/../translate/TranslateService.php';
require_once __DIR__ . '/../log/log.php';

class VideoProcessor {
    private $youtubeApi;
    private $videoRepository;
    private $translateService;
    private $videoTranslationRepository;

    public function __construct($youtubeApi) {
        $this->youtubeApi = $youtubeApi;
        $this->videoRepository = new VideoRepository();
        $this->translateService = new TranslateService();
        $this->videoTranslationRepository = new VideoTranslationRepository();
    }

    public function processVideo($channelId, $videoId) {
        if ($this->videoRepository->videoExists($videoId)) {
            logError("This video already exists: $videoId");
        }
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
            $translatedDescription = $translationsDescription[$lang] ?? 'No description available';
            echo "Adicionando tradução para o vídeo ID: $videoId, Idioma: $lang, Título: $translatedTitle, Descrição: $translatedDescription <br><br>";

            $this->videoTranslationRepository->addVideoTranslation(
                $videoId,
                $lang,
                $translatedTitle,
                $translatedDescription
            );
        }
    }
}

?>
