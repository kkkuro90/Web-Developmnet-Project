<?php
require_once 'config.php';
require_once 'functions.php';

$_SESSION = [];
session_destroy();

flash('Вы успешно вышли из системы');
header('Location: index.php');
exit();
?>