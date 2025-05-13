<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketPlace - Яркий маркетплейс</title>
    <link rel="stylesheet" href="styles.css">
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

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
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
        }

        .navigation-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0;
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

        section {
            padding: 6rem 0;
            position: relative;
        }

        section:nth-child(even) {
            background: var(--light);
        }

        h2 {
            font-size: 2.8rem;
            margin-bottom: 4rem;
            text-align: center;
            position: relative;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: -1px;
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
            z-index: 1;
        }

        .product-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(170, 0, 255, 0.1), rgba(0, 230, 118, 0.1));
            z-index: -1;
            opacity: 0;
            transition: var(--transition);
        }

        .product-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .product-card:hover:before {
            opacity: 1;
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

        .product-card > div {
            padding: 1.8rem;
        }

        .product-card h3 {
            font-size: 1.3rem;
            margin-bottom: 0.8rem;
            color: var(--dark);
        }

        .price {
            font-weight: 700;
            color: var(--secondary);
            font-size: 1.5rem;
            margin: 1.2rem 0;
            display: flex;
            align-items: center;
        }

        .price:before {
            content: '₽';
            margin-right: 5px;
            font-size: 1.2rem;
        }

        /* Специальная акция */
        .promotion-section {
            background: linear-gradient(-45deg, #AA00FF, #00E676);
            color: white;
        }

        .promotion-card {
            display: flex;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .promotion-card img {
            width: 45%;
            object-fit: cover;
        }

        .promotion-info {
            padding: 3rem;
            width: 55%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .promotion-info h3 {
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
            color: white;
            line-height: 1.2;
        }

        .promotion-info p {
            margin-bottom: 2rem;
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Преимущества с иконками */
        .advantages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }

        .advantage-item {
            text-align: center;
            padding: 3rem 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .advantage-item:before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(170, 0, 255, 0.05), transparent);
            transform: rotate(45deg);
            z-index: -1;
            transition: var(--transition);
            opacity: 0;
        }

        .advantage-item:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .advantage-item:hover:before {
            animation: shine 1.5s;
        }

        @keyframes shine {
            0% { left: -50%; opacity: 0; }
            50% { opacity: 1; }
            100% { left: 150%; opacity: 0; }
        }

        .advantage-item i {
            font-size: 3.5rem;
            margin-bottom: 1.8rem;
            background: linear-gradient(135deg, var(--accent), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            transition: var(--transition);
        }

        .advantage-item:hover i {
            transform: scale(1.2);
        }

        .advantage-item h3 {
            margin-bottom: 1.2rem;
            font-size: 1.5rem;
            color: var(--accent);
        }

        /* Яркие кнопки */
        .btn-posnawr, .buy-now {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 2.2rem;
            background: var(--secondary);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: 0 5px 15px rgba(0, 230, 118, 0.4);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-posnawr:before, .buy-now:before {
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

        .btn-posnawr:hover, .buy-now:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 25px rgba(0, 230, 118, 0.6);
        }

        .btn-posnawr:hover:before, .buy-now:hover:before {
            opacity: 1;
        }

        .buy-now {
            background: var(--secondary);
            box-shadow: 0 5px 15px rgba(255, 64, 129, 0.4);
            align-self: flex-start;
            margin-top: 1rem;
            padding: 1.2rem 3rem;
            font-size: 1.2rem;
            animation: pulse 2s infinite;
        }

        .buy-now:hover {
            box-shadow: 0 10px 25px rgba(255, 64, 129, 0.6);
        }

        /* Эффектный подвал */
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

        /* Адаптивность */
        @media (max-width: 992px) {
            .navigation {
                gap: 1.5rem;
            }
            
            h2 {
                font-size: 2.2rem;
            }
            
            .promotion-info {
                padding: 2rem;
            }
        }

        @media (max-width: 768px) {
            .navigation-section {
                flex-direction: column;
                gap: 1.5rem;
            }
            
            .navigation {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .user-actions {
                margin-top: 1rem;
            }
            
            .promotion-card {
                flex-direction: column;
            }
            
            .promotion-card img, 
            .promotion-info {
                width: 100%;
            }
            
            section {
                padding: 4rem 0;
            }
            
            h2 {
                font-size: 1.8rem;
                margin-bottom: 3rem;
            }
        }

        @media (max-width: 576px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .advantage-item {
                padding: 2rem 1.5rem;
            }
        }

        /* Мобильная версия (до 576px) */
    @media (max-width: 576px) {
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

        /* Секции */
        section {
            padding: 3rem 0;
        }

        h2 {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Карточки товаров */
        .products-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .product-card {
            border-radius: 15px;
        }

        .product-card > div {
            padding: 1.2rem;
        }

        /* Акции */
        .promotion-card {
            flex-direction: column;
        }

        .promotion-card img {
            width: 100%;
            height: 200px;
        }

        .promotion-info {
            width: 100%;
            padding: 1.5rem;
        }

        .promotion-info h3 {
            font-size: 1.5rem;
        }

        /* Преимущества */
        .advantages-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .advantage-item {
            padding: 1.5rem 1rem;
        }

        /* Кнопки */
        .btn-posnawr, .buy-now {
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            width: 100%;
        }

        .buy-now {
            margin-top: 0.5rem;
        }

        /* Подвал */
        .footer {
            padding: 3rem 0 2rem;
        }

        .footer p {
            font-size: 0.9rem;
        }
    }

    /* Дополнительные адаптации для очень маленьких экранов */
    @media (max-width: 400px) {
        :root {
            font-size: 13px;
        }

        .navigation {
            gap: 0.7rem;
        }

        .product-card img {
            height: 180px;
        }

        .promotion-info h3 {
            font-size: 1.3rem;
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
                        <a href="login.php" class="btn-login">Войти</a>
                        <a href="register.php" class="btn-register">Зарегистрироваться</a>
                    </div>
                </div>
            </section>
        </div>
    </header>

    <!-- Популярные товары -->
    <section id="popular-products" class="product-catalog">
        <div class="container">
            <h2 class="animate-on-scroll">Популярные товары</h2>
            <div class="products-grid">
                <div class="product-card animate-on-scroll" style="transition-delay: 0.1s">
                    <img src="uploads/часы.webp" alt="Товар 1">
                    <div>
                        <h3>Умные часы Pro X</h3>
                        <p class="price">12 999</p>
                        <a class="btn-posnawr" href="product.html">В корзину <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="product-card animate-on-scroll" style="transition-delay: 0.2s">
                    <img src="uploads/наушники.webp" alt="Товар 2">
                    <div>
                        <h3>Беспроводные наушники</h3>
                        <p class="price">5 499</p>
                        <a class="btn-posnawr" href="#">В корзину <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="product-card animate-on-scroll" style="transition-delay: 0.3s">
                    <img src="uploads/колонка.jpg" alt="Товар 3">
                    <div>
                        <h3>Портативная колонка</h3>
                        <p class="price">8 999</p>
                        <a class="btn-posnawr" href="#">В корзину <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Специальная акция -->
    <section id="current-promotion" class="promotion-section">
        <div class="container">
            <h2 class="animate-on-scroll">Специальное предложение</h2>
            <div class="promotion-card animate-on-scroll">
                <img src="uploads/смартфон.jpg" alt="Акционный товар">
                <div class="promotion-info">
                    <h3>Мега распродажа! </h3>
                    <p>Смартфон Note30 Ultra Pro — только сейчас!</p>
                    <p> Выгода до 10 000 ₽ и подарок при покупке! Акция действует только до конца этой недели. Успейте купить по лучшей цене!</p>
                    <button class="buy-now">Купить со скидкой <i class="fas fa-gift"></i></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Наши преимущества -->
    <section id="our-advantages" class="advantages-section">
        <div class="container">
            <h2 class="animate-on-scroll">Почему выбирают нас</h2>
            <div class="advantages-grid">
                <div class="advantage-item animate-on-scroll" style="transition-delay: 0.1s">
                    <i class="fas fa-rocket"></i>
                    <h3>Молниеносная доставка</h3>
                    <p>Доставка в день заказа по крупным городам</p>
                </div>
                <div class="advantage-item animate-on-scroll" style="transition-delay: 0.2s">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Гарантия 2 года</h3>
                    <p>Расширенная гарантия на все товары</p>
                </div>
                <div class="advantage-item animate-on-scroll" style="transition-delay: 0.3s">
                    <i class="fas fa-star"></i>
                    <h3>Премиум качество</h3>
                    <p>Только проверенные бренды и поставщики</p>
                </div>
                <div class="advantage-item animate-on-scroll" style="transition-delay: 0.4s">
                    <i class="fas fa-percent"></i>
                    <h3>Выгодные акции</h3>
                    <p>Регулярные скидки и специальные предложения</p>
                </div>
            </div>
        </div>
    </section>

    <!-- подвал -->
    <footer class="footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> MarketPlace. Все права защищены.</p>
        </div>
    </footer>

    <div class="cart-icon-container">
        <a href="basket.php" class="cart-icon-link">
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
        document.addEventListener('DOMContentLoaded', function() {
            const cartIcon = document.querySelector('.cart-icon-link');
            
            if (sessionStorage.getItem('cartUpdated') === 'true') {
                cartIcon.classList.add('cart-pulse');
                sessionStorage.removeItem('cartUpdated');
                
                setTimeout(() => {
                    cartIcon.classList.remove('cart-pulse');
                }, 500);
            }
        });
        
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