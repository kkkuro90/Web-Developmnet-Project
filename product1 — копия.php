<?php
session_start();
require_once 'config.php';

// Получаем ID товара из URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    die("Неверный ID товара");
}

// Получаем информацию о товаре
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Товар не найден");
}

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

// --- Обработка формы "В корзину" ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Проверяем, есть ли товар уже в корзине
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);

    if ($stmt->rowCount() > 0) {
        // Увеличиваем количество
        $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?")
           ->execute([$user_id, $product_id]);
    } else {
        // Добавляем товар в корзину
        $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)")
           ->execute([$user_id, $product_id]);
    }

    // Перенаправляем, чтобы избежать повторной отправки формы
    header("Location: product.php?id=$product_id&added=1");
    exit();
}

// --- Обработка формы отзыва ---
$error = $success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5) {
        $error = "Пожалуйста, выберите рейтинг от 1 до 5";
    } elseif (!empty($comment)) {
        try {
            // Проверяем, нет ли уже отзыва у этого пользователя
            $checkStmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? AND user_id = ?");
            $checkStmt->execute([$product_id, $user_id]);

            if ($checkStmt->rowCount() > 0) {
                // Обновляем существующий отзыв
                $pdo->prepare("UPDATE reviews SET rating = ?, comment = ?, created_at = NOW() WHERE product_id = ? AND user_id = ?")
                   ->execute([$rating, $comment, $product_id, $user_id]);
                $success = "Отзыв успешно обновлён!";
            } else {
                // Вставляем новый отзыв
                $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)")
                   ->execute([$product_id, $user_id, $rating, $comment]);
                $success = "Спасибо за ваш отзыв!";
            }
        } catch (PDOException $e) {
            $error = "Ошибка базы данных: " . $e->getMessage();
        }
    } else {
        $error = "Комментарий не может быть пустым";
    }

    // Перезагружаем страницу без GET-параметра success/added
    header("Location: product.php?id=$product_id" . ($success ? "?review_success=1" : "") . ($error ? "?review_error=1" : ""));
    exit();
}

