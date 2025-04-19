<?php
// Пример массива событий (можно заменить реальными данными из БД)
$events = [
    ['category' => 'Футбол', 'teams' => 'Ліверпуль vs Манчестер Сіті', 'time' => '20:00 Сьогодні'],
    ['category' => 'Футбол', 'teams' => 'Шахтер vs Оболонь', 'time' => '10:00 Завтра'],
    ['category' => 'Баскетбол', 'teams' => 'КТУ НАУ vs США', 'time' => '22:00 Сьогодні'],
    ['category' => 'Кіберспорт', 'teams' => 'Team Spirit vs NAVI', 'time' => '15:00 Сьогодні'],
    ['category' => 'Кіберспорт', 'teams' => 'FaZe Clan vs Astralis', 'time' => '19:00 Сьогодні'],
    ['category' => 'Волейбол', 'teams' => 'Греція vs Італія', 'time' => '20:00 Сьогодні'],
    ['category' => 'Футбол', 'teams' => 'Динамо Київ vs Заря', 'time' => '15:00 Сьогодні'],
    ['category' => 'Баскетбол', 'teams' => 'Лейкерс vs Бостон', 'time' => '18:00 Сьогодні'],
    ['category' => 'Теніс', 'teams' => 'Новак Джокович vs Рафаэль Надаль', 'time' => '16:00 Сьогодні'],
    ['category' => 'Волейбол', 'teams' => 'Україна vs США', 'time' => '17:00 Сьогодні'],
    ['category' => 'Баскетбол', 'teams' => 'Поколение чудес vs Сейрин', 'time' => '18:00 Сьогодні'],
    ['category' => 'Кіберспорт', 'teams' => 'Clan Piru vs Astralis', 'time' => '10:00 Завтра'],
];

$perPage = 6;
$totalPages = ceil(count($events) / $perPage);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($totalPages, $page));

$start = ($page - 1) * $perPage;
$eventsOnPage = array_slice($events, $start, $perPage);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Топ-події</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="light-theme">
    <section class="events-section">
        <h2>Топ-події</h2>
        <div class="events-grid">
            <?php foreach ($eventsOnPage as $event): ?>
                <div class="event-card">
                    <div class="event-header">
                        <span class="team"><?= htmlspecialchars($event['category']) ?></span>
                        <span class="time"><?= htmlspecialchars($event['time']) ?></span>
                    </div>
                    <div class="teams">
                        <span class="team"><?= htmlspecialchars($event['teams']) ?></span>
                    </div>
                    <div class="odds">
                        <button class="odd">1.8</button>
                        <button class="odd">2.3</button>
                        <button class="odd">3.0</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination" style="margin-top: 30px;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </section>
</body>
</html>
