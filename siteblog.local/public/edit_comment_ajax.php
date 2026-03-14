<?php

session_start();
require_once "config/db.php";

header("Content-Type: application/json");

if(!isset($_SESSION["user_id"])){
echo json_encode(["success"=>false]);
exit;
}

$comment_id = $_POST["comment_id"] ?? 0;
$content = trim($_POST["content"] ?? "");

if($content == ""){
echo json_encode(["success"=>false]);
exit;
}

$stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id=?");
$stmt->execute([$comment_id]);
$comment = $stmt->fetch();

if(!$comment){
echo json_encode(["success"=>false]);
exit;
}

if($comment["user_id"] != $_SESSION["user_id"]){
echo json_encode(["success"=>false]);
exit;
}

$stmt = $pdo->prepare("UPDATE comments SET content=? WHERE id=?");
$stmt->execute([$content,$comment_id]);

echo json_encode([
"success"=>true,
"content"=>htmlspecialchars($content)
]);