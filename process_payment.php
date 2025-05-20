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
        // Получаем информацию о товаре
        $stmt = $pdo->prepare("SELECT name, price, category FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Проверяем, есть ли товар в корзине
        $cart_stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $cart_stmt->execute([$user_id, $product_id]);
        $cart_item = $cart_stmt->fetch(PDO::FETCH_ASSOC);

        // Определяем количество
        $quantity = $cart_item ? $cart_item['quantity'] : 1;
        
        // Вычисляем общую сумму
        $total_amount = $product['price'] * $quantity;

        // Создаем заказ
        $pdo->prepare("
            INSERT INTO orders (user_id, product_id, product_name, quantity, price, category, total_amount, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'completed')
        ")->execute([
            $user_id, 
            $product_id, 
            $product['name'],
            $quantity,
            $product['price'],
            $product['category'],
            $total_amount
        ]);

        // Если товар был в корзине, удаляем его
        if ($cart_item) {
            $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?")
                ->execute([$user_id, $product_id]);
        }

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