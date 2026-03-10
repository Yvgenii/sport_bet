<?php
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Потрібна авторизація']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$amount = (float)($data['amount'] ?? 0);

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Недійсна сума ставки']);
    exit;
}

$userId = $_SESSION['user']['id'];

// Проверка баланса
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id FOR UPDATE");
$pdo->beginTransaction();
$stmt->execute(['id' => $userId]);
$balance = $stmt->fetchColumn();

if ($balance < $amount) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Недостатньо коштів']);
    exit;
}

// Списываем ставку
$stmt = $pdo->prepare("UPDATE users SET balance = balance - :amount WHERE id = :id");
$stmt->execute(['amount' => $amount, 'id' => $userId]);
$pdo->commit();

$_SESSION['user']['balance'] -= $amount;

echo json_encode(['success' => true, 'newBalance' => $_SESSION['user']['balance']]);
