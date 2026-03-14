<?php
session_start();
require_once "config/db.php";

header("Content-Type: application/json");

if(!isset($_SESSION["user_id"])){
    echo json_encode(["success"=>false, "message"=>"Не авторизованы"]);
    exit;
}

$comment_id = $_POST["comment_id"] ?? 0;

if(!$comment_id){
    echo json_encode(["success"=>false, "message"=>"Комментарий не найден"]);
    exit;
}

// Проверяем, что пользователь является автором комментария
$stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id=?");
$stmt->execute([$comment_id]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$comment){
    echo json_encode(["success"=>false, "message"=>"Комментарий не найден"]);
    exit;
}

if($comment["user_id"] != $_SESSION["user_id"]){
    echo json_encode(["success"=>false, "message"=>"Вы не можете удалить этот комментарий"]);
    exit;
}

// Удаляем комментарий
$stmt = $pdo->prepare("DELETE FROM comments WHERE id=?");
$stmt->execute([$comment_id]);

echo json_encode(["success"=>true]);