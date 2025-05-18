<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $card_number = $_POST['card_number'] ?? '';
    $card_name = $_POST['card_name'] ?? '';
    $exp_date = $_POST['exp_date'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    if (!$product_id) {
        die("Не выбрано ни одного товара");
    }

    // Сохраняем заказ
    $user_id = $_SESSION['user_id'];
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $pdo->prepare("
            INSERT INTO orders (user_id, product_id, total_amount, status)
            VALUES (?, ?, ?, 'paid')
        ")->execute([$user_id, $product_id, $product['price']]);

        $pdo->commit();

        header("Location: payment_success.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Ошибка оплаты: " . $e->getMessage();
        exit();
    }
}

header("Location: cart.php");
exit();
?>