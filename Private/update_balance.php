<?php
session_start();
require_once "../Private/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Користувач не авторизований"]);
    exit();
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$amountRaw = $_POST['amount'] ?? '';

if (!in_array($action, ['bet', 'win'])) {
    echo json_encode(["success" => false, "message" => "Невідома дія"]);
    exit();
}

// Конвертуємо суму у копійки (наприклад, 1.11 => 111)
$amount = round(floatval($amountRaw) * 100);
if ($amount <= 0) {
    echo json_encode(["success" => false, "message" => "Некоректна сума"]);
    exit();
}

// Отримуємо поточний баланс користувача
$stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["success" => false, "message" => "Користувача не знайдено"]);
    exit();
}

// Конвертуємо поточний баланс в копійки
$currentBalance = round(floatval($user['balance']) * 100);
$newBalance = $currentBalance;

if ($action === 'bet') {
    if ($currentBalance < $amount) {
        echo json_encode([
            "success" => false,
            "message" => "Недостатньо коштів",
            "debug" => [
                "amount" => $amount,
                "currentBalance" => $currentBalance
            ]
        ]);
        exit();
    }
    $newBalance = $currentBalance - $amount;

} elseif ($action === 'win') {
    $newBalance = $currentBalance + $amount;
}

// Записуємо новий баланс у базу (переводимо назад у гривні)
$newBalanceUAH = $newBalance / 100;
$stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
$stmt->bind_param("di", $newBalanceUAH, $userId);
$stmt->execute();

echo json_encode([
    "success" => true,
    "newBalance" => round($newBalanceUAH, 2)
]);
