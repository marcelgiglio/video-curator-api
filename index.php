<?php

function getDefinedRoutes($directory) {
    $routes = [];
    
    // Itera sobre todos os arquivos PHP na pasta
    foreach (new DirectoryIterator($directory) as $fileInfo) {
        if ($fileInfo->isDot() || $fileInfo->isDir() || $fileInfo->getExtension() !== 'php') {
            continue;
        }
        
        // Lê o conteúdo do arquivo
        $fileContent = file_get_contents($fileInfo->getPathname());
        
        // Usa regex para encontrar todas as rotas definidas com Route::add
        preg_match_all('/Route::add\(\s*\'([^\']+)\'/', $fileContent, $matches);
        
        if (!empty($matches[1])) {
            $routes = array_merge($routes, $matches[1]);
        }
    }
    
    return $routes;
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (preg_match('#^/api/#', $uri)) {
    require_once __DIR__ . '/route/ApiRoute.php';
} else {
    // Servir uma página HTML básica para acessos diretos
    header('Content-Type: text/html');
    
    // Obtém todas as rotas definidas
    $routes = getDefinedRoutes(__DIR__ . '/route');
    
    // Define as rotas que não devem ser exibidas
    $excludedRoutes = [
        '/api/channels/add',
        '/api/channels/delete'
    ];
    
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Video Curator API</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                color: #333;
                margin: 0;
                padding: 0;
                line-height: 1.6;
            }
            .container {
                max-width: 900px;
                margin: auto;
                padding: 20px;
            }
            h1 {
                text-align: center;
                margin-bottom: 20px;
            }
            p {
                font-size: 18px;
                margin-bottom: 20px;
            }
            ul {
                list-style: none;
                padding: 0;
            }
            li {
                background: #fff;
                margin-bottom: 10px;
                padding: 15px;
                border-radius: 5px;
                border: 1px solid #ddd;
            }
            li a {
                text-decoration: none;
                color: #007bff;
                font-weight: bold;
            }
            li a:hover {
                text-decoration: underline;
            }
            .footer {
                text-align: center;
                margin-top: 40px;
                font-size: 14px;
                color: #aaa;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Welcome to Video Curator API</h1>
            <p>This is a simple API to manage video channels and videos.</p>
            <p>To use the API, make HTTP requests to the <code>/api</code> endpoints.</p>';
    
    // Verifica se há rotas para listar
    if (!empty($routes)) {
        echo '<ul>';
        
        foreach ($routes as $route) {
            // Substitui o padrão ([a-zA-Z0-9_-]+) por 'example-id'
            $displayRoute = preg_replace('/\(\[a-zA-Z0-9_\-\]\+\)/', '[id]', $route);
            $exampleRoute = preg_replace('/\(\[a-zA-Z0-9_\-\]\+\)/', 'example-id', $route);
        
            // Exclui as rotas definidas
            if (in_array($route, $excludedRoutes)) {
                continue;
            }
        
            // Torna a rota clicável
            echo '<li><a href="' . htmlspecialchars($exampleRoute) . '">GET ' . htmlspecialchars($displayRoute) . '</a></li>';
        }        
        
        echo '</ul>';
    }
    
    echo '</div>
        <div class="footer">
            &copy; 2024 Video Curator API. All rights reserved.
        </div>
    </body>
    </html>';
}

?>
