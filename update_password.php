<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = 'Пароли не совпадают';
        header("Location: profile.php");
        exit();
    }

    if (strlen($new_password) < 6) {
        $_SESSION['error'] = 'Пароль должен содержать минимум 6 символов';
        header("Location: profile.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['error'] = 'Текущий пароль указан неверно';
        header("Location: profile.php");
        exit();
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
       ->execute([$hashed_password, $user_id]);

    $_SESSION['success'] = 'Пароль успешно изменён!';
    header("Location: profile.php");
    exit();
}
?>