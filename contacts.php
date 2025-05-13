<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Товар - MarketPlace</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary: #FFFFFF;
            --secondary: #00E676;
            --accent: #AA00FF;
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
            line-height: 1.7;
            overflow-x: hidden;
        }

        h1, h2, h3, h4 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
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

        /* Анимации */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

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

        
        .contacts-section {
            padding: 6rem 0;
        }

        .page-title {
            font-size: 3rem;
            text-align: center;
            margin-bottom: 4rem;
            color: var(--accent);
            position: relative;
        }

        .page-title:after {
            content: '';
            display: block;
            width: 100px;
            height: 5px;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
            margin: 1.5rem auto 0;
            border-radius: 5px;
        }

        .contacts-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            margin-bottom: 5rem;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }

        .contact-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .contact-icon {
            width: 70px;
            height: 70px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .contact-text {
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .contact-text strong {
            display: block;
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--accent);
        }

        .map-container {
            margin: 5rem 0;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            height: 400px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
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
            background: linear-gradient(90deg, var(--secondary), var(--accent), var(--accent));
        }

        .footer-content {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer a {
            color: white;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer a:hover {
            color: var(--secondary);
        }

        /* Анимации при скролле */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .animate-on-scroll.animated {
            opacity: 1;
            transform: translateY(0);
        }

        /* Адаптивность */
        @media (max-width: 992px) {
            .page-title {
                font-size: 2.5rem;
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
            .page-title {
                font-size: 1.8rem;
            }
            
            .contact-icon {
                width: 60px;
                height: 60px;
            }
            
            .map-container {
                height: 300px;
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

    <main class="contacts-section">
        <div class="container">
            <h2 class="page-title animate-on-scroll">Наши контакты</h2>
            
            <ul class="contacts-list">
                
                <li class="contact-item animate-on-scroll" style="transition-delay: 0.2s">
                    <img src="uploads//календарь2.png" alt="Часы работы" class="contact-icon">
                    <div class="contact-text">
                        <strong>Часы работы</strong>
                        Ежедневно: с 9:00 до 21:00
                    </div>
                </li>
                
                <li class="contact-item animate-on-scroll" style="transition-delay: 0.3s">
                    <img src="uploads//телефон3.png" alt="Телефон" class="contact-icon">
                    <div class="contact-text">
                        <strong>Телефон</strong>
                        <a href="tel:+7123456789">+7 (123) 456-789</a>
                    </div>
                </li>
                
                <li class="contact-item animate-on-scroll" style="transition-delay: 0.4s">
                    <img src="uploads//почта1.png" alt="Email" class="contact-icon">
                    <div class="contact-text">
                        <strong>Email</strong>
                        <a href="mailto:MarketPlace@mail.ru">MarketPlace@mail.ru</a>
                    </div>
                </li>
            </ul>
            
            <h2 class="animate-on-scroll">Мы находимся здесь</h2>
            
            <div class="map-container animate-on-scroll">
                <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A3e9a53635f9057bccda332328b9ef791f0e93fcf52b8834c32eb6607ecc6f534&amp;width=100%25&amp;height=100%&amp;lang=ru_RU&amp;scroll=true"></script>
            </div>
        </div>
    </main>

    <!-- Подвал -->
    <footer class="footer">
        <div class="container">
            <p>© 2025 MarketPlace. Все права защищены.</p>
        </div>
    </footer>

    <script>
        // Анимация при скролле
        document.addEventListener('DOMContentLoaded', function() {
            const animateElements = document.querySelectorAll('.animate-on-scroll');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                    }
                });
            }, {
                threshold: 0.1
            });

            animateElements.forEach(element => {
                observer.observe(element);
            });
        });
    </script>

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