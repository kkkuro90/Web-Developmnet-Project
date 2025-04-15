<?php
require_once 'php/functions.php';

if (!isLoggedIn()) {
    flash('Для доступа необходимо авторизоваться', 'error');
    header('Location: php/login.php');
    exit();
}
?>