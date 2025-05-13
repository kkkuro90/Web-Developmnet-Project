<?php

$products = [
    [
        'id' => 1,
        'name' => 'Умные часы Pro X',
        'price' => 12999,
        'image' => 'uploads/часы.webp',
        'description' => 'Инновационные умные часы с расширенными функциями мониторинга здоровья'
    ],
    [
        'id' => 2,
        'name' => 'Беспроводные наушники',
        'price' => 5499,
        'image' => 'uploads/наушники.webp',
        'description' => 'Наушники с шумоподавлением и 30-часовым временем работы'
    ],
    [
        'id' => 3,
        'name' => 'Портативная колонка',
        'price' => 8999,
        'image' => 'uploads/колонка.jpg',
        'description' => 'Мощная колонка с защитой от воды и 20-часовой работой'
    ],
    [
        'id' => 4,
        'name' => 'Смартфон Note30 Ultra Pro',
        'price' => 34999,
        'image' => 'uploads/смартфон.jpg',
        'description' => 'Флагманский смартфон с лучшей камерой на рынке'
    ],
    [
        'id' => 5,
        'name' => 'Фитнес-браслет',
        'price' => 2999,
        'image' => 'uploads/фитнес_браслет.jpg',
        'description' => 'Трекер активности с мониторингом сна и пульса'
    ],
    [
        'id' => 6,
        'name' => 'Электронная книга',
        'price' => 6999,
        'image' => 'uploads/книга.webp',
        'description' => 'Читалка с экраном E-Ink и подсветкой'
    ]
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог товаров - MarketPlace</title>
    <style>
        /* Яркая цветовая палитра */
        :root {
            --primary: #FFFFFF;
            --secondary: #00E676; /* Яркий зелёный */
            --accent: #AA00FF;   /* Неоновый фиолетовый */
            --dark: #1A1A2E;
            --light: #F8F9FA;
            --text: #2D3748;
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
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3, h4 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        /* Анимации */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Яркая шапка с градиентом */
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

        /* Каталог товаров */
        .catalog-section {
            padding: 6rem 0;
        }

        .section-title {
            font-size: 2.8rem;
            margin-bottom: 4rem;
            text-align: center;
            color: var(--accent);
            position: relative;
        }

        .section-title:after {
            content: '';
            display: block;
            width: 100px;
            height: 5px;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
            margin: 1.5rem auto 0;
            border-radius: 5px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2.5rem;
        }

        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .product-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: var(--transition);
        }

        .product-card:hover img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-name {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .product-description {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .product-price {
            font-weight: 700;
            color: var(--secondary);
            font-size: 1.5rem;
            margin: 1rem 0;
            display: flex;
            align-items: center;
        }

        .product-price:before {
            content: '₽';
            margin-right: 5px;
            font-size: 1.2rem;
        }

        .btn-add-to-cart {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem 1.5rem;
            background: var(--secondary);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
            text-decoration: none;
        }

        .btn-add-to-cart:hover {
            background: #00C853;
            transform: translateY(-2px);
        }

        /* Фильтры */
        .filters {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .filter-group {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .filter-label {
            font-weight: 600;
        }

        .filter-select {
            padding: 0.5rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            transition: var(--transition);
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--accent);
        }

        /* Подвал */
        .footer {
            background: var(--dark);
            color: white;
            padding: 3rem 0;
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

        /* Адаптивность */
        @media (max-width: 992px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }
            
            .section-title {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 768px) {
        
        :root {
            font-size: 14px; /* Уменьшаем базовый размер шрифта */
        }

        /* Шапка */
        .navigation-section {
            flex-direction: column;
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .navigation {
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .navigation a {
            font-size: 0.9rem;
        }

        .user-actions {
            flex-direction: column;
            gap: 0.8rem;
            width: 100%;
        }

        .user-actions a {
            width: 100%;
            text-align: center;
            padding: 0.6rem;
        }
    }

        @media (max-width: 576px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .product-card img {
                height: 180px;
            }
        }
    </style>
</head>
<body>

    <!-- Яркая шапка -->
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

    <section class="catalog-section">
        <div class="container">
            <h2 class="section-title">Каталог товаров</h2>
            
            <div class="filters">
                <div class="filter-group">
                    <span class="filter-label">Сортировка:</span>
                    <select class="filter-select">
                        <option>По популярности</option>
                        <option>По возрастанию цены</option>
                        <option>По убыванию цены</option>
                        <option>По новизне</option>
                    </select>
                </div>
                <div class="filter-group">
                    <span class="filter-label">Категория:</span>
                    <select class="filter-select">
                        <option>Все товары</option>
                        <option>Электроника</option>
                        <option>Аксессуары</option>
                        <option>Гаджеты</option>
                    </select>
                </div>
            </div>
            
            <!-- Сетка товаров -->
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <div class="product-info">
                        <h3 class="product-name"><?php echo $product['name']; ?></h3>
                        <p class="product-description"><?php echo $product['description']; ?></p>
                        <div class="product-price"><?php echo number_format($product['price'], 0, '', ' '); ?></div>
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn-add-to-cart">
                            Подробнее
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Подвал -->
    <footer class="footer">
        <div class="container">
            <p>© 2023 MarketPlace. Все права защищены.</p>
        </div>
    </footer>

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
    </style>

    <script>
        // Добавляем анимацию при изменении количества товаров
        document.addEventListener('DOMContentLoaded', function() {
            const cartIcon = document.querySelector('.cart-icon-link');
            
            // Проверяем, было ли изменение корзины (можно установить через sessionStorage)
            if (sessionStorage.getItem('cartUpdated') === 'true') {
                cartIcon.classList.add('cart-pulse');
                sessionStorage.removeItem('cartUpdated');
                
                // Удаляем класс после анимации
                setTimeout(() => {
                    cartIcon.classList.remove('cart-pulse');
                }, 500);
            }
        });
        
        // Для обновления счетчика без перезагрузки страницы можно использовать:
        function updateCartCount(count) {
            document.querySelector('.cart-count').textContent = count;
            document.querySelector('.cart-icon-link').classList.add('cart-pulse');
            setTimeout(() => {
                document.querySelector('.cart-icon-link').classList.remove('cart-pulse');
            }, 500);
        }
    </script>
</body>
</html>