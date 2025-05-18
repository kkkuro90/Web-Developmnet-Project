<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['debug'])) {
    echo '<pre>';
    print_r($_SESSION);
    echo 'Session ID: ' . session_id();
    echo '</pre>';
    exit();
}

if (empty($_SESSION['user_id'])) {

    header("Location: login.php?error=session_expired");
    echo '<script>window.location.href = "login.php?error=js_redirect";</script>';
    exit();
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}


require_once 'config.php'; 

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?"); 
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$orders_stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$orders_stmt->execute([$user_id]);
$orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php if (isset($_SESSION['error'])): ?>
    <div style="color: red;  padding: 1rem; border-radius: 10px;">
        <?= $_SESSION['error'] ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div style="color: green; padding: 1rem; border-radius: 10px;">
        <?= $_SESSION['success'] ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя - <?= htmlspecialchars($user['name']) ?></title>
    <link rel="stylesheet" href="styles_profile.css">
</head>
<body>

    <main class="profile-container">
        <div class="profile-sidebar">
            <div class="user-avatar">
                <img src="<?= !empty($user['avatar']) ? 'uploads/avatars/'.$user['avatar'] : 'images/default-avatar.jpg' ?>" alt="Аватар">
                <form action="upload_avatar.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="avatar" id="avatar-upload" accept="image/*">
                    <label for="avatar-upload" class="upload-btn">Изменить фото</label>
                    <button type="submit" class="save-btn">Сохранить</button>
                </form>
            </div>
            <nav class="profile-menu">
                <ul>
                    <li><a href="#personal-info" class="active">Личные данные</a></li>
                    <li><a href="#orders">Мои заказы</a></li>
                    <li><a href="#settings">Настройки</a></li>
                    <li><a href="logout.php" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выйти</a></li>
                </ul>
            </nav>
        </div>


        <div class="profile-content">
            <section id="personal-info" class="profile-section active">
                <h2>Личные данные</h2>
                <a href="catalog.php" class="top-catalog-btn">🛒 Перейти к каталогу товаров</a>
                <form action="update_profile.php" method="post" class="profile-form">
                    <div class="form-group">
                        <label for="name">Имя</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Адрес доставки</label>
                        <textarea id="address" name="address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" class="save-btn">Сохранить изменения</button>

                </form>
            </section>
            
            <section id="orders" class="profile-section">
                <h2>История заказов</h2>
                <?php if (count($orders) > 0): ?>
                    <div class="orders-list">
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <span class="order-id">Заказ #<?= $order['id'] ?></span>
                                    <span class="order-date"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></span>
                                    <span class="order-status <?= $order['status'] ?>"><?= $order['status'] ?></span>
                                </div>
                                <div class="order-body">
                                    <div class="order-summary">
                                    <span>Сумма: <?= number_format((float)($order['total_amount'] ?? 0), 2, ',', ' ') ?> ₽</span><span>Сумма: <?= number_format((float)($order['total_amount'] ?? 0), 2, ',', ' ') ?> ₽</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="all_orders.php" class="view-all">Показать все заказы</a>
                <?php else: ?>
                    <p class="no-orders">У вас пока нет заказов</p>
                <?php endif; ?>
            </section>
            
            <section id="settings" class="profile-section">
                <h2>Настройки аккаунта</h2>
                <form action="update_password.php" method="post" class="settings-form">
                    <div class="form-group">
                        <label for="current_password">Текущий пароль</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Новый пароль</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Подтвердите новый пароль</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="save-btn">Изменить пароль</button>
                    <button class="save-btn" onclick="confirmDelete()">Удалить аккаунт</button>
                </form>
                
                <div class="account-actions">
                    <button class="danger-btn" onclick="confirmDelete()">Удалить аккаунт</button>
                </div>
            </section>
        </div>
    </main>
    
    <script src="profile.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuLinks = document.querySelectorAll('.profile-menu a');
            const profileSections = document.querySelectorAll('.profile-section');
            
            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    menuLinks.forEach(item => item.classList.remove('active'));
                    profileSections.forEach(section => section.classList.remove('active'));
                    
                    this.classList.add('active');
                    
                    const targetSection = document.querySelector(this.getAttribute('href'));
                    targetSection.classList.add('active');
                });
            });
            
            const passwordForm = document.querySelector('.settings-form');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (newPassword !== confirmPassword) {
                        e.preventDefault();
                        alert('Новый пароль и подтверждение пароля не совпадают!');
                    }
                });
            }
        });
        
        function confirmDelete() {
            if (confirm("Вы уверены, что хотите удалить свой аккаунт? Это действие нельзя отменить.")) {
                window.location.href = "delete_account.php";
            }
        }
    </script>

    <form id="logout-form" action="logout.php" method="POST" style="display: none;">
        <input type="hidden" name="_token" value="<?= session_id() ?>">
    </form>
</body>
</html>