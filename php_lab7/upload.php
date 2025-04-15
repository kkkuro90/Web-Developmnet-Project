<?php
require_once 'auth_check.php';
require_once 'config.php';
require_once 'functions.php';

if (!isAdmin()) {
    flash('Доступ запрещен', 'error');
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    if (!in_array($file['type'], ALLOWED_TYPES)) {
        flash('Допустимы только изображения JPG, PNG и GIF', 'error');
        header('Location: admin.php');
        exit();
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        flash('Размер файла не должен превышать 5MB', 'error');
        header('Location: admin.php');
        exit();
    }
    
    if (!file_exists(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $targetPath = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO images (user_id, name, path, size)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $file['name'],
                $targetPath,
                $file['size']
            ]);
            
            flash('Изображение успешно загружено');
        } catch (PDOException $e) {
            unlink($targetPath);
            flash('Ошибка при сохранении в базу данных', 'error');
        }
    } else {
        flash('Ошибка при загрузке файла', 'error');
    }
}

header('Location: admin.php');
exit();
?>