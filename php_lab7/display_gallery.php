<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("
        SELECT i.*, u.username 
        FROM images i
        LEFT JOIN users u ON i.user_id = u.id
        ORDER BY i.uploaded_at DESC
    ");
    $images = $stmt->fetchAll();

    if (count($images) > 0) {
        foreach ($images as $image) {
            echo '<div class="gallery-item">';
            echo '<img src="' . e($image['path']) . '" alt="' . e($image['name']) . '">';
            echo '<div class="image-info">';
            echo '<span>Загружено: ' . e($image['username'] ?: 'Система') . '</span>';
            echo '<span>' . date('d.m.Y H:i', strtotime($image['uploaded_at'])) . '</span>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p class="empty">В галерее пока нет фотографий</p>';
    }
} catch (PDOException $e) {
    echo '<p class="error">Ошибка загрузки галереи</p>';
}
?>