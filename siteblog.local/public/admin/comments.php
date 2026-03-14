<?php
session_start();
require_once "../config/db.php";

if(!isset($_SESSION["role"]) || $_SESSION["role"]!=="admin"){
die("Доступ запрещён");
}

$stmt=$pdo->query("
SELECT comments.*, users.name
FROM comments
JOIN users ON comments.user_id=users.id
ORDER BY created_at DESC
LIMIT 50
");

$comments=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Комментарии</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="container">

<h2>Последние комментарии</h2>

<a href="posts.php">Назад</a>

<?php foreach($comments as $c): ?>

<div class="comment">

<div class="comment-meta">
<b><?php echo htmlspecialchars($c["name"]); ?></b>
| <?php echo $c["created_at"]; ?>
</div>

<p><?php echo htmlspecialchars($c["content"]); ?></p>

<a href="delete_comment.php?id=<?php echo $c["id"]; ?>"
onclick="return confirm('Удалить комментарий?')">
Удалить
</a>

</div>

<?php endforeach; ?>

</div>

</body>
</html>