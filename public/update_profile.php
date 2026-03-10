<?php
session_start();
require_once __DIR__ . '/../Private/db.php';

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Користувач не авторизований.']);
    exit();
}

$userId = (int)$_SESSION['user']['id'];
$username = trim(htmlspecialchars($_POST['username']));
$phone = trim(htmlspecialchars($_POST['phone']));

try {
    $stmt = $pdo->prepare("UPDATE users SET username = :username, phone = :phone WHERE id = :id");
    $stmt->execute([
        'username' => $username,
        'phone' => $phone,
        'id' => $userId
    ]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['user']['username'] = $username;
        $_SESSION['user']['phone'] = $phone;

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Зміни не були внесені.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Помилка бази даних.']);
}
exit();
