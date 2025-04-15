<?php
require_once 'config.php';
require_once 'functions.php';

if (isLoggedIn()) {
    header('Location: index.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        flash('Заполните все поля', 'error');
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                flash('Вы успешно вошли в систему');
                header('Location: index.html');
                exit();
            } else {
                flash('Неверное имя пользователя или пароль', 'error');
            }
        } catch (PDOException $e) {
            flash('Ошибка при входе в систему', 'error');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - MarketPlace</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Шапка -->
    <header class="header">
        <div class="container">
            <a href="index.html" class="logo">MarketPlace</a>
        </div>
    </header>

    <!-- Форма входа -->
    <section class="auth-section">
        <div class="container">
            <h2>Вход</h2>
            <form id="loginForm" class="auth-form">
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Введите ваш email" required>
                </div>
                <div class="input-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" placeholder="Введите пароль" required>
                </div>
                <button type="submit" class="btn-submit">Войти</button>
                <p class="auth-link">Ещё не зарегистрированы? <a href="register.html">Зарегистрируйтесь здесь</a>.</p>
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