<?php
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

// Проверка авторизации
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Необхідна авторизація']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $_SESSION['user']['id'];
$bet = (float)$data['amount'];
$multiplier = (float)$data['multiplier'];
$won = (bool)$data['won'];
$winnings = (float)$data['winnings'];

// Если выиграл — зачислить на баланс
if ($won && $winnings > 0) {
    $stmt = $pdo->prepare("UPDATE users SET balance = balance + :winnings WHERE id = :id");
    $stmt->execute(['winnings' => $winnings, 'id' => $user_id]);
    $_SESSION['user']['balance'] += $winnings;
}

// Сохраняем в историю
$stmt = $pdo->prepare("
    INSERT INTO airplane_history (user_id, bet_amount, multiplier, winnings, won)
    VALUES (:user_id, :bet_amount, :multiplier, :winnings, :won)
");

$stmt->execute([
    'user_id' => $user_id,
    'bet_amount' => $bet,
    'multiplier' => $multiplier,
    'winnings' => $winnings,
    'won' => $won
]);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'DB error']);
    exit;
}

echo json_encode([
    'success' => true,
    'newBalance' => $_SESSION['user']['balance']
]);

