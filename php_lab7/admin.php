<?php
require_once 'auth_check.php';
require_once 'functions.php';

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
    
    header('Location: admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админка</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Админка</h1>
            <nav>
                <a href="index.php">На главную</a>
                <a href="logout.php">Выйти</a>
            </nav>
        </header>

        <?php displayFlash(); ?>

        <section class="upload-section">
            <h2>Добавить изображение</h2>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <input type="file" name="image" accept="image/*" required>
                <button type="submit">Загрузить</button>
            </form>
        </section>

        <section class="admin-gallery">
            <h2>Управление изображениями</h2>
            <?php
            try {
                $stmt = $pdo->query("
                    SELECT i.*, u.username 
                    FROM images i
                    LEFT JOIN users u ON i.user_id = u.id
                    ORDER BY i.uploaded_at DESC
                ");
                $images = $stmt->fetchAll();

                if (count($images) > 0) {
                    echo '<div class="gallery">';
                    foreach ($images as $image) {
                        echo '<div class="gallery-item">';
                        echo '<img src="' . e($image['path']) . '" alt="' . e($image['name']) . '">';
                        echo '<div class="image-info">';
                        echo '<span>Загружено: ' . e($image['username'] ?: 'Система') . '</span>';
                        echo '<span>' . date('d.m.Y H:i', strtotime($image['uploaded_at'])) . '</span>';
                        echo '<form method="post">';
                        echo '<input type="hidden" name="image_id" value="' . e($image['id']) . '">';
                        echo '<button type="submit" name="delete" class="delete-btn">Удалить</button>';
                        echo '</form>';
                        echo '</div>';
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<p class="empty">Нет изображений</p>';
                }
            } catch (PDOException $e) {
                echo '<p class="error">Ошибка загрузки галереи</p>';
            }
            ?>
        </section>
    </div>
</body>
</html>