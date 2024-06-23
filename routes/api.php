<?php

require_once __DIR__ . '/route.php';

header('Content-Type: application/json');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (preg_match('#^/api/channels#', $uri)) {
    require_once __DIR__ . '/channels.php';
} elseif (preg_match('#^/api/videos#', $uri)) {
    require_once __DIR__ . '/videos.php';
} else {
    echo json_encode(['error' => 'Invalid endpoint.']);
    exit;
}

?>
