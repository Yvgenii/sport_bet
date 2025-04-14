<?php
// Настройки подключения к базе данных
$host = 'localhost'; // Имя хоста
$dbname = 'sport_bet'; // Имя базы данных 
$username = 'root'; // Имя пользователя базы данных (root)
$password = ''; // Пароль для базы данных (обычно пустой для root)

try {
    // Устанавливаем соединение с базой данных
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Устанавливаем режим обработки ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Если ошибка, выводим сообщение
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>
