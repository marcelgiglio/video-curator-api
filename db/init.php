<?php

require_once 'database.php'; // Inclui o arquivo com as funções de banco de dados

function createTables() {
    $createChannelsTable = "
        CREATE TABLE IF NOT EXISTS channels (
            channel_id INT PRIMARY KEY,
            name VARCHAR(255),
            country VARCHAR(255),
            language VARCHAR(255),
            url VARCHAR(255),
            description TEXT
        );
    ";

    $createVideosTable = "
        CREATE TABLE IF NOT EXISTS videos (
            video_id INT PRIMARY KEY,
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
            translation_id INT PRIMARY KEY,
            video_id INT,
            language VARCHAR(255),
            translated_title VARCHAR(255),
            translated_description TEXT,
            FOREIGN KEY (video_id) REFERENCES videos(video_id)
        );
    ";

    // Executa as queries para criar as tabelas
    executeQuery($createChannelsTable);
    executeQuery($createVideosTable);
    executeQuery($createVideoTranslationsTable);
}

// Chama a função para criar as tabelas
createTables();

?>
