<?php
session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$email = trim($_POST['email']);
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
$stmt->execute([$email]);

$user = $stmt->fetch();

if($user && password_verify($password,$user['password'])){

$_SESSION['user_id']=$user['id'];
$_SESSION['role']=$user['role'];
$_SESSION['name']=$user['name'];

if($user['role']=="admin"){
header("Location: admin/posts.php");
}else{
header("Location: index.php");
}

exit;

}else{
$error="Неверный email или пароль";
}

}
?>

<!DOCTYPE html>
<html lang="ru">
<head>

<meta charset="UTF-8">
<title>Вход</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="form-container">

<h2>Вход</h2>

<?php if($error): ?>
<div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Пароль" required>

<button type="submit">Войти</button>

</form>

<p style="margin-top:15px">
Нет аккаунта? <a href="register.php">Регистрация</a>
</p>

</div>

</body>
</html>