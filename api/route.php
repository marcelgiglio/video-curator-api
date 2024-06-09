<?php

class Route {
    private static $routes = [];

    public static function add($route, $callback, $method = 'get') {
        self::$routes[] = ['route' => $route, 'callback' => $callback, 'method' => $method];
    }

    public static function run($basepath = '/') {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        foreach (self::$routes as $route) {
            $route['route'] = '^' . str_replace(['*', '/'], ['.*', '\/'], $route['route']) . '$';
            if (preg_match('#' . $route['route'] . '#', $uri, $matches) && $method == $route['method']) {
                array_shift($matches);
                call_user_func_array($route['callback'], $matches);
                return;
            }
        }

        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        echo '404 Not Found';
    }
}

?>
