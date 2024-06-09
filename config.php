<?php

require_once __DIR__ . '/vendor/autoload.php';

// Carrega as variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configurações do banco de dados
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);

// Configurações da API do Google Cloud Translation
define('GOOGLE_CLOUD_TRANSLATION_API_KEY', $_ENV['GOOGLE_CLOUD_TRANSLATION_API_KEY']);

// Configurações da API do YouTube
define('GOOGLE_YOUTUBE_API_KEY', $_ENV['GOOGLE_YOUTUBE_API_KEY']);
