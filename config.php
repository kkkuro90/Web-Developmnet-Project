<?php

define('APP_ENV', 'development');
define('APP_DEBUG', APP_ENV === 'development');


define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'marketplace_db');
define('DB_CHARSET', 'utf8mb4'); 


define('UPLOAD_DIR', __DIR__ . '/../uploads/'); 
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_TYPES', [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif'
]);


define('SESSION_NAME', 'MARKETPLACE_SESSID');
define('SESSION_LIFETIME', 86400); 
define('SESSION_SECURE', true); 
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Strict');


if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
        'secure' => APP_ENV === 'production' ? SESSION_SECURE : false,
        'httponly' => SESSION_HTTPONLY,
        'samesite' => SESSION_SAMESITE
    ]);
    session_start();
}


try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false, 
        PDO::ATTR_STRINGIFY_FETCHES => false
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {

    error_log("Database connection error: " . $e->getMessage());
    
    if (APP_DEBUG) {
        die("Ошибка подключения к базе данных: " . htmlspecialchars($e->getMessage()));
    } else {
        die("Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.");
    }
}


function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

date_default_timezone_set('Europe/Moscow');
?>