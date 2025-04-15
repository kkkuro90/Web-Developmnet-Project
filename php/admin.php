<?php
require_once 'php/auth_check.php';
require_once 'php/functions.php';

if (!isAdmin()) {
    flash('Доступ запрещен', 'error');
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $imageId = (int)$_POST['image_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT path FROM images WHERE id = ?");
        $stmt->execute([$imageId]);
        $image = $stmt->fetch();
        
        if ($image) {
            if (file_exists($image['path'])) {
                unlink($image['path']);
            }
            
            $stmt = $pdo->prepare("DELETE FROM images WHERE id = ?");
            $stmt->execute([$imageId]);
            
            flash('Изображение успешно удалено');
        }
    } catch (PDOException $e) {
        flash('Ошибка при удалении изображения', 'error');
    }
    
    header('Location: php/admin.php');
    exit();
}
?>