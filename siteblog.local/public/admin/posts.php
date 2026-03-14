<?php
session_start();
require_once "../config/db.php";

/* доступ только админам */
if(!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin"){
    header("Location: ../index.php");
    exit;
}

/* получаем посты */
$stmt = $pdo->query("
SELECT posts.*, users.name AS author
FROM posts
JOIN users ON posts.user_id = users.id
ORDER BY posts.created_at DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Админ панель</title>
<link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="container">

    <!-- ADMIN HEADER -->
    <div class="admin-header">
        <div class="admin-header-top">
            <h1>Админ панель</h1>
            <a href="../logout.php" class="btn logout-btn">Выйти</a>
        </div>
        <div class="admin-menu">
            <a href="post_form.php" class="btn">Добавить пост</a>
            <a href="comments.php" class="btn">Комментарии</a>
            <a href="../index.php" class="btn secondary">На сайт</a>
        </div>
    </div>

    <!-- POSTS TABLE -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Заголовок</th>
                <th>Автор</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($posts as $post): ?>
            <tr>
                <td data-label="ID"><?php echo $post["id"]; ?></td>
                <td data-label="Заголовок"><?php echo htmlspecialchars($post["title"]); ?></td>
                <td data-label="Автор"><?php echo htmlspecialchars($post["author"]); ?></td>
                <td data-label="Дата"><?php echo date("d.m.Y H:i", strtotime($post["created_at"])); ?></td>
                <td class="actions" data-label="Действия">
                    <a href="post_form.php?id=<?php echo $post["id"]; ?>" class="btn edit-btn">Редактировать</a>
                    <a href="delete_post.php?id=<?php echo $post["id"]; ?>" class="btn delete-btn" onclick="return confirm('Удалить пост?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

</body>
</html>