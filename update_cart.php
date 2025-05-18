<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$quantity = max(1, intval($_POST['quantity']));

$pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?")
    ->execute([$quantity, $user_id, $product_id]);

header('Location: basket.php');
exit();