// Загрузка отзывов к товару
$reviews = [];
if ($product_id) {
    $stmt = $pdo->prepare("
        SELECT r.*, u.name as username 
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.product_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$product_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - MarketPlace</title>
    <style>
        :root {
            --primary: #FFFFFF;
            --secondary: #00E676; 
            --accent: #AA00FF; 
            --dark: #1A1A2E;
            --light: #F8F9FA;
            --text: #2D3748;
            --highlight: #FF4081; 
            --transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--text);
            background: var(--primary);
            line-height: 1.7;
            overflow-x: hidden;
        }

        h1, h2, h3, h4 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(170, 0, 255, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(170, 0, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(170, 0, 255, 0); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .header {
            background: linear-gradient(135deg, var(--accent), var(--secondary));
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 5px 25px rgba(170, 0, 255, 0.3);
            padding: 1.5rem 0;
        }

        .navigation-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: 900;
            color: white;
            text-decoration: none;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            letter-spacing: -1px;
        }

        .navigation {
            display: flex;
            gap: 2.5rem;
            list-style: none;
        }

        .navigation a {
            text-decoration: none;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            position: relative;
            transition: var(--transition);
            padding: 0.5rem 0;
        }

        .navigation a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 3px;
            bottom: 0;
            left: 0;
            background-color: var(--secondary);
            transition: var(--transition);
        }

        .navigation a:hover {
            color: var(--secondary);
            transform: translateY(-3px);
        }

        .navigation a:hover:after {
            width: 100%;
        }

        .user-actions {
            display: flex;
            gap: 1.5rem;
        }

        .user-actions a {
            padding: 0.7rem 1.5rem;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: var(--transition);
            font-size: 1rem;
        }

        .user-actions a:first-child {
            color: white;
            border: 2px solid white;
        }

        .user-actions a:last-child {
            background: white;
            color: var(--accent);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        .user-actions a:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .product-details {
            padding: 6rem 0;
            position: relative;
        }

        .product-details .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .product-image {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .product-image:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .product-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: var(--transition);
        }

        .product-image:hover img {
            transform: scale(1.03);
        }

        .product-info h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--accent);
            position: relative;
            display: inline-block;
        }

        .product-info h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--secondary));
            border-radius: 2px;
        }

        .price {
            font-size: 2rem;
            font-weight: 900;
            color: var(--secondary);
            margin: 1.5rem 0;
            display: flex;
            align-items: center;
        }

        .price:before {
            content: '₽';
            margin-right: 5px;
            font-size: 1.5rem;
        }

        .description {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2.5rem;
            color: var(--text);
        }

        .add-to-cart {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1.2rem 3rem;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.2rem;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 5px 20px rgba(255, 64, 129, 0.4);
            position: relative;
            overflow: hidden;
            z-index: 1;
            animation: pulse 2s infinite;
        }

        .add-to-cart:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--accent), var(--secondary));
            z-index: -1;
            opacity: 0;
            transition: var(--transition);
        }

        .add-to-cart:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 30px rgba(255, 64, 129, 0.6);
        }

        .add-to-cart:hover:before {
            opacity: 1;
        }

        .add-to-cart i {
            margin-left: 10px;
        }

        .added-to-cart {
            background: var(--secondary) !important;
        }

        .footer {
            background: var(--dark);
            color: white;
            padding: 5rem 0 3rem;
            text-align: center;
            position: relative;
        }

        .footer:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
        }

        .footer p {
            opacity: 0.8;
            font-size: 1.1rem;
        }
        .add-review {
            margin-top: 3rem;
            background: var(--glass-white);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Блок отзывов */
        .reviews-section {
            margin-top: 3rem;
        }

        .reviews-title {
            font-size: 1.5rem;
            color:rgb(152, 7, 255);
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .reviews-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, #AA00FF, #00E676);
        }

        .reviews-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .review-card {
            background: linear-gradient(90deg,rgba(170, 0, 255, 0.7),rgba(0, 230, 119, 0.7));
            padding: 1.5rem;
            border-radius: 15px;
            border: 1px solid var(--glass-border);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .review-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar i {
            font-size: 20px;
            color: white;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 50%;
        }

        .author-name {
            font-weight: bold;
            color: white;
        }

        .review-rating i {
            color: #AA00FF;
        }

        .review-date {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .review-content p {
            margin: 0;
            color: var(--text-white);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .cart-pulse {
            animation: pulse 0.5s ease;
        }
        .rating-stars {
            display: flex;
            gap: 0.5rem;
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.4);
            margin-bottom: 1rem;
        }

        .rating-stars i {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .rating-stars i.active,
        .rating-stars i:hover {
            color: #FFC107;
            transform: scale(1.2);
        }
        @media (max-width: 576px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .product-card img {
                height: 180px;
            }
        }

        @media (max-width: 992px) {
            .product-details .container {
                gap: 2rem;
            }
            
            .product-info h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .product-details .container {
                grid-template-columns: 1fr;
                gap: 3rem;
            }
            
            .product-info h2 {
                font-size: 1.8rem;
            }
            
            .price {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            .product-details {
                padding: 4rem 0;
            }
            
            .product-info h2 {
                font-size: 1.6rem;
            }
            
            .add-to-cart {
                width: 100%;
                padding: 1rem 2rem;
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <section class="navigation-section">
                <a href="index.php" class="logo">MarketPlace</a>
                <div class="navigation-container">
                    <nav aria-label="Navigation">
                        <ol class="navigation">
                            <li><a href="catalog.php">Каталог</a></li>
                            <li><a href="about-us.php">О компании</a></li>
                            <li><a href="contacts.php">Контакты</a></li>
                        </ol>
                    </nav>
                    <div class="user-actions">
                        <a href="login.php">Войти</a>
                        <a href="register.php">Зарегистрироваться</a>
                    </div>
                </div>
            </section>
        </div>
    </header>
    
    <section class="product-details">
        <div class="container">
            <div class="product-image">
            <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-info">
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <p class="price"><?= number_format($product['price'], 0, '', ' ') ?></p>
                <p class="description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                <form method="POST" action="">
                    <button type="submit" name="add_to_cart" class="add-to-cart <?= isset($_GET['added']) ? 'added-to-cart' : '' ?>">
                        <?= isset($_GET['added']) ? 'Добавлено! <i class="fas fa-check"></i>' : 'В корзину <i class="fas fa-shopping-cart"></i>' ?>
                    </button>
                </form>
                <div class="product-actions">
                    <form method="POST" action="" class="buy-now-form">
                        <button type="submit" name="buy_now" class="buy-now-btn">
                            Купить сейчас <i class="fas fa-bolt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="reviews-section">
        <h3 class="reviews-title">Отзывы покупателей</h3>

        <?php if (empty($reviews)): ?>
            <p>Нет отзывов для этого товара.</p>
        <?php else: ?>
            <div class="reviews-container">
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-author">
                                <div class="author-avatar"><i class="fas fa-user"></i></div>
                                <div class="author-info">
                                    <span class="author-name"><?= htmlspecialchars($review['username']) ?></span>
                                    <div class="review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="<?= $i <= $review['rating'] ? 'fas fa-star' : 'far fa-star' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <span class="review-date"><?= date('d.m.Y', strtotime($review['created_at'])) ?></span>
                        </div>
                        <div class="review-content">
                            <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>        

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="add-review">
            <h3>Оставить отзыв</h3>

            <?php if (!empty($error)): ?>
                <p style="color: red;"><?= $error ?></p>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <p style="color: #00E676;"><?= $success ?></p>
            <?php endif; ?>

            <form method="post" class="review-form" action="">
                <label for="rating">Ваша оценка:</label>
                <select name="rating" id="rating" required>
                    <option value="">Выберите оценку</option>
                    <option value="1">1 звезда</option>
                    <option value="2">2 звезды</option>
                    <option value="3">3 звезды</option>
                    <option value="4">4 звезды</option>
                    <option value="5">5 звёзд</option>
                </select>

                <label for="comment">Ваш отзыв:</label>
                <textarea name="comment" id="comment" rows="4" placeholder="Напишите ваш отзыв..."></textarea>

                <button type="submit" name="add_review" class="submit-review-btn">Добавить отзыв</button>
            </form>
        </div>
    <?php else: ?>
        <p style="margin: 2rem;">Чтобы оставить отзыв, <a href="login.php">войдите в аккаунт</a>.</p>
    <?php endif; ?>        


    <div class="cart-icon-container">
        <a href="cart.php" class="cart-icon-link">
            <svg class="cart-icon" viewBox="0 0 24 24">
                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
            </svg>
            <span class="cart-count"><?= !empty($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?></span>
        </a>
    </div>

    <style>
        .cart-icon-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .cart-icon-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #AA00FF, #00E676);
            border-radius: 50%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .cart-icon-link:hover {
            transform: scale(1.1) translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        .cart-icon {
            width: 24px;
            height: 24px;
            fill: white;
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: white;
            color: #AA00FF;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        /* Анимация при добавлении товара */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .cart-pulse {
            animation: pulse 0.5s ease;
        }

        .floating-cart-button {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background-color: #AA00FF;
        color: white !important;
        padding: 1rem;
        border-radius: 50px;
        font-size: 1.5rem;
        text-decoration: none;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        box-shadow: 0 8px 20px rgba(170, 0, 255, 0.3);
        transition: all 0.3s ease;
    }

    .floating-cart-button:hover {
        background-color: #9900E0;
        transform: scale(1.1);
    }

    .floating-cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #FFC107;
        color: black;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 12px;
        font-weight: bold;
        pointer-events: none;
    }
    </style>
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

        // Вызываем при загрузке страницы
        window.addEventListener('DOMContentLoaded', updateCartCount);

        // Если используешь кнопки +/- или удаление через JS
        document.querySelectorAll('.quantity-btn, .cart-item-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                setTimeout(updateCartCount, 500); // Обновляем через полсекунды после отправки формы
            });
        });

        // Если форма отправляется напрямую (POST)
        document.querySelector('form')?.addEventListener('submit', () => {
            setTimeout(updateCartCount, 800);
        });
    </script>

    <script>
        function applyFilters() {
            const category = document.querySelectorAll('.filter-select')[1].value;
            const sort = document.querySelectorAll('.filter-select')[0].value;

            let url = 'catalog.php';
            const params = [];

            if (category) params.push('category=' + encodeURIComponent(category));
            if (sort) params.push('sort=' + encodeURIComponent(sort));

            window.location.href = url + '?' + params.join('&');
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stars = document.querySelectorAll('.rating-stars i');
            const ratingInput = document.getElementById('rating-input');

            let selectedRating = 0;

            stars.forEach(star => {
                star.addEventListener('click', () => {
                    selectedRating = parseInt(star.getAttribute('data-rating'));
                    ratingInput.value = selectedRating;

                    // Обновляем внешний вид звёздочек
                    stars.forEach((s, index) => {
                        s.classList.remove('fas', 'active');
                        s.classList.add('far');
                        if (index < selectedRating) {
                            s.classList.remove('far');
                            s.classList.add('fas', 'active');
                        }
                    });
                });

                // Подсветка при наведении
                star.addEventListener('mouseover', () => {
                    const hoverRating = parseInt(star.getAttribute('data-rating'));
                    stars.forEach((s, index) => {
                        if (index < hoverRating) {
                            s.classList.add('active');
                        }
                    });
                });

                star.addEventListener('mouseout', () => {
                    stars.forEach(s => s.classList.remove('active'));
                });
            });
        });
    </script>

    <script>
        function updateFloatingCartCount() {
            fetch('get_cart_count.php')
                .then(res => res.json())
                .then(data => {
                    const countEl = document.querySelector('.floating-cart-count');
                    if (countEl) {
                        countEl.textContent = data.count;
                    }
                });
        }

        window.addEventListener('DOMContentLoaded', updateFloatingCartCount);
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', () => {
                setTimeout(updateFloatingCartCount, 800);
            });
        });
    </script>

</body>
</html>