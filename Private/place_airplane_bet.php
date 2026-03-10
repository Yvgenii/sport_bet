<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Необхідна авторизація']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$amount = (float)($data['amount'] ?? 0);

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Недійсна сума ставки']);
    exit;
}

$userId = $_SESSION['user']['id'];

// Перевірка балансу
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id FOR UPDATE");
$pdo->beginTransaction();
$stmt->execute(['id' => $userId]);
$balance = $stmt->fetchColumn();

if ($balance < $amount) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Недостатньо коштів']);
    exit;
}

// Списуємо ставку
$stmt = $pdo->prepare("UPDATE users SET balance = balance - :amount WHERE id = :id");
$stmt->execute(['amount' => $amount, 'id' => $userId]);
$pdo->commit();

// Оновлюємо сесію
$_SESSION['user']['balance'] -= $amount;
// Тимчасово зберігаємо ставку у сесії для історії
$_SESSION['airplane_last_bet'] = [
    'amount' => $amount,
    'multiplier' => null,
    'won' => false,
    'winnings' => 0
];

echo json_encode(['success' => true, 'message' => 'Ставку прийнято', 'newBalance' => $_SESSION['user']['balance']]);
