<?php
session_start();
require_once 'config.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);

    try {
        $pdo->prepare("DELETE FROM orders WHERE user_id = ?")->execute([$user_id]);

        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);

        header("Location: admin.php?success=1");
    } catch (PDOException $e) {
        header("Location: admin.php?error=" . urlencode("Не удалось удалить пользователя: " . $e->getMessage()));
    }

    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = trim($_POST['role']);

    if ($name && $email && $password && $role) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $role]);

            if ($stmt->rowCount()) {
                $_SESSION['success'] = 'Пользователь успешно добавлен';
            } else {
                $_SESSION['error'] = 'Не удалось добавить пользователя';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Ошибка базы данных: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = 'Все поля обязательны к заполнению';
    }

    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete_product'])) {
    $product_id = intval($_GET['delete_product']);
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$product_id]);
    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);

    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $imageName = uniqid('product_', true) . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $image = $imageName;
        } else {
            $_SESSION['error'] = 'Ошибка при загрузке изображения';
        }
    }

    if ($name && $price > 0 && $category && $image) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, category, image, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $price, $category, $image, $description]);

            if ($stmt->rowCount()) {
                $_SESSION['success'] = 'Товар успешно добавлен';
            } else {
                $_SESSION['error'] = 'Не удалось добавить товар';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Ошибка базы данных: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = 'Все поля обязательны к заполнению, включая изображение';
    }

    header("Location: admin.php");
    exit();
}

$users = $pdo->query("SELECT id, name, email, role FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$products = $pdo->query("SELECT id, name, price, category, image, description FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель - Управление</title>
    <link rel="stylesheet" href="styles_admin.css">
</head>
<body>

<div class="admin-container">

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php elseif (!empty($_SESSION['error'])): ?>
        <div class="alert error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="admin-header">
        <h1 class="admin-title">Панель управления</h1>
        <div class="admin-nav">
            <a href="admin.php">Управление</a>
            <a href="admin_stats.php">Статистика</a>
            <a href="logout.php">Выйти</a>
        </div>
    </div>

    <div class="admin-grid">
        <div class="card">
            <h2 class="section-title">Пользователи</h2>
            <div class="table-scroll">
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
            </div>

            <h3 style="margin-top: 1.5rem;">Добавить пользователя</h3>
            <form method="post">
                <input type="text" name="name" placeholder="Имя пользователя" required>
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

        <div class="card">
            <h2 class="section-title">Товары</h2>
            <div class="table-scroll">
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
                                <a href="?edit_product=<?= $product['id'] ?>">Редактировать</a>
                                <a href="?delete_product=<?= $product['id'] ?>" onclick="return confirm('Удалить товар?')">Удалить</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($_GET['edit_product'])):
                $product_id = intval($_GET['edit_product']);
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($product):
            ?>
                <h3 style="margin-top: 1.5rem;">Редактировать товар</h3>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" placeholder="Название товара" required>
                    <input type="number" name="price" value="<?= $product['price'] ?>" step="0.01" placeholder="Цена" required>
                    <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" placeholder="Категория" required>
                    <textarea name="description" rows="4" placeholder="Описание товара"><?= htmlspecialchars($product['description']) ?></textarea>
                    <label for="image">Изменить изображение:</label>
                    <input type="file" name="image" accept="image/*">
                    <button type="submit" name="edit_product">Сохранить изменения</button>
                    <a href="admin.php">Отмена</a>
                </form>
            <?php endif; endif; ?>

            <h3 style="margin-top: 1.5rem;">Добавить товар</h3>
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Название товара" required>
                <input type="number" name="price" placeholder="Цена" step="0.01" required>
                <input type="text" name="category" placeholder="Категория" required>
                <input type="file" name="image" accept="image/*" required>
                <textarea name="description" rows="4" placeholder="Описание товара"></textarea>
                <button type="submit" name="add_product">Добавить</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>