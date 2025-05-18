<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Товар - MarketPlace</title>
    <link rel="stylesheet" href="styles_contacts.css">     
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

    <footer class="footer">
        <div class="container">
            <p>© 2025 MarketPlace. Все права защищены.</p>
        </div>
    </footer>

    <script>
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