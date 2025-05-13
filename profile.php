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


/* if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=not_logged_in");
    exit();
}*/


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

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя - <?= htmlspecialchars($user['name']) ?></title>
    <style>
        :root {
            --primary-purple: #AA00FF;
            --primary-green: #00E676;
            --glass-white: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-white: rgba(255, 255, 255, 0.9);
        }
        
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(-45deg, var(--primary-purple), var(--primary-green));
            color: var(--text-white);
            min-height: 100vh;
        }
        
        .profile-container {
            display: flex;
            max-width: 1200px;
            margin: 2rem auto;
            background: var(--glass-white);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
        }
        
        .profile-sidebar {
            width: 300px;
            padding: 2rem;
            border-right: 1px solid var(--glass-border);
        }
        
        .user-avatar {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .user-avatar img {
            width: 300px;
            height: 300px;
            object-fit: cover;
            border: 5px solid var(--glass-white);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 1rem;
        }
        
        #avatar-upload {
            display: none;
        }
        
        .upload-btn, .save-btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .upload-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px dashed var(--glass-border);
        }
        
        .upload-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .save-btn {
            background: #9900e0a3;
            color: white;
        }
        
        .save-btn:hover {
            background: #9900e02d;
            transform: translateY(-2px);
        }
        
        .profile-menu ul {
            list-style: none;
            padding: 0;
            margin-top: 2rem;
        }
        
        .profile-menu li a {
            display: block;
            padding: 12px 16px;
            margin: 8px 0;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .profile-menu li a:hover, 
        .profile-menu li a.active {
            box-shadow: 0 4px 15px rgba(170, 0, 255, 0.3);
        }
        
        .profile-content {
            flex: 1;
            padding: 2rem;
        }
        
        .profile-section {
            display: none;
        }
        
        .profile-section.active {
            display: block;
        }
        
        .profile-section h2 {
            margin-top: 0;
            font-size: 28px;
            color: white;
            margin-bottom: 24px;
        }
        
        .profile-form, .settings-form {
            max-width: 600px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            opacity: 0.9;
        }
        
        .form-group input, 
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s;
        }
        
        .form-group input:focus, 
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 3px rgba(170, 0, 255, 0.2);
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
            border-radius: 15px;
        }
        
        .order-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.3s;
            border: 1px solid var(--glass-border);
        }
        
        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--glass-border);
        }
        
        .order-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.2);
        }
        
        .order-status.pending {
            background: rgba(255, 193, 7, 0.2);
            color: #FFC107;
        }
        
        .order-status.completed {
            background: rgba(40, 167, 69, 0.2);
            color: #34d058;
        }
        
        .order-status.cancelled {
            background: rgba(220, 53, 69, 0.2);
            color: #DC3545;
        }
        
        .order-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .details-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .details-link:hover {
            color: white;
            text-decoration: underline;
        }
        
        .no-orders {
            text-align: center;
            padding: 40px;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .view-all {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 16px;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            background: var(--primary-purple);
            transition: all 0.3s;
        }
        
        .view-all:hover {
            background: #9900E0;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(170, 0, 255, 0.3);
        }
        
        .account-actions {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--glass-border);
        }
        
        .danger-btn {
            background: rgba(220, 53, 69, 0.7);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .danger-btn:hover {
            background: rgba(220, 53, 69, 0.9);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
                margin: 1rem;
            }
            
            .profile-sidebar {
                width: auto;
                border-right: none;
                border-bottom: 1px solid var(--glass-border);
            }
        }
    </style>
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
                                        <a href="order_details.php?id=<?= $order['id'] ?>" class="details-link">Подробнее</a>
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
                </form>
                
                <div class="account-actions">
                    <button class="danger-btn" onclick="confirmDelete()">Удалить аккаунт</button>
                </div>
            </section>
        </div>
    </main>
    
    <script src="js/profile.js"></script>
    <script>
        // Переключение между разделами профиля
        document.addEventListener('DOMContentLoaded', function() {
            const menuLinks = document.querySelectorAll('.profile-menu a');
            const profileSections = document.querySelectorAll('.profile-section');
            
            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Удаляем активный класс у всех ссылок и разделов
                    menuLinks.forEach(item => item.classList.remove('active'));
                    profileSections.forEach(section => section.classList.remove('active'));
                    
                    // Добавляем активный класс к текущей ссылке
                    this.classList.add('active');
                    
                    // Показываем соответствующий раздел
                    const targetSection = document.querySelector(this.getAttribute('href'));
                    targetSection.classList.add('active');
                });
            });
            
            // Валидация формы изменения пароля
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