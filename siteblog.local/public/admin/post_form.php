<?php
session_start();
require_once "../config/db.php";

/* доступ только админам */
if(!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin"){
    die("Доступ запрещён");
}

// Инициализация переменной $id
$id = $_GET["id"] ?? null;

$title = "";
$content = "";
$image = "";

// Если редактируем существующий пост
if($id){
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id=?");
    $stmt->execute([$id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if($post){
        $title = $post["title"];
        $content = $post["content"];
        $image = $post["image"];
    } else {
        die("Пост не найден");
    }
}

// Обработка отправки формы
if($_SERVER["REQUEST_METHOD"] === "POST"){

    $title = $_POST["title"];
    $content = $_POST["content"];

    $image_path = $image;

    // Если загружена новая картинка
    if(!empty($_FILES["image"]["name"])){
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target = "../uploads/" . $filename;

        if(move_uploaded_file($_FILES["image"]["tmp_name"], $target)){
            $image_path = "uploads/" . $filename;
        }
    }

    if($id){
        // Редактирование поста
        $stmt = $pdo->prepare("
            UPDATE posts
            SET title=?, content=?, image=?
            WHERE id=?
        ");
        $stmt->execute([$title, $content, $image_path, $id]);
    } else {
        // Добавление нового поста
        $stmt = $pdo->prepare("
            INSERT INTO posts(title, content, image, user_id)
            VALUES(?,?,?,?)
        ");
        $stmt->execute([
            $title,
            $content,
            $image_path,
            $_SESSION["user_id"]
        ]);
    }

    header("Location: posts.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $id ? "Редактировать" : "Добавить"; ?> пост</title>

<link rel="stylesheet" href="../css/style.css">

</head>
<body>

<div class="container">

    <!-- Кнопка назад -->
    <a href="posts.php" class="btn" style="margin-bottom:15px; display:inline-block;">← Назад к постам</a>

    <h2><?php echo $id ? "Редактировать" : "Добавить"; ?> пост</h2>

    <form method="POST" enctype="multipart/form-data">

        <label>Заголовок</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>

        <label>Текст</label>
        <textarea name="content" rows="8" required><?php echo htmlspecialchars($content); ?></textarea>

        <label>Картинка</label>
        <input type="file" name="image">

        <?php if($image): ?>
            <img src="../<?php echo $image; ?>" width="200" style="display:block; margin-top:10px;">
        <?php endif; ?>

        <button type="submit">Сохранить</button>

    </form>

</div>

</body>
</html>