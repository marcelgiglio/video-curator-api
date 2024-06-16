<?php

require_once __DIR__ . '/../config.php';

class YouTubeDataAPI {
    private $apiKey;
    private $apiUrl;

    public function __construct() {
        $this->apiKey = GOOGLE_YOUTUBE_API_KEY;
        $this->apiUrl = "https://www.googleapis.com/youtube/v3";
    }

    // Método para buscar vídeos por canal
    public function getVideosByChannel($channelId, $publishedAfter = null) {
        $url = $this->apiUrl . "/search?key={$this->apiKey}&channelId={$channelId}&part=snippet,id&order=date&maxResults=60";
        if ($publishedAfter) {
            $url .= "&publishedAfter={$publishedAfter}";
        }
        return $this->makeRequest($url);
    }

    // Método para obter detalhes de um vídeo específico
    public function getVideoDetails($videoId) {
        $url = $this->apiUrl . "/videos?id={$videoId}&part=snippet,contentDetails&key={$this->apiKey}";
        return $this->makeRequest($url);
    }
    
    // Método para obter detalhes de um canal específico
    public function getChannelDetails($channelId) {
        $url = $this->apiUrl . "/channels?part=snippet,contentDetails,statistics&id={$channelId}&key={$this->apiKey}";
        echo "Request URL: $url\n"; // Log da URL
        return $this->makeRequest($url);
    }

    // Método para fazer a requisição à API
    private function makeRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($response, true);
    }
}
