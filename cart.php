<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $product_id = intval($product_id);
            $quantity = intval($quantity);
            if ($quantity < 1) {

                $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?")
                   ->execute([$user_id, $product_id]);
            } else {

                $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?")
                   ->execute([$quantity, $user_id, $product_id]);
            }
        }
        header('Location: cart.php');
        exit();
    }
}

if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?")
       ->execute([$user_id, $product_id]);
    header('Location: cart.php');
    exit();
}

$stmt = $pdo->prepare("SELECT p.id AS product_id, p.name, p.price, p.image, p.description, c.quantity, p.price * c.quantity AS total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_price = 0;
foreach ($cart_items as &$item) {
    $item['total'] = $item['price'] * $item['quantity'];
    $total_price += $item['total'];
}

unset($item);
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Корзина</title>
    <link rel="stylesheet" href="styles_cart.css">
</head>
<body>
    <main class="cart-container">
        <div class="cart-header">
            <h1 class="cart-title">Ваша корзина</h1>
            <a href="catalog.php" class="continue-shopping">← Продолжить покупки</a>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="cart-empty">
                <p>Ваша корзина пуста</p>
            </div>
        <?php else: ?>
            <form action="cart.php" method="post" class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">

                        <div class="cart-item-image">
                            <img src="uploads/products/<?= htmlspecialchars($item['image']) ?>" 
                                alt="<?= htmlspecialchars($item['name']) ?>"
                                onerror="this.src='images/no_image.png'; this.alt='Нет изображения';">
                        </div>


                        <div class="cart-item-details">
                            <h3 class="cart-item-title"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="cart-item-description"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                            <p class="cart-item-price"><?= number_format($item['price'], 0, '', ' ') ?> ₽</p>



                            <div class="cart-item-quantity">
                                <button type="button" class="quantity-btn minus">−</button>
                                <input type="number" name="quantity[<?= $item['product_id'] ?>]" value="<?= $item['quantity'] ?>" class="quantity-input" min="1">
                                <button type="button" class="quantity-btn plus">+</button>
                            </div>

                            <button type="button" class="cart-item-remove" onclick="removeItem(<?= $item['product_id'] ?>)">Удалить</button>
                        </div>


                        <div class="cart-item-total">
                            <p><?= number_format($item['total'], 0, '', ' ') ?> ₽</p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Товаров:</span>
                        <span class="cart-count"><?= !empty($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?></span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Итого:</span>
                        <span><?= number_format($total_price, 0, '', ' ') ?> ₽</span>
                    </div>
                    <?php if (!empty($cart_items)): ?>
                        <button type="submit" form="checkout-form" class="checkout-btn">Оформить заказ</button>
                    <?php endif; ?>
                </div>
            </form>
        <?php endif; ?>
    </main>

    <?php if (!empty($cart_items)): ?>
        <form action="checkout.php" method="post" id="checkout-form">
            <?php foreach ($cart_items as $item): ?>
                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
            <?php endforeach; ?>
        </form>
    <?php endif; ?>

    <script>
        function updateCartCount() {
            fetch('get_cart_count.php')
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.count;
                    }
                });
        }

        window.addEventListener('DOMContentLoaded', updateCartCount);

        document.querySelectorAll('.quantity-btn, .cart-item-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                setTimeout(updateCartCount, 500);
            });
        });

        document.querySelector('form')?.addEventListener('submit', () => {
            setTimeout(updateCartCount, 800);
        });
    </script>


    <script>
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const input = this.parentElement.querySelector('.quantity-input');
                let value = parseInt(input.value);
                if (this.classList.contains('minus') && value > 1) {
                    input.value = value - 1;
                } else if (this.classList.contains('plus')) {
                    input.value = value + 1;
                }
                input.form.submit();
            });
        });

        function removeItem(product_id) {
            window.location.href = 'cart.php?remove=' + product_id;
        }

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function () {
                this.form.submit();
            });
        });
    </script>
</body>
</html>