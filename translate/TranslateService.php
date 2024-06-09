<?php

require_once __DIR__ . '/../repository/LanguageRepository.php';

class TranslateService {
    private $languageRepository;

    public function __construct() {
        $this->languageRepository = new LanguageRepository();
    }

    public function translate($text, $sourceLang, $targetLang) {
        $url = "https://translate.googleapis.com/translate_a/single?client=gtx&dt=t&sl={$sourceLang}&tl={$targetLang}&q=" . urlencode($text);
        $response = file_get_contents($url);
        $responseArray = json_decode($response, true);
        return $responseArray[0][0][0];
    }

    public function translateAll($text, $sourceLang) {
        $translations = [];
        $targetLanguages = $this->getTargetLanguages();
        foreach ($targetLanguages as $targetLang) {
            if ($targetLang !== $sourceLang) {
                $translations[$targetLang] = $this->translate($text, $sourceLang, $targetLang);
            }
        }
        return $translations;
    }

    public function getTargetLanguages() {
        $languages = $this->languageRepository->listLanguages();
        return array_column($languages, 'code');
    }
}
