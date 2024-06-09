<?php

class TranslateService {
    private $targetLanguages = ['de', 'ar', 'en', 'es', 'pt', 'uk', 'fr', 'ru'];

    public function translate($text, $sourceLang, $targetLang) {
        $url = "https://translate.googleapis.com/translate_a/single?client=gtx&dt=t&sl={$sourceLang}&tl={$targetLang}&q=" . urlencode($text);
        $response = file_get_contents($url);
        $responseArray = json_decode($response, true);
        return $responseArray[0][0][0];
    }

    public function translateAll($text, $sourceLang) {
        $translations = [];
        foreach ($this->targetLanguages as $targetLang) {
            if ($targetLang !== $sourceLang) {
                $translations[$targetLang] = $this->translate($text, $sourceLang, $targetLang);
            }
        }
        return $translations;
    }

    public function addTargetLanguage($languageCode) {
        if (!in_array($languageCode, $this->targetLanguages)) {
            $this->targetLanguages[] = $languageCode;
        }
    }

    public function getTargetLanguages() {
        return $this->targetLanguages;
    }
}
