<?php

require_once __DIR__ . '/../config.php';

class YouTubeDataAPI {
    private $apiKey;

    public function __construct() {
        $this->apiKey = GOOGLE_YOUTUBE_API_KEY;
    }

    // Método para buscar vídeos por canal
    public function getVideosByChannel($channelId, $publishedAfter = null) {
        $url = "https://www.googleapis.com/youtube/v3/search?key={$this->apiKey}&channelId={$channelId}&part=snippet,id&order=date&maxResults=60";
        if ($publishedAfter) {
            $url .= "&publishedAfter={$publishedAfter}";
        }
        return $this->makeRequest($url);
    }

    // Método para obter detalhes de um vídeo específico
    public function getVideoDetails($videoId) {
        $url = "https://www.googleapis.com/youtube/v3/videos?id={$videoId}&part=snippet,contentDetails&key={$this->apiKey}";
        return $this->makeRequest($url);
    }

    // Método para fazer a requisição à API
    private function makeRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
