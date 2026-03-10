<?php
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Потрібна авторизація']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user']['id'];

$amount = (float)($data['amount'] ?? 0);
$multiplier = (float)($data['multiplier'] ?? 0);
$winnings = (float)($data['winnings'] ?? 0);
$won = (bool)($data['won'] ?? false);

if ($won && $winnings > 0) {
    $stmt = $pdo->prepare("UPDATE users SET balance = balance + :winnings WHERE id = :id");
    $stmt->execute(['winnings' => $winnings, 'id' => $user_id]);
    $_SESSION['user']['balance'] += $winnings;
}

// Сохраняем в историю (создадим таблицу на следующем шаге)
$stmt = $pdo->prepare("INSERT INTO coin_history (user_id, bet_amount, multiplier, winnings, won) VALUES (:user_id, :amount, :multiplier, :winnings, :won)");
$stmt->execute([
    'user_id' => $user_id,
    'amount' => $amount,
    'multiplier' => $multiplier,
    'winnings' => $winnings,
    'won' => $won
]);

echo json_encode(['success' => true, 'newBalance' => $_SESSION['user']['balance']]);
