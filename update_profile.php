<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    if (empty($name) || empty($email)) {
        $_SESSION['error'] = 'Имя и email обязательны для заполнения';
        header("Location: profile.php");
        exit();
    }
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Этот email уже используется другим пользователем';
        header("Location: profile.php");
        exit();
    }

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $address, $user_id]);
    
    $_SESSION['success'] = 'Данные профиля успешно обновлены';
}

header("Location: profile.php");
exit();