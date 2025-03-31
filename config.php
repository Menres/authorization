<?php
$host = 'localhost';  
$db = 'bigboss';  // имя вашей базы данных
$user = 'root';  // имя пользователя базы данных
$pass = ''; 




try {
    // Правильный синтаксис для подключения к базе данных
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    // Установка атрибута для обработки ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Сообщение об ошибке
    echo "Connection failed: " . $e->getMessage();
}
?>


