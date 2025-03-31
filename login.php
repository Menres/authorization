<?php
include 'config.php';
include 'session.php';
include 'templates/header.php';

if ($loggedIn) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Проверка на существование пользователя
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();


    // Проверяем, найден ли пользователь
    if ($user) {
        // Если пользователь не заблокирован, проверяем пароль
        if ($user['failed_attempts'] < 3) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                // Сбрасываем счетчик неудачных попыток при успешном входе
                $stmt = $pdo->prepare('UPDATE users SET failed_attempts = 0, last_failed_attempt = NULL WHERE id = ?');
                $stmt->execute([$user['id']]);
                header('Location: index.php');
                exit;
            } else {
                // Неудачный вход - увеличиваем счетчик неудачных попыток
                $failed_attempts = $user['failed_attempts'] + 1;
                $last_failed_attempt = date('Y-m-d H:i:s');
                $stmt = $pdo->prepare('UPDATE users SET failed_attempts = ?, last_failed_attempt = ? WHERE id = ?');
                $stmt->execute([$failed_attempts, $last_failed_attempt, $user['id']]);
                $error = 'Неверный логин или пароль.';
            }
        } else {
            $error = 'Ваш аккаунт заблокирован после 3-х неудачных попыток входа.';
        }
    } else {
        // Если пользователь не найден
        $error = 'Неверный логин или пароль.';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
</head>

<body>
    <h1>Авторизация</h1>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
    </form>
    <p><?php echo $error; ?></p>
    <p><a href="register.php">Зарегистрироваться</a></p>
</body>

</html>

<?php include 'templates/footer.php'; ?>
