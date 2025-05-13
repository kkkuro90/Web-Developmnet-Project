<?php
session_start();

$db_host = 'localhost';
$db_name = 'marketplace_db';
$db_user = 'root';
$db_pass = '';

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $product = [
        'name' => 'Товар не найден',
        'price' => 0,
        'description' => 'Извините, запрашиваемый товар не найден в нашем каталоге.',
        'image' => 'default-product.jpg'
    ];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$product_id] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => 1,
            'image' => $product['image']
        ];
    }
    
    header("Location: product.php?id=$product_id&added=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - MarketPlace</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
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
    
    <footer class="footer">
        <div class="container">
            <p>© <?= date('Y') ?> MarketPlace. Все права защищены.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.product-image, .product-info');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });

            elements.forEach(element => {
                element.style.opacity = 0;
                element.style.transform = 'translateY(30px)';
                element.style.transition = 'all 0.8s ease-out';
                observer.observe(element);
            });

            const addToCartBtn = document.querySelector('.add-to-cart');
            if (addToCartBtn.classList.contains('added-to-cart')) {
                setTimeout(() => {
                    addToCartBtn.classList.remove('added-to-cart');
                    addToCartBtn.innerHTML = 'В корзину <i class="fas fa-shopping-cart"></i>';
                }, 2000);
            }
        });
    </script>

    <div class="reviews-section">
        <h3 class="reviews-title">Отзывы о товаре</h3>
        
        <div class="reviews-container">
            <div class="review-card">
                <div class="review-header">
                    <div class="review-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <span class="author-name">Александр Петров</span>
                            <div class="review-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <span class="review-date">15.05.2023</span>
                </div>
                <div class="review-content">
                    <p>Отличный товар, полностью соответствует описанию. Качество на высоте, доставка быстрая. Рекомендую!</p>
                </div>
            </div>
            
            <div class="review-card">
                <div class="review-header">
                    <div class="review-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <span class="author-name">Мария Иванова</span>
                            <div class="review-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <span class="review-date">10.05.2023</span>
                </div>
                <div class="review-content">
                    <p>Хороший товар, но есть небольшие недочеты. В целом довольна покупкой, но ожидала немного лучшего качества.</p>
                </div>
            </div>
        </div>
        
        <div class="add-review">
            <h4>Оставить отзыв</h4>
            <form class="review-form">
                <div class="form-group">
                    <label for="review-name">Ваше имя</label>
                    <input type="text" id="review-name" required>
                </div>
                <div class="form-group">
                    <label>Ваша оценка</label>
                    <div class="rating-stars">
                        <i class="far fa-star" data-rating="1"></i>
                        <i class="far fa-star" data-rating="2"></i>
                        <i class="far fa-star" data-rating="3"></i>
                        <i class="far fa-star" data-rating="4"></i>
                        <i class="far fa-star" data-rating="5"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="review-text">Ваш отзыв</label>
                    <textarea id="review-text" rows="4" required></textarea>
                </div>
                <button type="submit" class="submit-review-btn">Отправить отзыв</button>
            </form>
        </div>
    </div>

    <style>
        .product-actions {
            display: flex;
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .buy-now-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1.2rem 3rem;
            background: var(--secondary);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.2rem;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 5px 20px rgba(0, 230, 118, 0.4);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .buy-now-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--secondary), var(--accent));
            z-index: -1;
            opacity: 0;
            transition: var(--transition);
        }
        
        .buy-now-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 230, 118, 0.6);
        }
        
        .buy-now-btn:hover:before {
            opacity: 1;
        }
        
        .buy-now-btn i {
            margin-left: 10px;
        }
        
        /* Стили для блока отзывов */
        .reviews-section {
            margin-top: 4rem;
            padding-top: 3rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .reviews-title {
            font-size: 1.8rem;
            color: var(--accent);
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        .reviews-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--secondary));
            border-radius: 2px;
        }
        
        .review-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }
        
        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
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
        
        .author-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
        }
        
        .author-name {
            font-weight: 600;
            color: var(--dark);
        }
        
        .review-rating {
            color: #FFC107;
            font-size: 0.9rem;
            margin-top: 0.2rem;
        }
        
        .review-date {
            font-size: 0.9rem;
            color: rgba(0, 0, 0, 0.5);
        }
        
        .review-content p {
            line-height: 1.6;
        }
        
        /* Форма добавления отзыва */
        .add-review {
            margin-top: 3rem;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .add-review h4 {
            font-size: 1.5rem;
            color: var(--accent);
            margin-bottom: 1.5rem;
        }
        
        .review-form .form-group {
            margin-bottom: 1.5rem;
        }
        
        .review-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .review-form input,
        .review-form textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .review-form input:focus,
        .review-form textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(170, 0, 255, 0.1);
        }
        
        .review-form textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .rating-stars {
            display: flex;
            gap: 0.5rem;
            font-size: 1.5rem;
            color: #ddd;
        }
        
        .rating-stars i {
            cursor: pointer;
            transition: var(--transition);
        }
        
        .rating-stars i:hover {
            color: #FFC107;
            transform: scale(1.2);
        }
        
        .rating-stars i.active {
            color: #FFC107;
        }
        
        .submit-review-btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .submit-review-btn:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(170, 0, 255, 0.3);
        }
        
        @media (max-width: 768px) {
            .product-actions {
                flex-direction: column;
            }
            
            .buy-now-btn,
            .add-to-cart {
                width: 100%;
            }
        }
    </style>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.rating-stars i');
            let selectedRating = 0;
            
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    selectedRating = rating;
                    
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.remove('far');
                            s.classList.add('fas');
                            s.classList.add('active');
                        } else {
                            s.classList.remove('fas');
                            s.classList.add('far');
                            s.classList.remove('active');
                        }
                    });
                });
                
                star.addEventListener('mouseover', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('hover');
                        } else {
                            s.classList.remove('hover');
                        }
                    });
                });
                
                star.addEventListener('mouseout', function() {
                    stars.forEach(s => {
                        s.classList.remove('hover');
                    });
                });
            });
            
            const reviewForm = document.querySelector('.review-form');
            if (reviewForm) {
                reviewForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (selectedRating === 0) {
                        alert('Пожалуйста, поставьте оценку');
                        return;
                    }
                    
                    alert('Спасибо за ваш отзыв!');
                    this.reset();
                    
                    stars.forEach(s => {
                        s.classList.remove('fas');
                        s.classList.add('far');
                        s.classList.remove('active');
                    });
                    selectedRating = 0;
                });
            }
        });
    </script>

</body>
</html>