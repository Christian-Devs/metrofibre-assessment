<?php

/**
 * GET  /api.php  -> solves using the default assessment stock
 * POST /api.php  -> solves using custom stock: {"ingredients": {"Dough": 5, ...}}
 *
 * PHP 5.2+ compatible (no http_response_code(), no short arrays).
 */

$root = dirname(dirname(__FILE__));
require $root . '/Solver.php';
$data = require $root . '/data.php';

// The search is exhaustive, so quantities are capped to keep response times bounded.
define('MAX_QUANTITY', 20);

function respond($status, $payload)
{
    header('HTTP/1.1 ' . $status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$ingredients = $data['ingredients'];

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);

    if (!is_array($body) || !isset($body['ingredients']) || !is_array($body['ingredients'])) {
        respond('400 Bad Request', array('error' => 'Body must be JSON with an "ingredients" object.'));
    }

    foreach ($body['ingredients'] as $name => $qty) {
        if (!isset($ingredients[$name])) {
            respond('400 Bad Request', array('error' => 'Unknown ingredient: ' . $name));
        }
        if (!is_int($qty) || $qty < 0 || $qty > MAX_QUANTITY) {
            respond('400 Bad Request', array(
                'error' => $name . ' must be a whole number between 0 and ' . MAX_QUANTITY . '.',
            ));
        }
        $ingredients[$name] = $qty;
    }
}elseif ($method !== 'GET') {
    header('Allow: GET, POST');
    respond('405 Method Not Allowed', array('error' => 'Use GET or POST.'));
}

$solver = new Solver($data['recipes']);

respond('200 OK', array(
    'ingredients' => $ingredients,
    'recipes' => $data['recipes'],
    'result' => $solver->solve($ingredients)
));