<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $pdo->prepare("DELETE FROM orders WHERE user_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);

    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);

    session_unset();
    session_destroy();

    header('Location: register.php');
    exit();
} catch (PDOException $e) {
    die("Ошибка при удалении аккаунта: " . $e->getMessage());
}