<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['product_id']);

$pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?")
    ->execute([$user_id, $product_id]);

header('Location: basket.php');
exit();