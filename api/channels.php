<?php
// Incluir o arquivo de inicialização do banco de dados
require_once '../db/database.php';

// Bloquear métodos que não são GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido.']);
    exit;
}

// Conectar ao banco de dados
$db = new Database();

header('Content-Type: application/json');

// Construir a query SQL baseada nos parâmetros de consulta
$query = "SELECT * FROM channels WHERE 1=1";
$params = [];

// Filtrar por país, se especificado
if (isset($_GET['country'])) {
    $country = $_GET['country'];
    if (strpos($country, '-') === 0) {
        $country = substr($country, 1);
        $query .= " AND country != ?";
    } else {
        $query .= " AND country = ?";
    }
    $params[] = $country;
}

// Filtrar por idioma, se especificado
if (isset($_GET['language'])) {
    $language = $_GET['language'];
    if (strpos($language, '-') === 0) {
        $language = substr($language, 1);
        $query .= " AND language != ?";
    } else {
        $query .= " AND language = ?";
    }
    $params[] = $language;
}

// Executar a query e retornar o resultado
$result = $db->fetchAll($query, $params);

if ($result) {
    echo json_encode(['status' => 'success', 'data' => $result]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No channels found.']);
}
?>
