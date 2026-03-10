<?php
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT multiplier, winnings, won FROM airplane_history WHERE user_id = :id ORDER BY id DESC LIMIT 5");
$stmt->execute(['id' => $userId]);

$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($history);
