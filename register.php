<?php

include 'templates/header.php';
include 'session.php';

if ($loggedIn) {
    header('Location: index.php');
    exit;
}

// Подключение к базе данных
$servername = "localhost"; // или ваш сервер
$username = "root"; // ваше имя пользователя
$password = ""; // ваш пароль
$dbname = "bigboss"; // имя вашей базы данных

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Инициализация переменной для пароля
$password_input = '';

// Проверка, была ли отправлена форма регистрации (если кнопка регистрации нажата(register))
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {     
    // Получаем данные для регистрации
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password_input = $_POST['password']; // Сохраняем введённый пароль для проверки
    $fullname = $_POST['fullname'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $interests = $_POST['interests'];
    $vk_link = $_POST['vk_link'];
    $blood_type = $_POST['blood_type'];
    $rhesus_factor = $_POST['rhesus_factor'];

    // Функция для проверки пароля
    function isValidPassword($password) {
        if (strlen($password) <= 6) return false;
        if (!preg_match('/[A-Z]/', $password)) return false;
        if (!preg_match('/[a-z]/', $password)) return false;
        if (!preg_match('/[0-9]/', $password)) return false;
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) return false;
        if (!preg_match('/ /', $password)) return false;
        if (!preg_match('/-/', $password)) return false;
        if (!preg_match('/_/', $password)) return false;
        if (preg_match('/[а-яА-ЯёЁ]/u', $password)) return false;

        return true;
    }

    // Проверка пароля
    if (!isValidPassword($password_input)) {
        echo "Пароль не соответствует требованиям. Убедитесь, что он содержит более 6 символов, включает заглавные и строчные буквы, цифры, специальные символы, пробелы, дефисы и подчеркивания, и не содержит русских букв.";
    } else {
        // Проверка существования email
        $checkEmailStmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $checkEmailStmt->bind_result($emailCount);
        $checkEmailStmt->fetch();
        $checkEmailStmt->close();

        if ($emailCount > 0) {
            echo "Этот адрес электронной почты уже зарегистрирован. Пожалуйста, используйте другой.";
        } else {
            // Хеширование пароля
            $hashedPassword = password_hash($password_input, PASSWORD_DEFAULT);

            // Подготовка и выполнение SQL-запроса для вставки
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, fullname, dob, address, gender, interests, vk_link, blood_type, rhesus_factor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssss", $username, $email, $hashedPassword, $fullname, $dob, $address, $gender, $interests, $vk_link, $blood_type, $rhesus_factor);

            if ($stmt->execute()) {
                echo "Регистрация прошла успешно!";
            } else {
                echo "Ошибка: " . $stmt->error;
            }

            // Закрытие подготовленного выражения
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
</head>
<body>
    <h2>Регистрация</h2>
    <form action="" method="post">
        <label for="username">Имя пользователя:</label>
        <input type="text" name="username" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Пароль:</label>
        <input type="password" name="password" required><br>

        <label for="fullname">ФИО:</label>
        <input type="text" name="fullname" required><br>

        <label for="dob">Дата рождения:</label>
        <input type="date" name="dob" required><br>

        <label for="address">Адрес:</label>
        <input type="text" name="address"><br>

        <label for="gender">Пол:</label>
        <select name="gender" required>
            <option value="мужской">Мужской</option>
            <option value="женский">Женский</option>
        </select><br>

        <label for="interests">Интересы:</label>
        <textarea name="interests"></textarea><br>

        <label for="vk_link">Ссылка на профиль ВК:</label>
        <input type="url" name="vk_link"><br>

        <label for="blood_type">Группа крови:</label>
        <select name="blood_type" required>
            <option value="I">I</option>
            <option value="II">II</option>
            <option value="III">III</option>
            <option value="IV">IV</option>
        </select><br>

        <label for="rhesus_factor">Резус-фактор:</label>
        <select name="rhesus_factor" required>
            <option value="+">+</option>
            <option value="-">-</option>
        </select><br>

        <button type="submit" name="register">Зарегистрироваться</button>
        <p><a href="login.php">Авторизироваться</a></p>
    </form>
</body>
</html>
<?php include 'templates/footer.php'; ?>