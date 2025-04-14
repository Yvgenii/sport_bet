<?php
session_start();
require '../Private/db.php';

function generateReferralCode($length = 8) {
    return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['reg-username']);
    $password = trim($_POST['reg-password']);
    $phone = trim($_POST['phone']);
    $referredBy = $_POST['referred_by'] ?? null;


    // Хэшируем пароль
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Проверка на уникальность имени пользователя
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);

    if ($stmt->rowCount() > 0) {
        die("Користувач із таким ім'ям вже існує.");
    }

    // Начальный баланс
    $initialBalance = 0;

    // Если указан промокод, проверим существует ли такой referral_code
    $referredBy = null;
    if (!empty($promo)) {
        $stmt = $pdo->prepare("SELECT referral_code FROM users WHERE referral_code = :code");
        $stmt->execute(['code' => $promo]);
        if ($stmt->rowCount() > 0) {
            $referredBy = $promo;
            $initialBalance = 1000; // Бонус за регистрацию по промокоду
        }
    }

    // Вставка нового пользователя
    $stmt = $pdo->prepare("INSERT INTO users (username, password, phone, balance, referral_code, referred_by) 
                           VALUES (:username, :password, :phone, :balance, :referral_code, :referred_by)");
    $referralCode = generateReferralCode();
    $stmt->execute([
        'username' => $username,
        'password' => $hashedPassword,
        'phone' => $phone,
        'balance' => $initialBalance,
        'referral_code' => $referralCode,
        'referred_by' => $referredBy
    ]);

    // Получаем ID нового пользователя
    $userId = $pdo->lastInsertId();

    // Начисление бонуса пригласившему пользователю
    if ($referredBy) {
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + 1000 WHERE referral_code = :code");
        $stmt->execute(['code' => $referredBy]);
    }

    // Записываем в сессию
    $_SESSION['user'] = [
        'id' => $userId,
        'username' => $username,
        'role' => 'user',
        'balance' => $initialBalance,
        'referral_code' => $referralCode
    ];

    header("Location: index.php");
    exit();
}
