<?php
session_start(); 
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    die('Доступ запрещен');
}

$user_id = $_SESSION['user_id'];
$uploadDir = __DIR__ . '/uploads/avatars/';


if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['avatar']['tmp_name'])) {
    $file = $_FILES['avatar'];
    

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg', 'image/png', 'image/gif'];
    
    if (!in_array($mime, $allowed)) {
        $_SESSION['error'] = 'Допустимы только JPG, PNG или GIF';
        header('Location: profile.php');
        exit;
    }
    

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('avatar_') . '.' . $ext;
    $destination = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        try {

            $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $oldAvatar = $stmt->fetchColumn();
            

            $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([$filename, $user_id]);
            

            if ($oldAvatar && file_exists($uploadDir . $oldAvatar)) {
                unlink($uploadDir . $oldAvatar);
            }
            

            $_SESSION['user_avatar'] = $filename;
            $_SESSION['success'] = 'Аватар обновлен!';
            
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Ошибка базы данных';
            unlink($destination);
        }
    } else {
        $_SESSION['error'] = 'Ошибка загрузки файла';
    }
    
    header('Location: profile.php');
    exit;
}