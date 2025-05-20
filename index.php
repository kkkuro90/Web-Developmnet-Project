<?php
session_start();
require_once 'config.php';

$stmt = $pdo->query("SELECT id, name, price, image, description, category FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketPlace - Яркий маркетплейс</title>
    <link rel="stylesheet" href="styles_index.css">
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
                        <a href="login.php" class="btn-login">Войти</a>
                        <a href="register.php" class="btn-register">Зарегистрироваться</a>
                    </div>
                </div>
            </section>
        </div>
    </header>

    <section id="popular-products" class="product-catalog">
        <div class="container">
            <h2 class="animate-on-scroll">Популярные товары</h2>
            <div class="products-grid">
                <?php foreach ($products as $index => $product): ?>
                    <div class="product-card animate-on-scroll" style="transition-delay: <?= 0.1 + $index * 0.1 ?>s">
                    <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div>
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="price"><?= number_format($product['price'], 0, '', ' ') ?> ₽</p>
                            <a class="btn-posnawr" href="product.php?id=<?= $product['id'] ?>">Подробнее <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="current-promotion" class="promotion-section">
        <div class="container">
            <h2 class="animate-on-scroll">Специальное предложение</h2>
            <div class="promotion-card animate-on-scroll">
                <img src="uploads/смартфон.jpg" alt="Акционный товар">
                <div class="promotion-info">
                    <h3>Мега распродажа! </h3>
                    <p>Смартфон Note30 Ultra Pro — только сейчас!</p>
                    <p> Выгода до 10 000 ₽ и подарок при покупке! Акция действует только до конца этой недели. Успейте купить по лучшей цене!</p>
                    <a href="product.php?id=4" class="buy-now">Купить со скидкой <i class="fas fa-gift"></i></a>
                </div>
            </div>
        </div>
    </section>

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

    <footer class="footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> MarketPlace. Все права защищены.</p>
        </div>
    </footer>

    <script>
        let currentIndex = 0;
        const totalToShow = 3;

        function loadProducts() {
            fetch('get_products.php')
                .then(response => response.json())
                .then(products => {
                    const grid = document.querySelector('.products-grid');
                    grid.innerHTML = '';

                    const chunk = products.slice(currentIndex, currentIndex + totalToShow);

                    chunk.forEach((product, index) => {
                        const card = document.createElement('div');
                        card.className = 'product-card animate-on-scroll';
                        card.style.transitionDelay = `${0.1 + index * 0.1}s`;
                        card.innerHTML = `
                            <img src="${product.image}" alt="${product.name}">
                            <div>
                                <h3>${product.name}</h3>
                                <p class="price">${Number(product.price).toLocaleString('ru-RU')} ₽</p>
                                <a class="btn-posnawr" href="product.php?id=${product.id}">В корзину <i class="fas fa-arrow-right"></i></a>
                            </div>
                        `;
                        grid.appendChild(card);
                    });

                    currentIndex = (currentIndex + totalToShow) % products.length;
                })
                .catch(err => console.error('Ошибка загрузки товаров:', err));
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadProducts();
            setInterval(loadProducts, 5000);
        });
    </script>

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
    function updateAuthButtons() {
        fetch('check_auth.php')
            .then(response => response.json())
            .then(data => {
                const userActions = document.querySelector('.user-actions');
                if (data.isAuthenticated) {
                    let buttons = `<a href="profile.php" class="btn-login">Профиль (${data.username})</a>`;
                    if (data.role === 'admin') {
                        buttons += `<a href="admin.php" class="btn-register">Админ панель</a>`;
                    }
                    buttons += `<a href="logout.php" class="btn-register">Выйти</a>`;
                    userActions.innerHTML = buttons;
                } else {
                    userActions.innerHTML = `
                        <a href="login.php" class="btn-login">Войти</a>
                        <a href="register.php" class="btn-register">Зарегистрироваться</a>
                    `;
                }
            })
            .catch(error => console.error('Ошибка:', error));
    }

    // Обновляем кнопки при загрузке страницы
    document.addEventListener('DOMContentLoaded', updateAuthButtons);
    </script>
    
</body>
</html>