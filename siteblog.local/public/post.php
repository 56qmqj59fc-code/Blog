<?php
session_start();
require_once "config/db.php";

if(!isset($_GET["id"])) {
    die("Пост не найден");
}

$post_id = (int)$_GET["id"];

// Получение поста
$stmt = $pdo->prepare("
SELECT posts.*, users.name AS author,
(SELECT COUNT(*) FROM post_likes WHERE post_id = posts.id) AS likes
FROM posts
JOIN users ON posts.user_id = users.id
WHERE posts.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$post){
    die("Пост не найден");
}

// Получение комментариев
$stmt = $pdo->prepare("
SELECT comments.*, users.name,
(SELECT COUNT(*) FROM comment_likes WHERE comment_id = comments.id) AS likes
FROM comments
JOIN users ON comments.user_id = users.id
WHERE post_id = ?
ORDER BY created_at DESC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($post["title"]); ?></title>
<link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php include 'header.php'; ?>

<div class="container">

<a href="index.php" class="btn">← Назад</a>

<h2><?php echo htmlspecialchars($post["title"]); ?></h2>

<p class="meta">
Автор: <?php echo htmlspecialchars($post["author"]); ?>
</p>

<?php if($post["image"]): ?>
<img src="<?php echo $post["image"]; ?>" class="post-image">
<?php endif; ?>

<div class="post-like">
<button id="likePostBtn" data-id="<?php echo $post_id; ?>">❤ Нравится</button>
<span id="postLikeCount"><?php echo $post["likes"]; ?></span>
</div>

<p><?php echo nl2br(htmlspecialchars($post["content"])); ?></p>

<h3 style="margin-top:30px">Комментарии</h3>

<div id="comments">

<?php foreach($comments as $comment): ?>
<div class="comment" data-id="<?php echo $comment['id']; ?>">
    <div class="comment-meta">
        <b>
            <?php echo htmlspecialchars($comment["name"]); ?>
            <?php if(isset($_SESSION["user_id"]) && $_SESSION["user_id"]==$comment["user_id"]): ?>
                <span style="color:#3498db">(Вы)</span>
            <?php endif; ?>
        </b>
        | <?php echo date("d.m.Y H:i", strtotime($comment["created_at"])); ?>
    </div>

    <div class="comment-text"><?php echo htmlspecialchars($comment["content"]); ?></div>

    <div class="comment-like">
        <button class="like-btn" data-id="<?php echo $comment['id']; ?>">❤</button>
        <span class="like-count"><?php echo $comment['likes']; ?></span>
    </div>

    <?php if(isset($_SESSION["user_id"]) && $_SESSION["user_id"]==$comment["user_id"]): ?>
    <div class="comment-actions">
        <button class="edit-btn" data-id="<?php echo $comment['id']; ?>">Редактировать</button>
        <button class="delete-btn" data-id="<?php echo $comment['id']; ?>">Удалить</button>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>

</div>

<?php if(isset($_SESSION["user_id"])): ?>
<div class="comment-form">
<h3>Добавить комментарий</h3>
<form id="commentForm">
<textarea name="content" required></textarea>
<input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
<button type="submit">Отправить</button>
</form>
</div>
<?php else: ?>
<p class="login-msg">
Чтобы оставить комментарий, <a href="login.php">войдите</a>
</p>
<?php endif; ?>

</div>

<script>
const commentsContainer = document.getElementById("comments");

/* ЛАЙК ПОСТА */
const likeBtn = document.getElementById("likePostBtn");
if(likeBtn){
    likeBtn.addEventListener("click", function(){
        let id = this.dataset.id;
        fetch("like_post.php",{
            method:"POST",
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:"post_id="+id
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.success){
                document.getElementById("postLikeCount").innerText = data.likes;
            }
        });
    });
}

/* ЛАЙК КОММЕНТАРИЯ */
commentsContainer.addEventListener("click", function(e){
    if(e.target.classList.contains("like-btn")){
        let id = e.target.dataset.id;
        let count = e.target.nextElementSibling;
        fetch("like_comment.php",{
            method:"POST",
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:"comment_id="+id
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.success){
                count.innerText = data.likes;
            }
        });
    }
});

/* ДОБАВЛЕНИЕ КОММЕНТАРИЯ */
const form = document.getElementById("commentForm");
if(form){
    form.addEventListener("submit", function(e){
        e.preventDefault();
        let formData = new FormData(this);
        fetch("add_comment.php",{
            method:"POST",
            body:formData
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.success){
                let commentHTML = `
<div class="comment" data-id="${data.comment_id}">
    <div class="comment-meta">
        <b>${data.name} <span style="color:#3498db">(Вы)</span></b> | ${data.date}
    </div>
    <div class="comment-text">${data.content}</div>
    <div class="comment-like">
        <button class="like-btn" data-id="${data.comment_id}">❤</button>
        <span class="like-count">0</span>
    </div>
    <div class="comment-actions">
        <button class="edit-btn" data-id="${data.comment_id}">Редактировать</button>
        <button class="delete-btn" data-id="${data.comment_id}">Удалить</button>
    </div>
</div>
                `;
                commentsContainer.insertAdjacentHTML("afterbegin", commentHTML);
                form.reset();
            }
        });
    });
}

/* УДАЛЕНИЕ КОММЕНТАРИЯ */
commentsContainer.addEventListener("click", function(e){
    if(e.target.classList.contains("delete-btn")){
        if(!confirm("Удалить комментарий?")) return;
        let commentBlock = e.target.closest(".comment");
        let id = commentBlock.dataset.id;
        fetch("delete_comment_ajax.php",{   // <-- поправили на реальный файл
            method:"POST",
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:"comment_id="+id
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.success){
                commentBlock.remove();
            } else {
                alert(data.message || "Ошибка удаления");
            }
        });
    }
});

/* РЕДАКТИРОВАНИЕ КОММЕНТАРИЯ */
commentsContainer.addEventListener("click", function(e){
    if(e.target.classList.contains("edit-btn")){
        let commentBlock = e.target.closest(".comment");
        let textBlock = commentBlock.querySelector(".comment-text");
        let oldText = textBlock.innerText;
        let newText = prompt("Редактировать комментарий:", oldText);
        if(newText === null) return;

        fetch("edit_comment_ajax.php",{   // <-- поправили на реальный файл
            method:"POST",
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:"comment_id="+commentBlock.dataset.id+"&content="+encodeURIComponent(newText)
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.success){
                textBlock.innerText = data.content;
            } else {
                alert(data.message || "Ошибка редактирования");
            }
        });
    }
});
</script>

</body>
</html>