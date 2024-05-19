<?php

require_once __DIR__ . '/../repository/ChannelRepository.php';

header('Content-Type: application/json');

$channelRepository = new ChannelRepository();

$routes = [
    'GET' => [
        '/channels' => function($params) use ($channelRepository) {
            if (isset($params['language']) && isset($params['country'])) {
                return $channelRepository->getChannelsByLanguageAndCountry($params['language'], $params['country']);
            } elseif (isset($params['language'])) {
                return $channelRepository->getChannelsByLanguageGroupedByCountry($params['language']);
            } elseif (isset($params['country'])) {
                return $channelRepository->getChannelsByCountryGroupedByLanguage($params['country']);
            } else {
                return $channelRepository->listChannels();
            }
        },
        '/channels/recent' => function($params) use ($channelRepository) {
            return $channelRepository->getRecentChannels();
        },
        '/channels/counts/language' => function($params) use ($channelRepository) {
            return $channelRepository->countChannelsByLanguage();
        },
        '/channels/counts/country' => function($params) use ($channelRepository) {
            return $channelRepository->countChannelsByCountry();
        },
        '/channels/search' => function($params) use ($channelRepository) {
            if (isset($params['name'])) {
                return $channelRepository->getChannelByName($params['name']);
            } else {
                throw new Exception('Name parameter is required.');
            }
        },
        '/channels/\d+' => function($params) use ($channelRepository, $uri) {
            preg_match('/\/channels\/(\d+)/', $uri, $matches);
            return $channelRepository->getChannelById($matches[1]);
        }
    ]
];

function route($routes) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    $params = $_GET;

    try {
        if (isset($routes[$method])) {
            foreach ($routes[$method] as $route => $callback) {
                if (preg_match('#^' . $route . '$#', $uri)) {
                    $result = $callback($params);
                    echo json_encode($result);
                    return;
                }
            }
        }
        throw new Exception('Invalid endpoint.');
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

route($routes);
