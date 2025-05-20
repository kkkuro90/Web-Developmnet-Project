<?php
session_start();
require_once 'config.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);

    $newImage = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $imageName = uniqid('product_', true) . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $oldImage = $stmt->fetchColumn();

            if ($oldImage && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }

            $newImage = $imageName;
        } else {
            $_SESSION['error'] = 'Ошибка при загрузке изображения';
            header("Location: admin.php?edit_product=$product_id");
            exit();
        }
    }

    if ($name && $price > 0 && $category) {
        try {
            if ($newImage) {
                $pdo->prepare("UPDATE products SET name = ?, price = ?, category = ?, description = ?, image = ? WHERE id = ?")
                    ->execute([$name, $price, $category, $description, $newImage, $product_id]);
            } else {
                $pdo->prepare("UPDATE products SET name = ?, price = ?, category = ?, description = ? WHERE id = ?")
                    ->execute([$name, $price, $category, $description, $product_id]);
            }

            $_SESSION['success'] = 'Товар успешно обновлён!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Ошибка базы данных: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = 'Все поля обязательны к заполнению';
    }

    header("Location: admin.php");
    exit();
}
?>