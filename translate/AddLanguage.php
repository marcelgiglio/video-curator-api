<?php

require_once __DIR__ . '/TranslateService.php';
require_once __DIR__ . '/../repository/VideoRepository.php';
require_once __DIR__ . '/../repository/VideoTranslationRepository.php';
require_once __DIR__ . '/../repository/LanguageRepository.php';

class AddLanguage {
    private $translateService;
    private $videoRepository;
    private $translationRepository;
    private $languageRepository;

    public function __construct() {
        $this->translateService = new TranslateService();
        $this->videoRepository = new VideoRepository();
        $this->translationRepository = new VideoTranslationRepository();
        $this->languageRepository = new LanguageRepository();
    }

    public function addLanguage($newLanguageCode, $languageName) {
        // Adiciona o novo idioma ao banco de dados
        $this->languageRepository->addLanguage($newLanguageCode, $languageName);
        
        // Traduz todos os vídeos existentes
        $videos = $this->videoRepository->listVideos();
        foreach ($videos as $video) {
            $title = $video['title'];
            $description = $video['description'];
            $originalLanguage = $video['original_language'];

            if ($originalLanguage !== $newLanguageCode) {
                $translatedTitle = $this->translateService->translate($title, $originalLanguage, $newLanguageCode);
                $translatedDescription = $this->translateService->translate($description, $originalLanguage, $newLanguageCode);

                $this->translationRepository->addVideoTranslation(
                    $video['video_id'],
                    $newLanguageCode,
                    $translatedTitle,
                    $translatedDescription
                );
            }
        }
    }
}

// Exemplo de uso
$addLanguage = new AddLanguage();
$addLanguage->addLanguage('it', 'Italian'); // Substitua 'it' e 'Italian' pelo código e nome do novo idioma que deseja adicionar
