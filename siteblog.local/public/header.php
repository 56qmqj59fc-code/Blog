<?php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Блог</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="header">
    <a href="index.php" class="site-title"><b>Блог</b></a>
    <div class="topbar">
        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if($_SESSION['role'] === 'admin'): ?>
                <a href="admin/posts.php" class="btn">Админ панель</a>
            <?php endif; ?>
            <a href="logout.php" class="btn logout-btn">Выйти</a>
        <?php else: ?>
            <a href="login.php" class="btn">Войти</a>
            <a href="register.php" class="btn secondary">Регистрация</a>
        <?php endif; ?>
    </div>
</div>