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

// Обработка формы регистрации
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = trim($_POST['name'] ?? '');  // Обратите внимание - 'name', а не 'username'
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $email = trim($_POST['email'] ?? '');
    
    $errors = [];
    
    // Валидация данных
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
            // Проверяем существование пользователя
            $stmt = $pdo->prepare("SELECT id FROM users WHERE name = ? OR email = ?");  // Используем 'name'
            $stmt->execute([$name, $email]);
            $existing_user = $stmt->fetch();
            
            if ($existing_user) {
                flash('Пользователь с таким именем или email уже существует', 'error');
            } else {
                // Хешируем пароль
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Создаем нового пользователя
                $stmt = $pdo->prepare("INSERT INTO users (name, password, email, role, created_at) VALUES (?, ?, ?, 'user', NOW())");  // Используем 'name'
                $stmt->execute([$name, $password_hash, $email]);
                
                // Автоматически входим после регистрации
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['name'] = $name;  // Сохраняем 'name' в сессии
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
    <style>
        :root {
            --primary: #FFFFFF;
            --secondary: #00E676; 
            --accent: #AA00FF;   
            --dark: #1A1A2E;
            --light: #F8F9FA;
            --text: #2D3748;
            --highlight: #FF4081;
            --transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--text);
            background: var(--light);
            line-height: 1.7;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        h1, h2, h3, h4 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            flex: 1;
        }

        .header {
            background: linear-gradient(135deg, var(--accent), var(--secondary));
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 5px 25px rgba(170, 0, 255, 0.3);
            padding: 1.5rem 0;
        }

        .logo {
            font-size: 2rem;
            font-weight: 900;
            color: white;
            text-decoration: none;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            letter-spacing: -1px;
        }

        .register-section {
            padding: 6rem 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .register-form {
            max-width: 500px;
            margin: 0 auto;
            padding: 3rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .register-form:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
        }

        .form-title {
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
            color: var(--accent);
            position: relative;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(170, 0, 255, 0.1);
        }

        .btn-submit {
            display: block;
            width: 100%;
            padding: 1rem;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 2rem;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-submit:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--accent), var(--secondary));
            z-index: -1;
            opacity: 0;
            transition: var(--transition);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 64, 129, 0.3);
        }

        .btn-submit:hover:before {
            opacity: 1;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text);
        }

        .login-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .login-link a:hover {
            color: var(--accen);
            text-decoration: underline;
        }

        .alert {
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: center;
            animation: fadeIn 0.5s forwards;
        }

        .alert-success {
            background-color: rgba(0, 230, 118, 0.1);
            color: var(--secondary);
            border: 1px solid var(--secondary);
        }

        .alert-error {
            background-color: rgba(255, 64, 129, 0.1);
            color: var(--accen);
            border: 1px solid var(--accen);
        }

        .footer {
            background: var(--dark);
            color: white;
            padding: 3rem 0;
            text-align: center;
            position: relative;
            margin-top: auto;
        }

        .footer:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
        }

        @media (max-width: 768px) {
            .register-form {
                padding: 2rem;
            }
            
            .form-title {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            .register-form {
                padding: 1.5rem;
            }
            
            .form-title {
                font-size: 1.5rem;
            }
            
            .form-control {
                padding: 0.8rem;
            }
        }
    </style>
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