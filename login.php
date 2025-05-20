<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_SESSION['flash'])) {
    unset($_SESSION['flash']);
}

require_once 'config.php';
require_once 'functions.php';

if (isLoggedIn()) {
    header('Location: profile.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        flash('Заполните все поля', 'error');
    } else {
        try {
            if (!isset($pdo)) {
                throw new Exception('Отсутствует подключение к базе данных');
            }

            $tableExists = $pdo->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
            if (!$tableExists) {
                throw new Exception('Таблица users не существует');
            }
            
            $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE name = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['name'];
                $_SESSION['role'] = $user['role']; 

                if ($user['role'] === 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: profile.php');
                }
                exit();
            } else {
                flash('Неверное имя пользователя или пароль', 'error');
            }
        } catch (PDOException $e) {
            error_log("DB Error: " . $e->getMessage());
            flash('Ошибка базы данных: ' . $e->getMessage(), 'error');
        } catch (Exception $e) {
            error_log("System Error: " . $e->getMessage());
            flash('Системная ошибка: ' . $e->getMessage(), 'error');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация - MarketPlace</title>
    <link rel="stylesheet" href="styles_login.css"> 
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">MarketPlace</a>
        </div>
    </header>

    <main class="login-section">
        <div class="container">
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                    <p><?= $_SESSION['flash']['message'] ?></p>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <form method="post" class="login-form">
                <h2 class="form-title">Вход в аккаунт</h2>
                
                <div class="form-group">
                    <label for="username">Имя пользователя</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn-submit">Войти</button>
                
                <p class="register-link">Еще нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>© 2023 MarketPlace. Все права защищены.</p>
        </div>
    </footer>
</body>
</html>