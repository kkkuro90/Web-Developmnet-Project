<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();
header('Content-Type: application/json');

// Добавляем отладочную информацию
$debug = [
    'session_data' => $_SESSION,
    'session_id' => session_id(),
    'time' => date('Y-m-d H:i:s')
];

$response = [
    'isAuthenticated' => isset($_SESSION['user_id']) && isset($_SESSION['username']),
    'username' => $_SESSION['username'] ?? null,
    'role' => $_SESSION['role'] ?? null,
    'debug' => $debug
];

echo json_encode($response);
?> 