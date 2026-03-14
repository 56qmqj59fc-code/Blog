<?php
session_start();
require_once "config/db.php";

if(!isset($_SESSION["user_id"])){
echo json_encode(["success"=>false]);
exit;
}

$user_id = $_SESSION["user_id"];
$post_id = (int)$_POST["post_id"];

$stmt = $pdo->prepare("
SELECT id FROM post_likes
WHERE user_id=? AND post_id=?
");

$stmt->execute([$user_id,$post_id]);

if($stmt->fetch()){

$pdo->prepare("
DELETE FROM post_likes
WHERE user_id=? AND post_id=?
")->execute([$user_id,$post_id]);

}else{

$pdo->prepare("
INSERT INTO post_likes (user_id,post_id)
VALUES (?,?)
")->execute([$user_id,$post_id]);

}

$stmt = $pdo->prepare("
SELECT COUNT(*) FROM post_likes
WHERE post_id=?
");

$stmt->execute([$post_id]);

$likes = $stmt->fetchColumn();

echo json_encode([
"success"=>true,
"likes"=>$likes
]);