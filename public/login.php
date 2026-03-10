<?php
session_start();
require_once __DIR__ . '/../Private/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim(htmlspecialchars($_POST['username']));
    $password = trim(htmlspecialchars($_POST['password']));


    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => isset($user['role']) ? $user['role'] : 'user',
            'balance' => $user['balance'],
        ];

        
        if ($_SESSION['user']['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Неверное имя пользователя или пароль!";
    }
}
?>
