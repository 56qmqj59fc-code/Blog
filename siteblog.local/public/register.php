<?php
session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm = $_POST['confirm'];

if(strlen($password) < 6){
$error = "Пароль должен быть не менее 6 символов";
}
elseif($password !== $confirm){
$error = "Пароли не совпадают";
}
else{

$stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
$stmt->execute([$email]);

if($stmt->fetch()){
$error = "Пользователь с таким email уже существует";
}else{

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
$stmt->execute([$name,$email,$hash]);

header("Location: login.php");
exit;

}

}

}
?>

<!DOCTYPE html>
<html lang="ru">
<head>

<meta charset="UTF-8">
<title>Регистрация</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="form-container">

<h2>Регистрация</h2>

<?php if($error): ?>
<div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">

<input type="text" name="name" placeholder="Имя" required>

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Пароль" required>

<input type="password" name="confirm" placeholder="Подтвердите пароль" required>

<button type="submit">Зарегистрироваться</button>

</form>

<p style="margin-top:15px">
Уже есть аккаунт? <a href="login.php">Войти</a>
</p>

</div>

</body>
</html>