<?php
require_once 'config.php'; 

$stmt = $pdo->query("SELECT id, name, price, image, description, category FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О компании - MarketPlace </title>
    <link rel="stylesheet" href="styles_about-us.css">
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
                    <img src="uploads/компания.jpg" alt="Наш офис и склад">
                </div>
            </div>
        </div>
    </section>

    <section class="team-section">
        <div class="container">
            <h2>Наша команда</h2>
            <div class="team-grid">
                <div class="team-member">
                    <div class="team-member-info">
                        <h4>Иван Петров</h4>
                        <p>Генеральный директор</p>
                    </div>
                </div>
                <div class="team-member">
                    <div class="team-member-info">
                        <h4>Елена Смирнова</h4>
                        <p>Технический директор</p>
                    </div>
                </div>
                <div class="team-member">
                    <div class="team-member-info">
                        <h4>Алексей Орлов</h4>
                        <p>Руководитель отдела продаж</p>
                    </div>
                </div>
                <div class="team-member">
                    <div class="team-member-info">
                        <h4>Ольга Иванова</h4>
                        <p>Главный инженер</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
</body>
</html>