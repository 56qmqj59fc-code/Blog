<?php
session_start();
require_once "../config/db.php";

if(!isset($_SESSION["role"]) || $_SESSION["role"]!=="admin"){
die("Доступ запрещён");
}

$id=(int)$_GET["id"];

$stmt=$pdo->prepare("DELETE FROM comments WHERE id=?");
$stmt->execute([$id]);

header("Location: comments.php");