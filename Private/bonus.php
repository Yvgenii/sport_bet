<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../Private/db.php';

// Проверка авторизации
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован.']);
    exit();
}

$userId = (int)$_SESSION['user']['id'];
$bonusAmount = 5000;

try {
    $pdo->beginTransaction();

    // Проверяем, получал ли пользователь бонус
    $stmt = $pdo->prepare("SELECT has_bonus FROM users WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $userId]);
    $hasBonus = $stmt->fetchColumn();

    if ($hasBonus) {
        throw new Exception("Вы уже получили бонус ранее.");
    }

    // Обновляем баланс и отмечаем, что бонус выдан
    $stmt = $pdo->prepare("UPDATE users SET balance = balance + :bonus, has_bonus = 1 WHERE id = :id");
    $stmt->execute(['bonus' => $bonusAmount, 'id' => $userId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Ошибка обновления баланса пользователя ID: $userId.");
    }

    // Подтверждаем транзакцию
    $pdo->commit();

    // Обновляем данные в сессии
    $_SESSION['user']['balance'] += $bonusAmount;
    $_SESSION['user']['has_bonus'] = 1;

    echo json_encode(['success' => true, 'newBalance' => $_SESSION['user']['balance']]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Ошибка бонуса: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

exit();
