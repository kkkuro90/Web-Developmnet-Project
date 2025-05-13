<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О компании - MarketPlace </title>
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

        /* About Us Section */
        .about-section {
            padding: 6rem 0;
            background: var(--light);
        }

        .about-section h2 {
            font-size: 2.8rem;
            margin-bottom: 4rem;
            text-align: center;
            position: relative;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: -1px;
        }

        .about-section h2:after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--secondary));
            border-radius: 2px;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .about-image {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .about-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: var(--transition);
        }

        .about-image:hover img {
            transform: scale(1.03);
        }

        .about-text h3 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--accent);
        }

        .about-text p {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .feature-item {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            text-align: center;
        }

        .feature-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-item i {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--accent), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .feature-item h4 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        /* Team Section */
        .team-section {
            padding: 6rem 0;
            background: white;
        }

        .team-section h2 {
            font-size: 2.8rem;
            margin-bottom: 4rem;
            text-align: center;
            position: relative;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: -1px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }

        .team-member {
            background: var(--light);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
            text-align: center;
        }

        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .team-member img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .team-member-info {
            padding: 2rem;
        }

        .team-member-info h4 {
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .team-member-info p {
            color: var(--accent);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--secondary));
            color: white;
            transition: var(--transition);
        }

        .social-links a:hover {
            transform: translateY(-5px) scale(1.1);
        }

        /* Footer */
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

        /* Responsive */
        @media (max-width: 992px) {
            .about-content {
                gap: 2rem;
            }
            
            .about-text h3 {
                font-size: 1.8rem;
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
            .about-section, .team-section {
                padding: 4rem 0;
            }
            
            .about-section h2, .team-section h2 {
                font-size: 1.8rem;
                margin-bottom: 3rem;
            }
            
            .navigation {
                gap: 1.5rem;
            }
            
            .user-actions {
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
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

    <!-- About Us Section -->
    <section class="about-section">
        <div class="container">
            <h2>О нашей компании</h2>
            <div class="about-content">
                <div class="about-text">
                    <h3>MarketPlace - лидер в сфере электротехники</h3>
                    <p>Мы - современная компания, специализирующаяся на поставках электротехнического оборудования и компонентов. Основанная в 2010 году, наша компания быстро завоевала доверие клиентов благодаря высокому качеству продукции и профессиональному сервису.</p>
                    <p>Сегодня MarketPlace предлагает более 10,000 наименований продукции от ведущих мировых производителей. Наши склады расположены в 5 городах России, что позволяет нам оперативно осуществлять поставки в любой регион страны.</p>
                    <p>Мы постоянно расширяем ассортимент и внедряем новые технологии, чтобы предложить нашим клиентам самые современные и надежные решения в области электротехники.</p>
                </div>
                <div class="about-image">
                    <img src="uploads/" alt="Наш офис и склад">
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2>Наша команда</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="images/team1.jpg" alt="Иван Петров">
                    <div class="team-member-info">
                        <h4>Иван Петров</h4>
                        <p>Генеральный директор</p>
                    </div>
                </div>
                <div class="team-member">
                    <img src="images/team2.jpg" alt="Елена Смирнова">
                    <div class="team-member-info">
                        <h4>Елена Смирнова</h4>
                        <p>Технический директор</p>
                    </div>
                </div>
                <div class="team-member">
                    <img src="images/team3.jpg" alt="Алексей Козлов">
                    <div class="team-member-info">
                        <h4>Алексей Орлов</h4>
                        <p>Руководитель отдела продаж</p>
                    </div>
                </div>
                <div class="team-member">
                    <img src="images/team4.jpg" alt="Ольга Иванова">
                    <div class="team-member-info">
                        <h4>Ольга Иванова</h4>
                        <p>Главный инженер</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> MarketPlace. Все права защищены.</p>
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