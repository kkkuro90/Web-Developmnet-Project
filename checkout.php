<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$product_id = $_POST['product_id'] ?? null;

if (!$product_id) {
    die("Не выбрано ни одного товара");
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Товар не найден");
}

$total_price = $product['price'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оплата</title>
    <link rel="stylesheet" href="styles-checkout.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
</head>
<body>

<h2>Оплатите следующий товар:</h2>

<div class="checkout-product">
    <img src="uploads/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
    <div class="product-info">
        <h3><?= htmlspecialchars($product['name']) ?></h3>
        <p>Цена: <?= number_format($product['price'], 0, '', ' ') ?> ₽</p>
    </div>
</div>

<p><strong>Итого:</strong> <?= number_format($total_price, 0, '', ' ') ?> ₽</p>

<form action="process_payment.php" method="post">
    <input type="hidden" name="product_id" value="<?= $product_id ?>">

    <label for="card_number">Номер карты:</label>
    <input type="text" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX" required>

    <label for="card_name">Имя держателя карты:</label>
    <input type="text" id="card_name" name="card_name" placeholder="IVAN IVANOV" required>

    <label for="exp_date">Срок действия (ММ/ГГ):</label>
    <input type="text" id="exp_date" name="exp_date" placeholder="MM/YY" required>

    <label for="cvv">CVV:</label>
    <input type="text" id="cvv" name="cvv" placeholder="XXX" required>

    <button type="submit">Подтвердить платеж</button>
</form>

<script>
    new Cleave('#card_number', {
        creditCard: true,
        delimiter: ' ',
        blocks: [4, 4, 4, 4]
    });
    new Cleave('#card_name', {
        uppercase: true,
        stripLeadingZeroes: true
    });
    new Cleave('#exp_date', {
        date: true,
        datePattern: ['m', 'y'],
        delimiter: '/'
    });
    new Cleave('#cvv', {
        blocks: [3],
        numericOnly: true
    });
</script>

</body>
</html>