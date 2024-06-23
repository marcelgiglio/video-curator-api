<?php

require_once __DIR__ . '/../gateway/YouTubeGateway.php';
require_once __DIR__ . '/../repository/VideoRepository.php';
require_once __DIR__ . '/../repository/VideoTranslationRepository.php';
require_once __DIR__ . '/../gateway/TranslateGateway.php';
require_once __DIR__ . '/../log/log.php';

function processVideo($youtubeApi, $channelId, $videoId) {
    $videoRepository = new VideoRepository();
    $translateService = new TranslateService();
    $videoTranslationRepository = new VideoTranslationRepository();

    if ($videoRepository->videoExists($videoId)) {
        logError("This video already exists: $videoId");
        return;
    }

    $videoDetails = $youtubeApi->getVideoDetails($videoId);

    $title = $videoDetails['items'][0]['snippet']['title'] ?? 'No title';
    $description = $videoDetails['items'][0]['snippet']['description'] ?? 'No description';
    $originalLanguage = $videoDetails['items'][0]['snippet']['defaultAudioLanguage'] ?? 'unknown';
    $publishedAt = $videoDetails['items'][0]['snippet']['publishedAt'] ?? date('Y-m-d');
    $duration = $videoDetails['items'][0]['contentDetails']['duration'] ?? 'PT0M';

    $thumbnailUrl = $videoDetails['items'][0]['snippet']['thumbnails']['default']['url'] ?? null;
    $thumbnailPath = saveThumbnail($thumbnailUrl, $videoId);

    $videoRepository->addVideo(
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
    addTranslations($videoTranslationRepository, $translateService, $videoId, $title, $description, $originalLanguage);
}

function saveThumbnail($url, $videoId) {
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
            $thumbnailPath = getDefaultThumbnailPath();
        }
    } else {
        $thumbnailPath = getDefaultThumbnailPath();
    }

    return 'thumbnails/' . basename($thumbnailPath);
}

function getDefaultThumbnailPath() {
    return __DIR__ . '/../thumbnails/default.jpg';
}

function addTranslations($videoTranslationRepository, $translateService, $videoId, $title, $description, $originalLanguage) {
    $translationsTitle = $translateService->translateAll($title, $originalLanguage);
    $translationsDescription = $translateService->translateAll($description, $originalLanguage);

    foreach ($translationsTitle as $lang => $translatedTitle) {
        $translatedDescription = $translationsDescription[$lang] ?? 'No description available';

        $videoTranslationRepository->addVideoTranslation(
            $videoId,
            $lang,
            $translatedTitle,
            $translatedDescription
        );
    }
}

?>
