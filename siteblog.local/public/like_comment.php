<?php
session_start();
require_once "config/db.php";

if(!isset($_SESSION['user_id'])){
    echo json_encode(['success'=>false]);
    exit;
}

if($_SERVER['REQUEST_METHOD']==='POST'){
    $comment_id = (int)$_POST['comment_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT id FROM comment_likes WHERE comment_id=? AND user_id=?");
    $stmt->execute([$comment_id,$user_id]);
    if($stmt->fetch()){
        echo json_encode(['success'=>false]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO comment_likes (comment_id,user_id) VALUES (?,?)");
    $stmt->execute([$comment_id,$user_id]);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id=?");
    $stmt->execute([$comment_id]);
    $likes = $stmt->fetchColumn();

    echo json_encode(['success'=>true,'likes'=>$likes]);
}