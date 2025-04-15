<?php
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebPHPLab7</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Галерея</h1>
            <nav>
                <?php if (isLoggedIn()): ?>
                    <span>Привет, <?php echo e($_SESSION['username']); ?>!</span>
                    <?php if (isAdmin()): ?>
                        <a href="admin.php">Админка</a>
                    <?php endif; ?>
                    <a href="logout.php">Выйти</a>
                <?php else: ?>
                    <a href="login.php">Вход</a>
                    <a href="register.php">Регистрация</a>
                <?php endif; ?>
            </nav>
        </header>

        <?php displayFlash(); ?>

        <div class="gallery">
            <?php include 'display_gallery.php'; ?>
        </div>
    </div>
</body>
</html>