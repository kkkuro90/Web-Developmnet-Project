<?php
session_start();
require_once 'config.php';

$error = $success = "";

// Генерация простой математической капчи
$capcha_num1 = rand(1, 9);
$capcha_num2 = rand(1, 9);
$_SESSION['captcha'] = $capcha_num1 + $capcha_num2;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация - MarketPlace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css ">
    <style>
        :root {
            --primary-purple: #AA00FF;
            --primary-green: #00E676;
            --glass-white: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-white: rgba(255, 255, 255, 0.9);
            --transition: all 0.3s ease;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(-45deg, var(--primary-purple), var(--primary-green));
            color: var(--text-white);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 500px;
            width: 100%;
            padding: 2rem;
            background: var(--glass-white);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-purple), var(--primary-green));
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.8rem 1rem;
            margin-bottom: 1.5rem;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(170, 0, 255, 0.3);
            background: rgba(255, 255, 255, 0.15);
        }

        button {
            width: 100%;
            background-color: var(--primary-purple);
            color: white;
            padding: 0.8rem;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #9900E0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(170, 0, 255, 0.3);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            text-align: center;
        }

        .alert.error {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .alert.success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .captcha-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .captcha-question {
            font-weight: bold;
        }

        .captcha-input {
            width: 80px;
            padding: 0.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Регистрация</h2>

    <?php if (!empty($error)): ?>
        <div class="alert error"><?= $error ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" action="register.php">
        <label for="name">Имя</label>
        <input type="text" name="name" id="name" required placeholder="Ваше имя">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required placeholder="example@example.com">

        <label for="password">Пароль</label>
        <input type="password" name="password" id="password" required placeholder="••••••••">

        <label for="confirm_password">Подтвердите пароль</label>
        <input type="password" name="confirm_password" id="confirm_password" required placeholder="••••••••">

        <!-- Капча -->
        <div class="captcha-box">
            <span class="captcha-question"><?= $capcha_num1 ?> + <?= $capcha_num2 ?> = ?</span>
            <input type="number" name="captcha_answer" class="captcha-input" required>
        </div>

        <button type="submit" name="register">Зарегистрироваться</button>
        <p style="margin-top: 1.5rem; text-align: center;">
            Уже есть аккаунт? <a href="login.php" style="color: var(--primary-green); text-decoration: underline;">Войти</a>
        </p>
    </form>
</div>

</body>
</html>