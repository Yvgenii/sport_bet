<?php
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
  echo json_encode(['success' => false]);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$choice = $data['choice'] ?? '';
$amount = (float)($data['amount'] ?? 0);
$userId = $_SESSION['user']['id'];

$result = rand(0, 1) ? 'heads' : 'tails';
$win = $choice === $result;

$winnings = 0;
if ($win) {
  $winnings = $amount * 2;
  $stmt = $pdo->prepare("UPDATE users SET balance = balance + :win WHERE id = :id");
  $stmt->execute(['win' => $winnings, 'id' => $userId]);
  $_SESSION['user']['balance'] += $winnings;
}

// Можно сохранить в таблицу history_coin, если хочешь

echo json_encode([
  'win' => $win,
  'result' => $result === 'heads' ? 'Орел' : 'Решка',
  'winnings' => $winnings
]);
