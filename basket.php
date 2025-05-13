<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина покупок</title>
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
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: var(--text-white);
            min-height: 100vh;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .cart-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--glass-white);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
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
        
        .cart-empty {
            text-align: center;
            padding: 3rem;
            font-size: 1.2rem;
        }
        
        .cart-items {
            margin-bottom: 2rem;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            margin-bottom: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            transition: all 0.3s;
        }
        
        .cart-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        
        .cart-item-image {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
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
            margin: 0;
            opacity: 0.9;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
            margin: 0.5rem 0;
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
            display: flex;
            align-items: center;
            justify-content: center;
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
        
        .cart-summary {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid var(--glass-border);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
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
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .checkout-btn:hover {
            background: #00C764;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 230, 118, 0.3);
        }
        
        .continue-shopping {
            display: inline-block;
            margin-top: 1rem;
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: all 0.3s;
        }
        
        .continue-shopping:hover {
            opacity: 1;
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
                text-align: center;
            }
            
            .cart-item-image {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .cart-item-details {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="cart-container">
        <div class="cart-header">
            <h1 class="cart-title">Ваша корзина</h1>
            <a href="index.php" class="continue-shopping">← Продолжить покупки</a>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <div class="cart-empty">
                <p>Ваша корзина пуста</p>
                <a href="index.php" class="continue-shopping">Вернуться к покупкам</a>
            </div>
        <?php else: ?>
            <form action="cart.php" method="post" class="cart-items">
                <?php foreach ($cart_items as $product_id => $item): ?>
                    <div class="cart-item">
                        <img src="uploads/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-item-image">
                        <div class="cart-item-details">
                            <h3 class="cart-item-title"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="cart-item-price"><?= number_format($item['price'], 2, ',', ' ') ?> ₽</p>
                            <div class="cart-item-quantity">
                                <button type="button" class="quantity-btn minus" data-id="<?= $product_id ?>">-</button>
                                <input type="number" name="quantity[<?= $product_id ?>]" value="<?= $item['quantity'] ?>" min="1" class="quantity-input">
                                <button type="button" class="quantity-btn plus" data-id="<?= $product_id ?>">+</button>
                            </div>
                            <button type="button" class="cart-item-remove" onclick="removeItem(<?= $product_id ?>)">Удалить</button>
                        </div>
                        <div class="cart-item-total">
                            <p><?= number_format($item['total'], 2, ',', ' ') ?> ₽</p>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Товаров:</span>
                        <span><?= array_sum(array_column($cart_items, 'quantity')) ?></span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Итого:</span>
                        <span><?= number_format($total_price, 2, ',', ' ') ?> ₽</span>
                    </div>
                    <button type="submit" class="save-btn" style="margin-top: 1rem;">Обновить корзину</button>
                </div>
                
                <a href="checkout.php" class="checkout-btn">Оформить заказ</a>
            </form>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>

        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.quantity-input');
                let value = parseInt(input.value);
                
                if (this.classList.contains('minus') && value > 1) {
                    input.value = value - 1;
                } else if (this.classList.contains('plus')) {
                    input.value = value + 1;
                }
            });
        });
        

        function removeItem(product_id) {
            if (confirm('Удалить товар из корзины?')) {
                window.location.href = 'cart.php?remove=' + product_id;
            }
        }
        

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>