<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
  echo json_encode(["success" => false, "message" => "Not logged in"]);
  exit();
}

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?"); // ошибка мб 
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo json_encode(["success" => true, "balance" => floatval($user['balance'])]);
