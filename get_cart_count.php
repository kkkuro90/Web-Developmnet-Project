<?php
session_start();
require_once 'config.php';

$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $cart_count = $row['total'] ?? 0;
} 

echo json_encode(['count' => (int)$cart_count]);