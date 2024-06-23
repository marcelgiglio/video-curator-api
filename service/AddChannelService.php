<?php

set_time_limit(1000); // Aumenta o limite de tempo para 1000 segundos

require_once __DIR__ . '/../repository/ChannelRepository.php';
require_once __DIR__ . '/VideoProcessorService.php';
require_once __DIR__ . '/../gateway/YouTubeGateway.php';
require_once __DIR__ . '/../log/log.php';

class AddChannel {
    private $youtubeApi;
    private $channelRepository;
    private $videoProcessor;

    public function __construct() {
        $this->youtubeApi = new YouTubeDataAPI();
        $this->channelRepository = new ChannelRepository();
        $this->videoProcessor = new VideoProcessor($this->youtubeApi);
    }

    public function addNewChannel($channelId) {
        // Verifica se o canal já existe no banco de dados
        if ($this->channelRepository->channelExists($channelId)) {
            logError("Channel already exists in the database: " . $channelId);
            return ['status' => 'error', 'message' => 'Channel already exists'];
        }
        
        // Busca informações do canal na API do YouTube
        $channelDetails = $this->youtubeApi->getChannelDetails($channelId);
        
        if (empty($channelDetails['items'])) {
            return ['status' => 'error', 'message' => 'Channel not found'];
        }

        $channelInfo = $channelDetails['items'][0];
        $name = $channelInfo['snippet']['title'] ?? 'No name';
        $description = $channelInfo['snippet']['description'] ?? 'No description';
        $country = $channelInfo['snippet']['country'] ?? 'Unknown';
        $language = $channelInfo['snippet']['defaultLanguage'] ?? 'Unknown';
        $url = 'https://www.youtube.com/channel/' . $channelId;

        // Adiciona o canal no banco de dados
        $this->channelRepository->addChannel($channelId, $name, $country, $language, $url, $description);

        // Busca vídeos do canal no YouTube
        $videos = $this->youtubeApi->getVideosByChannel($channelId);

        // Processa cada vídeo do canal
        foreach ($videos['items'] as $video) {
            if (isset($video['id']['videoId'])) {
                $videoId = $video['id']['videoId'];
                $this->videoProcessor->processVideo($channelId, $videoId);
            } else {
                logError("Video ID missing for a video in channel: " . $channelId);
            }
        }
        
        return ['status' => 'success', 'message' => 'Channel added successfully'];

    }
}
?>
