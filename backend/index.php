<?php
header('Content-Type: application/json');

require dirname(__FILE__) . '/Solver.php';
$data = require dirname(__FILE__) . '/data.php';

$solver = new Solver($data['recipes']);
echo json_encode($solver->solve($data['ingredients']));