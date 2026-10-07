<?php
header('Content-Type: application/json');
$data = require dirname(__FILE__) . '/data.php';
echo json_encode($data);