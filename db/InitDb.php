<?php

require_once __DIR__ . '/DatabaseDb.php';

function createTables($db) {
    $charset = 'utf8mb4';
    $collate = 'utf8mb4_unicode_ci';

    $createChannelsTable = "
        CREATE TABLE IF NOT EXISTS channels (
            channel_id VARCHAR(255) PRIMARY KEY,
            name VARCHAR(255),
            country VARCHAR(255),
            language VARCHAR(255),
            url VARCHAR(255),
            description TEXT
        ) CHARACTER SET $charset COLLATE $collate;
    ";

    $createVideosTable = "
        CREATE TABLE IF NOT EXISTS videos (
            video_id VARCHAR(255) PRIMARY KEY,
            channel_id VARCHAR(255),
            title VARCHAR(255),
            description TEXT,
            original_language VARCHAR(255),
            publication_date DATE,
            duration INT,
            url VARCHAR(255),
            thumbnail VARCHAR(255),
            FOREIGN KEY (channel_id) REFERENCES channels(channel_id)
        ) CHARACTER SET $charset COLLATE $collate;
    ";

    $createVideoTranslationsTable = "
        CREATE TABLE IF NOT EXISTS video_translations (
            translation_id INT AUTO_INCREMENT PRIMARY KEY,
            video_id VARCHAR(255),
            language VARCHAR(255),
            translated_title VARCHAR(255),
            translated_description TEXT,
            FOREIGN KEY (video_id) REFERENCES videos(video_id)
        ) CHARACTER SET $charset COLLATE $collate;
    ";

    $createLanguagesTable = "
        CREATE TABLE IF NOT EXISTS languages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(5) NOT NULL,
            name VARCHAR(50) NOT NULL
        ) CHARACTER SET $charset COLLATE $collate;
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
