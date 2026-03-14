<?php
session_start();
require_once "config/db.php";

header("Content-Type: application/json");

if(!isset($_SESSION["user_id"])){
    echo json_encode(["success"=>false, "message"=>"Не авторизованы"]);
    exit;
}

$comment_id = $_POST["comment_id"] ?? 0;
$content = trim($_POST["content"] ?? "");

if(!$comment_id || $content == ""){
    echo json_encode(["success"=>false, "message"=>"Неверные данные"]);
    exit;
}

// Проверяем авторство
$stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id=?");
$stmt->execute([$comment_id]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$comment){
    echo json_encode(["success"=>false, "message"=>"Комментарий не найден"]);
    exit;
}

if($comment["user_id"] != $_SESSION["user_id"]){
    echo json_encode(["success"=>false, "message"=>"Вы не можете редактировать этот комментарий"]);
    exit;
}

// Обновляем комментарий
$stmt = $pdo->prepare("UPDATE comments SET content=? WHERE id=?");
$stmt->execute([$content, $comment_id]);

echo json_encode(["success"=>true, "content"=>htmlspecialchars($content)]);