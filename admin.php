<?php
session_start();
require_once 'config.php';

// Проверка прав администратора
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Обработка удаления пользователя
if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
    header("Location: admin.php");
    exit();
}

// Обработка добавления пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = $_POST['role'];

    if ($username && $email && $password) {
        $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)")
            ->execute([$name, $email, $password, $role]);
    }
    header("Location: admin.php");
    exit();
}

// Обработка удаления товара
if (isset($_GET['delete_product'])) {
    $product_id = intval($_GET['delete_product']);
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$product_id]);
    header("Location: admin.php");
    exit();
}

// Обработка добавления товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);

    if ($name && $price > 0 && $category) {
        $pdo->prepare("INSERT INTO products (name, price, category) VALUES (?, ?, ?)")
            ->execute([$name, $price, $category]);
    }
    header("Location: admin.php");
    exit();
}

// Получение списка пользователей
$users = $pdo->query("SELECT id, name, email, role FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Получение списка товаров
$products = $pdo->query("SELECT id, name, price, category FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Управление</title>
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

        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--glass-white);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--glass-border);
        }

        .admin-title {
            font-size: 2rem;
            margin: 0;
        }

        .admin-nav {
            display: flex;
            gap: 1rem;
        }

        .admin-nav a {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }

        .admin-nav a:hover {
            background: var(--primary-purple);
        }

        h2.section-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
            position: relative;
            padding-bottom: 0.5rem;
        }

        h2.section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-purple), var(--primary-green));
        }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 2rem;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
            border: 1px solid var(--glass-border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        th {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        input, select {
            padding: 0.6rem;
            border-radius: 8px;
            border: none;
            outline: none;
        }

        button {
            padding: 0.6rem;
            background-color: var(--primary-purple);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #9c27b0;
        }

        .actions a {
            color: #ff5252;
            text-decoration: none;
            margin-left: 10px;
            font-weight: bold;
        }

        .actions a:hover {
            color: red;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Панель управления</h1>
            <div class="admin-nav">
                <a href="admin.php">Управление</a>
                <a href="admin_stats.php">Статистика</a>
                <a href="logout.php">Выйти</a>
            </div>
        </div>

        <div class="admin-grid">
            <!-- Управление пользователями -->
            <div class="card">
                <h2 class="section-title">Пользователи</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Роль</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td class="actions">
                                <a href="?delete_user=<?= $user['id'] ?>" onclick="return confirm('Удалить пользователя?')">Удалить</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3 style="margin-top: 1.5rem;">Добавить пользователя</h3>
                <form method="post">
                    <input type="text" name="username" placeholder="Имя пользователя" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Пароль" required>
                    <select name="role" required>
                        <option value="">Выберите роль</option>
                        <option value="user">Пользователь</option>
                        <option value="admin">Администратор</option>
                    </select>
                    <button type="submit" name="add_user">Добавить</button>
                </form>
            </div>

            <!-- Управление товарами -->
            <div class="card">
                <h2 class="section-title">Товары</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Цена</th>
                            <th>Категория</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= number_format($product['price'], 2, ',', ' ') ?> ₽</td>
                            <td><?= htmlspecialchars($product['category']) ?></td>
                            <td class="actions">
                                <a href="?delete_product=<?= $product['id'] ?>" onclick="return confirm('Удалить товар?')">Удалить</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3 style="margin-top: 1.5rem;">Добавить товар</h3>
                <form method="post">
                    <input type="text" name="name" placeholder="Название товара" required>
                    <input type="number" name="price" placeholder="Цена" step="0.01" required>
                    <input type="text" name="category" placeholder="Категория" required>
                    <button type="submit" name="add_product">Добавить</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>