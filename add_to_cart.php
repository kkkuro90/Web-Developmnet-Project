<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$product_id = intval($_POST['product_id']);
$user_id = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?")
       ->execute([$user_id, $product_id]);
} else {
    $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)")
       ->execute([$user_id, $product_id]);
}

header('Location: cart.php');
exit();
?>