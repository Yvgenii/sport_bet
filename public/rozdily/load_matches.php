<?php
require_once __DIR__ . "/../../Private/db.php";

if (!isset($_GET['sport'])) {
    echo "<p>Не обраний вид спорту.</p>";
    exit;
}

$sport = $_GET['sport'];

$stmt = $pdo->prepare("SELECT * FROM matches WHERE LOWER(sport) = LOWER(:sport) ORDER BY date ASC");
$stmt->execute(['sport' => $sport]);
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$matches) {
    echo "<p>Немає доступних матчів для цього виду спорту.</p>";
    exit;
}

echo "<h2>Матчі - " . ucfirst(htmlspecialchars($sport)) . "</h2>";
echo "<div class='matches-list'>";

foreach ($matches as $match) {
    $teams = htmlspecialchars($match['teams']);
    $date = htmlspecialchars($match['date']);
    $odds = explode(':', $match['odds']);

    echo "<div class='match-card'>";
    echo "<div class='match-teams'><strong>{$teams}</strong></div>";
    echo "<div class='match-time'>🕒 {$date}</div>";

    echo "<div class='match-odds'>";
if (count($odds) === 3) {
    // Кнопки с модалкой
    echo "<button class='bet-btn' onclick=\"openBetModal('{$match['teams']}', '1', " . trim($odds[0]) . ")\">1 - " . trim($odds[0]) . "</button>";
    echo "<button class='bet-btn' onclick=\"openBetModal('{$match['teams']}', 'X', " . trim($odds[1]) . ")\">X - " . trim($odds[1]) . "</button>";
    echo "<button class='bet-btn' onclick=\"openBetModal('{$match['teams']}', '2', " . trim($odds[2]) . ")\">2 - " . trim($odds[2]) . "</button>";
} else {
    echo "<span class='odd'>Коефіцієнти не вказані</span>";
}
echo "</div>";

    echo "</div>";
}


echo "</div>";

echo "</div>";
