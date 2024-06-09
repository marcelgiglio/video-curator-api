<?php

require_once __DIR__ . '/database.php';

function createTables($db) {
    $createChannelsTable = "
        CREATE TABLE IF NOT EXISTS channels (
            channel_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            country VARCHAR(255),
            language VARCHAR(255),
            url VARCHAR(255),
            description TEXT
        );
    ";

    $createVideosTable = "
        CREATE TABLE IF NOT EXISTS videos (
            video_id INT AUTO_INCREMENT PRIMARY KEY,
            channel_id INT,
            title VARCHAR(255),
            description TEXT,
            original_language VARCHAR(255),
            publication_date DATE,
            duration INT,
            url VARCHAR(255),
            thumbnail VARCHAR(255),
            FOREIGN KEY (channel_id) REFERENCES channels(channel_id)
        );
    ";

    $createVideoTranslationsTable = "
        CREATE TABLE IF NOT EXISTS video_translations (
            translation_id INT AUTO_INCREMENT PRIMARY KEY,
            video_id INT,
            language VARCHAR(255),
            translated_title VARCHAR(255),
            translated_description TEXT,
            FOREIGN KEY (video_id) REFERENCES videos(video_id)
        );
    ";

    $createLanguagesTable = "
        CREATE TABLE IF NOT EXISTS languages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(5) NOT NULL,
            name VARCHAR(50) NOT NULL
        );
    ";

    // Executa as queries para criar as tabelas
    $db->executeQuery($createChannelsTable);
    $db->executeQuery($createVideosTable);
    $db->executeQuery($createVideoTranslationsTable);
    $db->executeQuery($createLanguagesTable);
}

// Instancia a classe Database
$db = new Database();

// Chama a função para criar as tabelas
createTables($db);

// Fecha a conexão
$db->closeConnection();
