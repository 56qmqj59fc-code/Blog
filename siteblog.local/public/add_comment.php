<?php
session_start();
require_once "config/db.php";

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false,'error'=>'Войдите, чтобы комментировать']);
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = (int)$_POST['post_id'];
    $content = trim($_POST['content']);

    if(!$content){
        echo json_encode(['success'=>false,'error'=>'Комментарий пустой']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO comments (post_id,user_id,content,created_at) VALUES (?,?,?,NOW())");
    $stmt->execute([$post_id,$_SESSION['user_id'],$content]);

    echo json_encode([
        'success'=>true,
        'comment_id'=>$pdo->lastInsertId(),
        'name'=>$_SESSION['name'],
        'content'=>htmlspecialchars($content),
        'date'=>date('d.m.Y H:i')
    ]);
}