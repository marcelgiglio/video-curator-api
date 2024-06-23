<?php

require_once __DIR__ . '/../repository/LanguageRepository.php';

class TranslateService {
    private $languageRepository;

    public function __construct() {
        $this->languageRepository = new LanguageRepository();
    }

    public function translate($text, $sourceLang, $targetLang) {
        // Base URL da API de tradução
        $baseUrl = "https://translate.googleapis.com/translate_a/single?client=gtx&dt=t&sl={$sourceLang}&tl={$targetLang}&q=";
        
        // Codificar o texto e garantir que a URL completa não ultrapasse 2000 caracteres
        $encodedText = urlencode($text);
        $maxLength = 2000 - strlen($baseUrl);
        $encodedText = substr($encodedText, 0, $maxLength);

        try {
            $url = $baseUrl . $encodedText;
            $response = file_get_contents($url);

            if ($response === FALSE) {
                error_log("Erro ao acessar a API de tradução.");
                return null;
            }

            $responseArray = json_decode($response, true);

            // Verificação para garantir que a estrutura do array está correta
            if (is_array($responseArray) && isset($responseArray[0]) && isset($responseArray[0][0]) && isset($responseArray[0][0][0])) {
                return $responseArray[0][0][0];
            } else {
                error_log("Erro na estrutura da resposta da API de tradução.");
                return null;
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function translateAll($text, $sourceLang) {
        $translations = [];
        $targetLanguages = $this->getTargetLanguages();
        foreach ($targetLanguages as $targetLang) {
            if ($targetLang !== $sourceLang) {
                $translation = $this->translate($text, $sourceLang, $targetLang);
                if ($translation !== null) {
                    $translations[$targetLang] = $translation;
                }
            }
        }
        return $translations;
    }

    public function getTargetLanguages() {
        $languages = $this->languageRepository->listLanguages();
        return array_column($languages, 'code');
    }
}
?>
