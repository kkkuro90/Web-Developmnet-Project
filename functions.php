<?php
require_once 'config.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}
function flash($message, $type = 'success') {
    $_SESSION['flash'] = compact('message', 'type');
}
function displayFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        echo "<div class='flash {$flash['type']}'>{$flash['message']}</div>";
        unset($_SESSION['flash']);
    }
}
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>