<?php
require_once __DIR__ . '/db.php';

$apiKey = 'd99402999c4286b1f37b28f33b78dc41';
$region = 'us';
$markets = 'h2h';

$sportMap = [
    'soccer_epl' => 'Футбол',
    'basketball_euroleague' => 'Баскетбол',
    'baseball_mlb' => 'Бейсбол',
    'icehockey_nhl' => 'Хокей',
    'golf_masters' => 'Гольф',
    'tennis_wta' => 'Теніс',
    'rugby_union' => 'Регбі'
];

foreach ($sportMap as $sportKey => $sportName) {
    echo "<h3>🔄 Завантаження матчів для: $sportName ($sportKey)</h3>";

    $oddsUrl = "https://api.the-odds-api.com/v4/sports/$sportKey/odds/?apiKey=$apiKey&regions=$region&markets=$markets";
    $matches = json_decode(@file_get_contents($oddsUrl), true);

    if (!$matches || isset($matches['message'])) {
        echo "<p style='color:orange;'>⚠️ Немає даних для $sportName</p>";
        continue;
    }

    foreach ($matches as $match) {
        $teams = $match['home_team'] . ' vs ' . $match['away_team'];
        $date = $match['commence_time'];

        // По умолчанию
        $odds = '1 : ? : ?';

        if (!empty($match['bookmakers'][0]['markets'][0]['outcomes'])) {
            $outcomes = $match['bookmakers'][0]['markets'][0]['outcomes'];
            if (count($outcomes) >= 2) {
                $price1 = $outcomes[0]['price'];
                $price2 = $outcomes[1]['price'];
                $odds = "1 : $price1 : $price2";
            }
        }

        // Проверка на дубликат
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM matches WHERE teams = ? AND date = ?");
        $stmt->execute([$teams, $date]);

        if ($stmt->fetchColumn() == 0) {
            $insert = $pdo->prepare("INSERT INTO matches (sport, teams, date, odds) VALUES (?, ?, ?, ?)");
            $insert->execute([$sportName, $teams, $date, $odds]);
            echo "<p style='color:green;'>✅ Додано матч: $teams ($sportName)</p>";
        } else {
            echo "<p style='color:gray;'>ℹ️ Вже існує: $teams</p>";
        }
    }

    echo "<hr>";
}

echo "<h2 style='color:green;'>🏁 Завантаження завершено!</h2>";
?>