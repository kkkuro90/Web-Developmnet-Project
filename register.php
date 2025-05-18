<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once 'functions.php';

if (isLoggedIn()) {
    header('Location: profile.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $email = trim($_POST['email'] ?? '');
    
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Имя пользователя обязательно';
    } elseif (strlen($name) < 4) {
        $errors[] = 'Имя пользователя должно быть не менее 4 символов';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email';
    }
    
    if (empty($password)) {
        $errors[] = 'Пароль обязателен';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Пароль должен быть не менее 6 символов';
    }
    
    if ($password !== $password_confirm) {
        $errors[] = 'Пароли не совпадают';
    }
    
    if (empty($errors)) {
        try {

            $stmt = $pdo->prepare("SELECT id FROM users WHERE name = ? OR email = ?"); 
            $stmt->execute([$name, $email]);
            $existing_user = $stmt->fetch();
            
            if ($existing_user) {
                flash('Пользователь с таким именем или email уже существует', 'error');
            } else {

                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (name, password, email, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
                $stmt->execute([$name, $password_hash, $email]);

                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['name'] = $name; 
                $_SESSION['role'] = 'user';
                
                flash('Регистрация прошла успешно! Добро пожаловать!', 'success');
                header('Location: profile.php');
                exit();
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            flash('Ошибка при регистрации: ' . $e->getMessage(), 'error');
        }
    } else {
        flash(implode('<br>', $errors), 'error');
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - MarketPlace</title>
    <link rel="stylesheet" href="styles_register.css"> 
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="catalog.php" class="logo">MarketPlace</a>
        </div>
    </header>

    <main class="register-section">
        <div class="container">
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                    <p><?= $_SESSION['flash']['message'] ?></p>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <form method="post" class="register-form">
                <h2 class="form-title">Регистрация</h2>
                
                <div class="form-group">
                    <label for="username">Имя пользователя</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Подтверждение пароля</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                </div>
                
                <button type="submit" class="btn-submit">Зарегистрироваться</button>
                
                <p class="login-link">Уже есть аккаунт? <a href="login.php">Войти</a></p>
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