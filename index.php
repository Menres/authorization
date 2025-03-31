<?php
include 'session.php';
include 'templates/header.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная страница</title>
</head>
<body>
    <?php if ($loggedIn): ?>
        <h1>Вы вошли как <?php echo htmlspecialchars($_SESSION['user_id']); ?>. <a href="logout.php">Выйти</a></h1>
        <a href="login.php">Авторизация</a>
        <p><a href="register.php">Регистрация</a></p>

        <!-- Здесь можно добавить контент для авторизованных пользователей -->
    <?php else: ?>
        <h1>Вы не авторизованы. Введите логин и пароль или зарегистрируйтесь.</h1>
        <p><a href="login.php">Авторизация</a></p>
        <p><a href="register.php">Регистрация</a></p>
    <?php endif; ?>
</body>
</html>


<?php include 'templates/footer.php'; // Подключение подвала ?>
