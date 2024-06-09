<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (preg_match('#^/api/#', $uri)) {
    require_once __DIR__ . '/api.php';
} else {
    // Servir uma página HTML básica para acessos diretos
    header('Content-Type: text/html');
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Video Curator API</title>
    </head>
    <body>
        <h1>Welcome to Video Curator API</h1>
        <p>This is a simple API to manage video channels and videos.</p>
        <p>To use the API, make HTTP requests to the <code>/api</code> endpoints.</p>
        <ul>
            <li><code>GET /api/channels</code> - List all channels</li>
            <li><code>GET /api/channels?language=xx</code> - List channels by language</li>
            <li><code>GET /api/channels?country=xx</code> - List channels by country</li>
            <li><code>GET /api/channels?language=xx&country=xx</code> - List channels by language and country</li>
            <li><code>GET /api/channels/{id}</code> - Get channel by ID</li>
            <li><code>GET /api/channels/search?name=xx</code> - Search channel by name</li>
            <li><code>GET /api/channels/recent</code> - List recently added channels</li>
            <li><code>GET /api/channels/counts/language</code> - Count channels by language</li>
            <li><code>GET /api/channels/counts/country</code> - Count channels by country</li>
        </ul>
    </body>
    </html>';
}
