<?php
session_start();
require_once 'config.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    die("Неверный ID товара");
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Товар не найден");
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {

    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);

    if ($stmt->rowCount() > 0) {

        $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?")
           ->execute([$user_id, $product_id]);
    } else {

        $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)")
           ->execute([$user_id, $product_id]);
    }

    header("Location: product.php?id=$product_id&added=1");
    exit();
}

$error = $success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5) {
        $error = "Пожалуйста, выберите рейтинг от 1 до 5";
    } elseif (empty($comment)) {
        $error = "Комментарий не может быть пустым";
    } else {
        try {

            $checkStmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? AND user_id = ?");
            $checkStmt->execute([$product_id, $user_id]);

            if ($checkStmt->rowCount() > 0) {

                $pdo->prepare("UPDATE reviews SET rating = ?, comment = ?, created_at = NOW() WHERE product_id = ? AND user_id = ?")
                   ->execute([$rating, $comment, $product_id, $user_id]);
                $success = "Отзыв успешно обновлён!";
            } else {

                $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)")
                   ->execute([$product_id, $user_id, $rating, $comment]);
                $success = "Спасибо за ваш отзыв!";
            }
        } catch (PDOException $e) {
            $error = "Ошибка базы данных: " . $e->getMessage();
        }
    }

    header("Location: product.php?id=$product_id" . ($success ? "&review_success=1" : "") . ($error ? "&review_error=1" : ""));
    exit();
}

$reviews = [];
$stmt = $pdo->prepare("
    SELECT r.*, u.name AS username 
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.product_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$product_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> - MarketPlace</title>
    <link rel="stylesheet" href="styles_product.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <section class="navigation-section">
                <a href="catalog.php" class="logo">MarketPlace</a>
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

    <div class="container">
        <div class="product-details">
            <div class="product-image">
            <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>

            <div class="product-info">
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                <p>Цена: <?= number_format($product['price'], 0, '', ' ') ?> ₽</p>

                <form method="post" action="">
                    <button type="submit" name="add_to_cart" class="add-to-cart<?= isset($_GET['added']) ? ' added-to-cart' : '' ?>">
                        <?= isset($_GET['added']) ? 'Добавлено! <i class="fas fa-check"></i>' : 'В корзину <i class="fas fa-shopping-cart"></i>' ?>
                    </button>
                </form>

                <div class="product-actions">
                <form method="POST" action="checkout.php" class="buy-now-form">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <button type="submit" name="buy_now" class="buy-now-btn">Купить сейчас <i class="fas fa-bolt"></i></button>
</form>
                </div>
            </div>
        </div>
    </div>
    <section class="reviews-section">
        <h3 class="reviews-title">Отзывы покупателей</h3>

        <?php if (empty($reviews)): ?>
            <p>Нет отзывов для этого товара.</p>
        <?php else: ?>
            <div class="reviews-container">
    <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $review): ?>
            <div class="review-card">
                <div class="review-header">
                    <div class="review-author">
                        <div class="author-avatar"><i class="fas fa-user"></i></div>
                        <div class="author-info">
                            <span class="author-name"><?= htmlspecialchars($review['username']) ?></span>
                            <div class="review-rating">
                                <?php for ($i = 1; $i <= $review['rating']; $i++): ?>
                                    <span>⭐</span>
                                <?php endfor; ?>
                                <?php for ($i = 1; $i <= 5 - $review['rating']; $i++): ?>
                                    <span>☆</span>
                                <?php endfor; ?>
                                <span class="rating-value"><?= $review['rating'] ?>/5</span>
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
    <?php else: ?>
        <p>Нет отзывов для этого товара.</p>
    <?php endif; ?>
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
                    <option value="1">1 ⭐</option>
                    <option value="2">2 ⭐</option>
                    <option value="3">3 ⭐</option>
                    <option value="4">4 ⭐</option>
                    <option value="5">5 ⭐</option>
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

                    stars.forEach((s, index) => {
                        s.classList.remove('fas', 'active');
                        s.classList.add('far');
                        if (index < selectedRating) {
                            s.classList.remove('far');
                            s.classList.add('fas', 'active');
                        }
                    });
                });

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