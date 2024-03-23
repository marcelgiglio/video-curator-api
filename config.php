<?php

require_once __DIR__ . '/vendor/autoload.php';

// Carrega as variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configurações do banco de dados
define('DB_HOST', getenv('DB_HOST'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));

// Configurações da API do Google Cloud Translation
define('GOOGLE_CLOUD_TRANSLATION_API_KEY', getenv('GOOGLE_CLOUD_TRANSLATION_API_KEY'));

// Configurações da API do YouTube
define('GOOGLE_YOUTUBE_API_KEY', getenv('GOOGLE_YOUTUBE_API_KEY'));
