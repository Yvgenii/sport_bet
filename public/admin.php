<?php
session_start();
require_once __DIR__ . '/../Private/db.php';

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php"); // Если не администратор, перенаправляем
    exit();
}

// Обработка действий администратора
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_match') {

            $sport = htmlspecialchars($_POST['sport']);
            $teams = htmlspecialchars($_POST['teams']);
            $date = htmlspecialchars($_POST['date']);
            $odds = htmlspecialchars($_POST['odds']);

            $stmt = $pdo->prepare("INSERT INTO matches (sport, teams, date, odds) VALUES (:sport, :teams, :date, :odds)");
            $stmt->execute(['sport' => $sport, 'teams' => $teams, 'date' => $date, 'odds' => $odds]);

            $message = "Матч успішно доданий!";
        } elseif ($_POST['action'] === 'delete_bet') {
            $betId = intval($_POST['bet_id']);

            $stmt = $pdo->prepare("DELETE FROM bets WHERE id = :id");
            $stmt->execute(['id' => $betId]);

            $message = "Ставку успішно видалено!";
        } elseif ($_POST['action'] === 'approve_bet') {
            $betId = intval($_POST['bet_id']);
            $userId = intval($_POST['user_id']);
            $amount = floatval($_POST['amount']);

            try {
                $stmt = $pdo->prepare("UPDATE users SET balance = balance + :amount WHERE id = :id");
                $stmt->execute(['amount' => $amount, 'id' => $userId]);

                $stmt = $pdo->prepare("DELETE FROM bets WHERE id = :id");
                $stmt->execute(['id' => $betId]);

                $message = "Виграш підтверджений, гроші переведені!";
            } catch (PDOException $e) {
                $message = "Помилка: " . $e->getMessage();
            }
        }
    }
}

// Получение всех матчей
$matches = $pdo->query("SELECT * FROM matches ORDER BY date DESC")->fetchAll(PDO::FETCH_ASSOC);

// Получение всех ставок
$bets = $pdo->query("SELECT b.*, u.username FROM bets b JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Адмін-панель</title>
    <div class="navigation">
        <a href="index.php" class="btn">Повернутись на сайт</a>
    </div>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Адмін-панель</h1>

    <!-- Вывод сообщений -->
    <?php if (isset($message)) echo "<p style='color: green;'>$message</p>"; ?>

    <!-- Добавление матча -->
    <div class="section">
        <h2>Додавання матчу</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_match">
            <div class="form-group">
                <label for="sport">Вид спорту</label>
                <input type="text" id="sport" name="sport" required>
            </div>
            <div class="form-group">
                <label for="teams">Команди</label>
                <input type="text" id="teams" name="teams" required>
            </div>
            <div class="form-group">
                <label for="date">Дата</label>
                <input type="datetime-local" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="odds">Коефіцієнти</label>
                <input type="text" id="odds" name="odds" required>
            </div>
            <button type="submit" class="btn">Додати матч</button>
        </form>
    </div>

    <!-- Список ставок -->
    <div class="section">
        <h2>Ставки користувачів</h2>
        <table>
            <thead>
                <tr>
                    <th>Користувач</th>
                    <th>Подія</th>
                    <th>Тип</th>
                    <th>Сума</th>
                    <th>Потенційний виграш</th>
                    <th>Дата</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bets as $bet): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($bet['username']); ?></td>
                        <td><?php echo htmlspecialchars($bet['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($bet['bet_type']); ?></td>
                        <td><?php echo htmlspecialchars($bet['amount']); ?> ₴</td>
                        <td><?php echo htmlspecialchars($bet['potential_win']); ?> ₴</td>
                        <td><?php echo date('d.m.Y H:i', strtotime($bet['created_at'])); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete_bet">
                                <input type="hidden" name="bet_id" value="<?php echo $bet['id']; ?>">
                                <button type="submit" class="btn btn-danger">Видалити</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="approve_bet">
                                <input type="hidden" name="bet_id" value="<?php echo $bet['id']; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $bet['user_id']; ?>">
                                <input type="hidden" name="amount" value="<?php echo $bet['potential_win']; ?>">
                                <button type="submit" class="btn">Підтвердити</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
