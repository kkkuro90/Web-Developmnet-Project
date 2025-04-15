<?php
require_once 'functions.php';

if (!isLoggedIn()) {
    flash('Для доступа необходимо авторизоваться', 'error');
    header('Location: login.php');
    exit();
}
?>