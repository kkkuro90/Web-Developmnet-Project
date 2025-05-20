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

$stmt = $pdo->prepare("
    SELECT c.product_id, c.quantity, p.name, p.price, p.image 
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
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
    <style>

        :root {
            --primary-purple: #AA00FF;
            --primary-green: #00E676;
            --glass-white: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-white: rgba(255, 255, 255, 0.9);
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(-45deg, var(--primary-purple), var(--primary-green));
            color: var(--text-white);
            min-height: 100vh;
        }

        .cart-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--glass-white);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .cart-title {
            font-size: 2rem;
            margin: 0;
        }

        .continue-shopping {
            color: white;
            text-decoration: none;
        }

        .cart-empty {
            text-align: center;
            padding: 3rem;
            font-size: 1.2rem;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            margin-bottom: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
        }

        .cart-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 1.5rem;
            border: 1px solid var(--glass-border);
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-title {
            font-size: 1.2rem;
            margin: 0 0 0.5rem 0;
        }

        .cart-item-price {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        .quantity-input {
            width: 60px;
            padding: 0.5rem;
            text-align: center;
            border-radius: 30px;
            border: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            margin: 0 0.5rem;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background: var(--primary-purple);
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .quantity-btn:hover {
            background: #9900E0;
            transform: scale(1.1);
        }

        .cart-item-remove {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .cart-item-remove:hover {
            color: #DC3545;
        }

        .summary-total {
            font-size: 1.3rem;
            font-weight: 500;
            border-top: 1px solid var(--glass-border);
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            margin-top: 1.5rem;
            background: var(--primary-green);
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .checkout-btn:hover {
            background: #00C764;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 230, 118, 0.3);
        }
        .product-card img,
        .product-image img {
            max-width: 10%;
            height: auto;
            max-height: 250px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
                text-align: center;
            }
            .cart-item-image {
                margin: 0 0 1rem 0;
            }
        }

        .cart-empty {
        position: relative;
        text-align: center;
        padding: 4rem 2rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
    }

    .cart-empty p {
        font-size: 1.2rem;
        color: white;
        margin-bottom: 1.5rem;
    }

    .continue-shopping {
        position: absolute;
        top: 1rem;
        right: 2rem;
        background-color: #AA00FF;
        color: white;
        padding: 0.6rem 1.2rem;
        border-radius: 30px;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    .continue-shopping:hover {
        background-color: #9900E0;
    }
    </style>
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
                <a href="catalog.php" class="continue-shopping">Вернуться к покупкам</a>
            </div>
        <?php else: ?>
            <form action="cart.php" method="post" class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-details">
                            <h3 class="cart-item-title"><?= htmlspecialchars($item['name']) ?></h3>
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
                        <span><?= count($cart_items) ?></span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Итого:</span>
                        <span><?= number_format($total_price, 0, '', ' ') ?> ₽</span>
                    </div>
                    <button type="submit" class="checkout-btn">Оформить заказ</button>
                </div>
            </form>
        <?php endif; ?>
    </main>

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