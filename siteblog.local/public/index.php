<?php
session_start();
require_once 'config/db.php';

// Пагинация
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Получаем посты
$stmt = $pdo->prepare("
    SELECT posts.*, users.name 
    FROM posts 
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

$total = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$total_pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Блог</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Подключаем шапку -->
<?php include 'header.php'; ?>

<div class="container">

    <?php foreach($posts as $post): ?>
        <div class="post-card">
            <h2>
                <a href="post.php?id=<?php echo $post['id']; ?>">
                    <?php echo htmlspecialchars($post['title']); ?>
                </a>
            </h2>
            <p class="meta">
                Автор: <?php echo htmlspecialchars($post['name']); ?> |
                Дата: <?php echo $post['created_at']; ?>
            </p>
            <?php if($post['image']): ?>
                <img src="<?php echo $post['image']; ?>" class="post-image">
            <?php endif; ?>
            <p>
                <?php echo mb_substr(strip_tags($post['content']),0,200); ?>...
            </p>
        </div>
    <?php endforeach; ?>

    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?page=<?php echo $page-1; ?>" class="btn">Предыдущая</a>
        <?php endif; ?>

        <?php for($i=1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="btn <?php if($i==$page) echo 'active'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if($page < $total_pages): ?>
            <a href="?page=<?php echo $page+1; ?>" class="btn">Следующая</a>
        <?php endif; ?>
    </div>

</div>

</body>
</html>