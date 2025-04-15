<?php
require_once 'php/config.php';
require_once 'php/functions.php';

if (isLoggedIn()) {
    header('Location: index.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($username) || empty($password) || empty($confirm_password)) {
        flash('Заполните все поля', 'error');
    } elseif ($password !== $confirm_password) {
        flash('Пароли не совпадают', 'error');
    } elseif (strlen($password) < 6) {
        flash('Пароль должен быть не менее 6 символов', 'error');
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed_password]);
            
            flash('Регистрация успешна. Теперь вы можете войти.');
            header('Location: php/login.php');
            exit();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                flash('Это имя пользователя уже занято', 'error');
            } else {
                flash('Ошибка при регистрации', 'error');
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - MarketPlace</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Шапка -->
    <header class="header">
        <div class="container">
            <a href="index.html" class="logo">MarketPlace</a>
        </div>
    </header>

    <!-- Форма регистрации -->
    <section class="auth-section">
        <div class="container">
            <h2>Регистрация</h2>
            <form id="registerForm" class="auth-form">
                <div class="input-group">
                    <label for="name">Имя:</label>
                    <input type="text" id="name" name="name" placeholder="Введите ваше имя" required>
                </div>
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Введите ваш email" required>
                </div>
                <div class="input-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" placeholder="Введите пароль" required>
                </div>
                <div class="input-group">
                    <label for="confirm-password">Подтвердите пароль:</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Подтвердите пароль" required>
                </div>
                <button type="submit" class="btn-submit">Зарегистрироваться</button>
                <p class="auth-link">Уже есть аккаунт? <a href="login.html">Войдите здесь</a>.</p>
            </form>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>© 2025 MarketPlace. Все права защищены.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>