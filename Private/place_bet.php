<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../Private/db.php';

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован.']);
    exit;
}


// Получение данных из запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['event'], $data['type'], $data['odds'], $data['amount'])) {
    echo json_encode(['success' => false, 'message' => 'Некорректные данные.']);
    exit();
}

// Данные пользователя
$userId = (int) $_SESSION['user']['id'];
$eventName = trim($data['event']);
$betType = trim($data['type']);
$odds = (float) $data['odds'];
$amount = (float) $data['amount'];

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Сума ставки повинна бути більшою за нуль.']);
    exit();
}

try {
    // Начало транзакции
    $pdo->beginTransaction();

    // Проверяем баланс в БД
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $userId]);
    $userBalance = (float) $stmt->fetchColumn();

    if ($amount > $userBalance) {
        echo json_encode(['success' => false, 'message' => 'Недостатньо коштів на балансі.']);
        exit();
    }

    // Обновление баланса пользователя
    $stmt = $pdo->prepare("UPDATE users SET balance = balance - :amount WHERE id = :id");
    $stmt->execute(['amount' => $amount, 'id' => $userId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Баланс не обновился, возможно, недостаточно средств.");
    }

    // Вставка ставки
    $potentialWin = round($amount * $odds, 2);
    $stmt = $pdo->prepare("INSERT INTO bets (user_id, event_name, bet_type, amount, potential_win, created_at) 
                           VALUES (:user_id, :event_name, :bet_type, :amount, :potential_win, NOW())");
    $stmt->execute([
        'user_id' => $userId,
        'event_name' => $eventName,
        'bet_type' => $betType,
        'amount' => $amount,
        'potential_win' => $potentialWin,
    ]);

    // Обновляем баланс в сессии
    $_SESSION['user']['balance'] -= $amount;

    // Подтверждаем транзакцию
    $pdo->commit();

    echo json_encode(['success' => true, 'newBalance' => $_SESSION['user']['balance'], 'potentialWin' => $potentialWin]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Ошибка обработки ставки: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка при обработке ставки. Попробуйте позже.']);
}
exit();
