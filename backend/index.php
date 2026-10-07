<?php
header('Content-Type: application.json');
echo json_encode(array('php_version' => PHP_VERSION));