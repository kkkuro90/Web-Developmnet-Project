<?php
session_start();
require_once 'config.php'; 

$stmt = $pdo->query("SELECT id, name, price, image, description, category FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
require_once 'config.php';

$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? '';

$query = "SELECT * FROM products WHERE 1";
$params = [];

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
}

switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'newest':
        $query .= " ORDER BY created_at DESC";
        break;
    default:
        $query .= " ORDER BY id DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = $pdo->query("SELECT DISTINCT category FROM products")->fetchAll(PDO::FETCH_COLUMN);
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог товаров - MarketPlace</title>
    <link rel="stylesheet" href="styles_catalog.css">
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

    <section class="catalog-section">
        <div class="container">
            <h2 class="section-title">Каталог товаров</h2>
            
            <div class="filters">
                <div class="filter-group">
                    <span class="filter-label">Сортировка:</span>
                    <select class="filter-select" onchange="applyFilters()">
                        <option value="">По умолчанию</option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>По возрастанию цены</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>По убыванию цены</option>
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Новые товары</option>
                    </select>
                </div>
                <div class="filter-group">
                    <span class="filter-label">Категория:</span>
                    <select class="filter-select" onchange="applyFilters()">
                        <option value="">Все категории</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
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

    <div class="cart-icon-container">
        <a href="cart.php" class="cart-icon-link">
            <svg class="cart-icon" viewBox="0 0 24 24">
                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
            </svg>
            <span class="cart-count"><?= !empty($_SESSION['catalog']) ? array_sum(array_column($_SESSION['catalog'], 'quantity')) : 0 ?></span>
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

    <footer class="footer">
        <div class="container">
            <p>© 2023 MarketPlace. Все права защищены.</p>
        </div>
    </footer>
</body>
</html>