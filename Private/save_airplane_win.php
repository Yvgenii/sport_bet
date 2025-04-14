<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизований користувач']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$winnings = (float)($data['winnings'] ?? 0);
$userId = $_SESSION['user']['id'];

if ($winnings <= 0) {
    echo json_encode(['success' => false, 'message' => 'Некоректна сума виграшу']);
    exit;
}

// Зараховуємо виграш
$stmt = $pdo->prepare("UPDATE users SET balance = balance + :win WHERE id = :id");
$stmt->execute(['win' => $winnings, 'id' => $userId]);

// Оновлюємо баланс у сесії
$_SESSION['user']['balance'] += $winnings;

echo json_encode([
    'success' => true,
    'message' => 'Виграш зараховано',
    'newBalance' => $_SESSION['user']['balance']
]);
