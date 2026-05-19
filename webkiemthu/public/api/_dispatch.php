<?php

$incomingScript = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
$apiPos = strpos($incomingScript, '/api/');
$publicBase = $apiPos !== false ? substr($incomingScript, 0, $apiPos) : '';
$publicBase = rtrim((string)$publicBase, '/');

$_SERVER['SCRIPT_NAME'] = ($publicBase === '' ? '' : $publicBase) . '/index.php';
$_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];

require __DIR__ . '/../index.php';
